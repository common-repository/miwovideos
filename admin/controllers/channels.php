<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die ;

class MiwovideosControllerChannels extends MiwoVideosController {
	
	public function __construct($config = array()) {
		parent::__construct('channels');
	}

    # Default
    public function defaultChannel() {
        # Check token
        MRequest::checkToken() or mexit('Invalid Token');

        # Action
        MiwoVideos::get('channels')->updateDefaultChannel(1);

        # Return
        parent::route();
    }

    # Not Default
    public function notDefaultChannel() {
        # Check token
        MRequest::checkToken() or mexit('Invalid Token');

        # Action
        MiwoVideos::get('channels')->updateDefaultChannel(0);

        # Return
        parent::route();
    }

    # Publish
    public function publish() {
        # Check token
        MRequest::checkToken() or mexit('Invalid Token');

        # Action
        self::updateField($this->_table, 'published', 1, $this->_model);

        $cid = MRequest::getVar('cid', array(), 'post');
        MArrayHelper::toInteger($cid);

        foreach ($cid as $id) {
            $video_ids = MiwoVideos::get('channels')->getVideos($id);
            if (!empty($video_ids)) {
                $this->_model->publish_rel($video_ids);
            }
        }

        $this->_model->publish($cid);

        # Return
        self::route();
    }

    # Unpublish
    public function unpublish() {
        # Check token
        MRequest::checkToken() or mexit('Invalid Token');

        # Action
        self::updateField($this->_table, 'published', 0, $this->_model);

        $cid = MRequest::getVar('cid', array(), 'post');
        MArrayHelper::toInteger($cid);

        foreach ($cid as $id) {
            $video_ids = MiwoVideos::get('channels')->getVideos($id);
            if (!empty($video_ids)) {
                $this->_model->unpublish_rel($video_ids);
            }
        }

        $this->_model->unpublish($cid);

        # Return
        self::route();
    }


    public function save() {
        if (MiwoVideos::get('channels')->getChannels(MFactory::getUser()->get('id')) > 0) {
			$post = MRequest::get('post', MREQUEST_ALLOWRAW);
			$msg = MText::sprintf('MLIB_X_PRO_MEMBERS', 'Multi-Channel') . ". ";
			$msg .= MText::sprintf('MLIB_PRO_MEMBERS_DESC', 'http://miwisoft.com/wordpress-plugins/miwovideos-share-your-videos#pricing', 'MiwoVideos');
			self::route($msg, $post);
			return MError::raiseWarning(500, MText::_('COM_MIWOVIDEOS_COMMON_RECORD_SAVED_NOT'));
		}
		parent::save();
	}
    public function delete() {
        # Check token
        MRequest::checkToken() or mexit('Invalid Token');

        $cid = MRequest::getVar('cid', array(), 'post');
        MArrayHelper::toInteger($cid);

        foreach ($cid as $id) {
            $video_ids = MiwoVideos::get('channels')->getVideos($id);

            # Action
            if (!empty($video_ids)) {
                foreach ($video_ids as $video_id) {
                    if (MFolder::exists(MIWOVIDEOS_UPLOAD_DIR.'/videos/'.$video_id)) {
                        MFolder::delete(MIWOVIDEOS_UPLOAD_DIR.'/videos/'.$video_id);
                    }
                    if (MFolder::exists(MIWOVIDEOS_UPLOAD_DIR.'/images/videos/'.$video_id)) {
                        MFolder::delete(MIWOVIDEOS_UPLOAD_DIR.'/images/videos/'.$video_id);
                    }
                }

                if (MFolder::exists(MIWOVIDEOS_UPLOAD_DIR.'/images/channels/'.$id)) {
                    MFolder::delete(MIWOVIDEOS_UPLOAD_DIR.'/images/channels/'.$id);
                }

                $del_rel_video_row = $this->_model->delete_rel($video_ids);

            } else {
                $del_rel_video_row = true;
            }
        }

        $del_row = $this->deleteRecord($this->_table, $this->_model);
        $del_rel_row = $this->_model->delete($cid);

        if (!$del_rel_row and !$del_row and !$del_rel_video_row) {
            $msg = MText::_('COM_MIWOVIDEOS_COMMON_RECORDS_DELETED_NOT');
        } else {
            $msg = MText::_('COM_MIWOVIDEOS_COMMON_RECORDS_DELETED');
        }

        # Return
        $this->setRedirect('index.php?option='.$this->_option.'&view='.$this->_context, $msg);

        return $msg;
    }
}