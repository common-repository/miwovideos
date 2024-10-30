<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die ;


class MiwovideosControllerVideos extends MiwoVideosController {

	public function __construct($config = array())	{
		parent::__construct('videos');
	}

    public function edit() {
        MRequest::setVar('hidemainmenu', 1);

        $view = $this->getView('Videos', 'edit');
	    $videos_model = $this->getModel('videos');
	    $view->setModel($videos_model, true);
	    $processes_model = $this->getModel('processes');
	    $view->setModel($processes_model);
	    if (MRequest::getWord('layout') == 'preview') {
		    $view->displayPreview('edit');
	    }
	    else {
		    $view->display('edit');
	    }
    }

	public function save() {
		$post       = MRequest::get('post', MREQUEST_ALLOWRAW);
        $cid = MRequest::getVar('cid', array(), 'post');

        if(!empty($cid[0])){
            $post['id'] = $cid[0];
        }

        $thumb_size = MiwoVideos::get('utility')->getThumbSize($this->config->get('thumb_size'));

        # Thumb Image
        if (isset($_FILES['thumb_image']['name'])) {
            $fileExt = strtolower(MFile::getExt($_FILES['thumb_image']['name']));
            $supportedTypes = array('jpg', 'png', 'gif');
            if (in_array($fileExt, $supportedTypes)) {
                $fileName = hash('haval256,5', MString::strtolower($_FILES['thumb_image']['name'])) . '.' . $fileExt;
                $imagePath = MIWOVIDEOS_UPLOAD_DIR.'/images/'.$this->_context.'/'.$post['id'].'/orig/'.$fileName;
                $thumbPath = MIWOVIDEOS_UPLOAD_DIR.'/images/'.$this->_context.'/'.$post['id'].'/'.$thumb_size.'/'.$fileName;
                MFile::upload($_FILES['thumb_image']['tmp_name'], $imagePath);
                MFolder::create(MIWOVIDEOS_UPLOAD_DIR.'/images/'.$this->_context.'/'.$post['id'].'/'.$thumb_size.'/');
                MiwoVideos::get('utility')->resizeImage($imagePath, $thumbPath, $thumb_size, $thumb_size, 95);
                $post['thumb'] = $fileName;
            }
        }

        $table = ucfirst($this->_component).ucfirst($this->_context);
        $row = MiwoVideos::getTable($table);
        $row->load($post['id']);

        if (isset($post['del_thumb']) and $row->thumb) {
            if (MFile::exists(MIWOVIDEOS_UPLOAD_DIR.'/images/'.$this->_context.'/'.$post['id'].'/orig/'.$row->thumb)) {
                MFile::delete(MIWOVIDEOS_UPLOAD_DIR.'/images/'.$this->_context.'/'.$post['id'].'/orig/'.$row->thumb);
                //MFile::delete(MIWOVIDEOS_UPLOAD_DIR.'/images/'.$this->_context.'/'.$post['id'].'/orig');
            }

            if (MFile::exists(MIWOVIDEOS_UPLOAD_DIR.'/images/'.$this->_context.'/'.$post['id'].'/'.$thumb_size.'/'.$row->thumb)) {
                MFile::delete(MIWOVIDEOS_UPLOAD_DIR.'/images/'.$this->_context.'/'.$post['id'].'/'.$thumb_size.'/'.$row->thumb);
            }

            $post['thumb'] = '';
        }


        if (!$post['channel_id']) {
            $post['channel_id'] = MiwoVideos::get('channels')->getDefaultChannel()->id;
        }

        $ret = $this->_model->store($post);

        if($ret){
            $msg = MText::_('COM_MIWOVIDEOS_VIDEO_SAVED');
        }
        else {
            $msg = MText::_('COM_MIWOVIDEOS_VIDEO_SAVE_ERROR');
        }

		MiwoVideos::get('utility')->trigger('onFinderAfterSave', array('com_miwovideos.videos', $post['id'], null), 'finder');

		parent::route($msg, $post);

        return $msg;
	}

    public function copy() {
        # Check token
        MRequest::checkToken() or mexit('Invalid Token');

        $cid = MRequest::getVar('cid', array(), 'post');
        
        foreach ($cid as $id) {
        	$this->_model->copy($id);
        }
        
        $msg = MText::_('COM_MIWOVIDEOS_RECORD_COPIED');

        $this->setRedirect('index.php?option='.$this->_option.'&view='.$this->_context, $msg);

        return $msg;
    }
    
	public function delete() {
    	# Check token
		MRequest::checkToken() or mexit('Invalid Token');
		
		$cid = MRequest::getVar('cid', array(), 'post');
        MArrayHelper::toInteger($cid);

		# Action
        foreach ($cid as $id) {
            if (MFolder::exists(MIWOVIDEOS_UPLOAD_DIR.'/videos/'.$id)) {
                MFolder::delete(MIWOVIDEOS_UPLOAD_DIR.'/videos/'.$id);
            }
            if (MFolder::exists(MIWOVIDEOS_UPLOAD_DIR.'/images/videos/'.$id)) {
                MFolder::delete(MIWOVIDEOS_UPLOAD_DIR.'/images/videos/'.$id);

            }

	        MiwoVideos::get('utility')->trigger('onFinderAfterDelete', array('com_miwovideos.videos', $id), 'finder');
        }

        $del_row = $this->deleteRecord($this->_table, $this->_model);
        $del_rel_row = $this->_model->delete($cid);

		if (!$del_row and !$del_rel_row) {
            $msg = MText::_('COM_MIWOVIDEOS_COMMON_RECORDS_DELETED_NOT');
		} else {
			$msg = MText::_('COM_MIWOVIDEOS_COMMON_RECORDS_DELETED');
		}

		$this->setRedirect('index.php?option='.$this->_option.'&view='.$this->_context, $msg);

        return $msg;
    }

    public function autoComplete(){
        $query = MRequest::getVar('query');
        $videos = json_encode($this->_model->autoComplete($query));
        echo $videos;
        exit();
    }

    public function createAutoFieldHtml(){
        $fieldid = MRequest::getInt('fieldid');
        $html = MiwoVideos::get('fields')->createAutoFieldHtml($fieldid);
        echo $html;
        exit();
    }

    # Feature
    public function feature() {
        # Check token
        MRequest::checkToken() or mexit('Invalid Token');

        # Action
        self::updateField($this->_table, 'featured', 1, $this->_model);

	    # Trigger
	    $this->trigger();

        # Return
        self::route();
    }

    # Unfeature
    public function unfeature() {
        # Check token
        MRequest::checkToken() or mexit('Invalid Token');

        # Action
        self::updateField($this->_table, 'featured', 0, $this->_model);

	    # Trigger
	    $this->trigger();

        # Return
        self::route();
    }

	public function publish() {
		$this->trigger();
		parent::publish();
	}

	public function unpublish() {
		$this->trigger();
		parent::unpublish();
	}

	protected function trigger() {
		$cid = MRequest::getVar('cid', array(), 'post');
		MiwoVideos::get('utility')->trigger('onFinderAfterSave', array('com_miwovideos.videos', $cid[0], null), 'finder');
	}
}