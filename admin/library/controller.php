<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die('Restricted Access');

mimport('framework.filesystem.file');
mimport('framework.filesystem.folder');
mimport('framework.application.component.controller');

if (!class_exists('MiwisoftController')) {
	if (interface_exists('MController')) {
		abstract class MiwisoftController extends MControllerLegacy {}
	}
	else {
		class MiwisoftController extends MController {}
	}
}

class MiwovideosController extends MiwisoftController {
	
	public $_mainframe;
	public $_option;
	public $_component;
	public $_context;
	public $_table;
	public $_model;
	
    public function __construct($context = '', $table = '') {
		parent::__construct();

        $this->_view = MiwoVideos::getInput()->getCmd('view', '');
        $this->_task = MiwoVideos::getInput()->getCmd('task', '');
        $this->_dashboard = MiwoVideos::getInput()->getInt('dashboard', 0);

		if (empty($context) and !empty($this->_view)) {
			$context = $this->_view;
		}
		
		$this->_mainframe = MFactory::getApplication();
		if ($this->_mainframe->isAdmin()) {
			$this->_option = MiwoVideos::get('utility')->findOption();
		}
		else {
			$this->_option = MRequest::getCmd('option');
		}
		
		$this->_component = str_replace('com_', '', $this->_option);
		
		$this->_context = $context;
		
		$this->_table = $table;
		if ($this->_table == '') {
			$this->_table = $this->_context;
		}

        if (!MiwoVideos::isDashboard()) {
		    $this->_model = $this->getModel($context);
        }

        $this->_document = MFactory::getDocument();
		$this->config = MiwoVideos::getConfig();
		
		# Register tasks
		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');
		$this->registerTask('save2new', 'save');
		$this->registerTask('remove', 'delete');
	    $this->registerTask('back', 'back');
	}
	
	public function display($cachable = false, $urlparams = false) {
        $layout = MRequest::getCmd('layout');

        $function = 'display'.ucfirst($layout);

        $view = $this->getView(ucfirst($this->_context), 'html');

        if (!empty($this->_model)) {
            $view->setModel($this->_model, true);
        }

        if (!empty($layout)) {
            $view->setLayout($layout);
        }

        $view->$function();
	}

	public function edit() {
		MRequest::setVar('hidemainmenu', 1);
		
		$view = $this->getView(ucfirst($this->_context), 'edit');
		$view->setModel($this->_model, true);
		$view->display('edit');
	}

	public function getModel($name = '', $prefix = '', $config = array()) {
		if (MiwoVideos::isDashboard()) {
			$path = MPATH_MIWOVIDEOS_ADMIN.'/models/'.$name.'.php';

			if (file_exists($path)) {
				require_once $path;
			}
			else {
				return null;
			}

			$model_name = 'MiwovideosModel'.ucfirst($name);
			return new $model_name();
		}

		return parent::getModel($name, $prefix, $config);
	}

    public function cancel() {
        $this->route();
    }

    public function getDashboardView() {
        $type = 'html';

        $tasks = array('add', 'edit', 'apply', 'save2new');
        if (in_array($this->_task, $tasks)) {
            $type = 'edit';
        }

        $path = MPATH_MIWOVIDEOS_ADMIN.'/views/'.$this->_context.'/view.'.$type.'.php';

        if (file_exists($path)) {
            require_once $path;
        }
        else {
            return null;
        }

        $layout = MiwoVideos::getInput()->getCmd('layout', 'default');

        $view_name = 'MiwovideosView'.ucfirst($this->_context);
        $model_name = 'MiwovideosModel'.ucfirst($this->_context);

        $options['name'] = $this->_context;
        $options['layout'] = $layout;
        $options['base_path'] = MPATH_MIWOVIDEOS_ADMIN;

        $view = new $view_name($options);

        $model_file = MPATH_MIWOVIDEOS_ADMIN.'/models/'.$this->_context.'.php';
        if (file_exists($model_file)) {
            require_once($model_file);

            $this->_model = new $model_name();

            $view->setModel($this->_model, true);
        }

        if (MiwoVideos::is30()) {
            MHtml::_('formbehavior.chosen', 'select');
        }

        $this->_document->addStyleSheet(MURL_MIWOVIDEOS.'/site/assets/css/dashboard.css');
        $this->_document->addStyleSheet(MURL_MIWOVIDEOS.'/admin/assets/css/j3.css');

        return $view;
    }
	
	public function route($msg = "", $post = array()) {
        if (MiwoVideos::get('utility')->isDashboard()) {
            $this->routeDashboard($msg, $post);
            return;
        }

        switch ($this->_task) {
            case 'apply':
            case 'resetstats':
                $link = 'index.php?option='.$this->_option.'&view='.$this->_context.'&task=edit&cid[]='.$post['id'];
                break;
            case 'save2new':
                $link = 'index.php?option='.$this->_option.'&view='.$this->_context.'&task=add';
                break;
            default:
                $link = 'index.php?option='.$this->_option.'&view='.$this->_context;
                break;
        }

        parent::setRedirect($link, $msg);
	}

    public function routeDashboard($msg = "", $post = array()) {
        if (empty($post)) {
            $post = MRequest::get('post', MREQUEST_ALLOWRAW);
            $cid = MRequest::getVar('cid', array(), 'post');

            if (!empty($cid[0])){
                $post['id'] = $cid[0];
            }

            $post['Itemid'] = MiwoVideos::getInput()->getInt('Itemid', 0);
        }

        switch ($this->_task) {
            case 'apply':
            case 'resetstats':
                $link = MRoute::_('index.php?option='.$this->_option.'&view='.$this->_context.'&task=edit&cid[]='.$post['id'].'&dashboard=1&Itemid='.$post['Itemid']);
                break;
            case 'save2new':
                $link = MRoute::_('index.php?option='.$this->_option.'&view='.$this->_context.'&task=add&dashboard=1&Itemid='.$post['Itemid']);
                break;
	        case 'back':
		        $link = MRoute::_('index.php?option='.$this->_option.'&view=dashboard&dashboard=1&Itemid='.$post['Itemid']);
		        break;
            default:
                $link = MRoute::_('index.php?option='.$this->_option.'&view='.$this->_context.'&dashboard=1&Itemid='.$post['Itemid']);
                break;
        }

        $link = str_replace('&amp;', '&', $link);

        parent::setRedirect($link, $msg);
    }
	
	# Delete
	public function delete() {
		# Check token
		MRequest::checkToken() or mexit('Invalid Token');

        $cid = MRequest::getVar('cid', array(), 'post');
        MArrayHelper::toInteger($cid);

        // Is this really a folder?
        foreach ($cid as $id) {
			$path_folder = MIWOVIDEOS_UPLOAD_DIR.'/images/'.$this->_context.'/'.$id;
            if(MFolder::exists($path_folder)) {
                MFolder::delete($path_folder);
            }
        }
		
		# Action
		if (!self::deleteRecord($this->_table, $this->_model)) {
			$msg = MText::_('COM_MIWOVIDEOS_COMMON_RECORDS_DELETED_NOT');
		} else {
			$msg = MText::_('COM_MIWOVIDEOS_COMMON_RECORDS_DELETED');
		}
		
		# Return
		self::route($msg);

        return $msg;
	}
	
	public function deleteRecord($table, $model, $where = true) {
		if ($where === true) {
			$where = self::_getWhere($model);
		}
		
		if (!MiwoDB::query("DELETE FROM #__{$this->_component}_{$table}{$where}")) {
			return false;
		}

		return true;
    }
	
	public function _getWhere($model, $prefix = "") {
        $where = '';
		
        $sel = MRequest::getVar('selection', 'selected', 'post');
        if ($sel == 'selected') {
            $where = self::_buildSelectedWhere($prefix);
        } elseif ($sel == 'filtered') {
            $where = $model->_buildViewWhere($prefix);
        }
        
        return $where;
    }
	
	# Get the id's of selected records
	public function _buildSelectedWhere($prefix = "") {
		$cid = MRequest::getVar('cid', array(), 'post', 'array');
		MArrayHelper::toInteger($cid);
		
		$where = '';
		if(count($cid) > 0){
			$where = " WHERE {$prefix}id IN (".implode(',',$cid).")";
		}

		return $where;
	}
	
	# Publish
	public function publish() {
		# Check token
		MRequest::checkToken() or mexit('Invalid Token');
		
		# Action
		self::updateField($this->_table, 'published', 1, $this->_model);
		
		# Return
		self::route();
	}
	
	# Unpublish
	public function unpublish() {
		# Check token
		MRequest::checkToken() or mexit('Invalid Token');
		
		# Action
		self::updateField($this->_table, 'published', 0, $this->_model);
		
		# Return
		self::route();
	}

    # Feature
    public function feature() {
        # Check token
        MRequest::checkToken() or mexit('Invalid Token');

        # Action
        self::updateField($this->_table, 'featured', 1, $this->_model);

        # Return
        self::route();
    }

    # Unfeature
    public function unfeature() {
        # Check token
        MRequest::checkToken() or mexit('Invalid Token');

        # Action
        self::updateField($this->_table, 'featured', 0, $this->_model);

        # Return
        self::route();
    }

    public function resetStats() {
        $id = MiwoVideos::getInput()->getInt('id', 0);
        $type = MiwoVideos::getInput()->getWord('type', '');

        self::updateField($this->_table, $type, 0, $this->_model);

        $post = array();
        $post['id'] = $id;

        self::route(MText::_('COM_MIWOVIDEOS_RESET_STATS'), $post);
    }
	
	public function checkLikesDislikes() {
        $item_id    = MiwoVideos::getInput()->getInt('item_id');
        $item_type  = MiwoVideos::getInput()->getWord('item_type');
        $user       = MFactory::getUser();
        $json       = array();
        if($user->id != 0) {
            $ret = $this->_model->checkLikesDislikes($item_id, $item_type);
            if(is_null($ret)) {
                $json['type'] = -1;
            } else {
                $json['type'] = $ret;
            }
            $json['success'] = 1;
        } else {
            $json['redirect'] = MiwoVideos::get('utility')->redirectWithReturn();
        }
        echo json_encode($json);
        exit();
    }

    public function likeDislikeItem() {
        $type       = MiwoVideos::getInput()->getInt('type', '');
        $item_id    = MiwoVideos::getInput()->getInt('item_id');
        $item_type  = MiwoVideos::getInput()->getWord('item_type', '');
        $change     = MiwoVideos::getInput()->getInt('change', '');
        MRequest::setVar('selection', 'filtered', 'post');
        $user       = MFactory::getUser();
        $json       = array();
        if($user->id != 0) {
            switch ($type) {
                case 1: # Like
                    switch ($change) {
                        case 1: # Change Dislike to Like
                            self::updateField('likes', 'type', $type, $this->_model);
                            if(self::decreaseField($item_type, 'dislikes', 1, $item_id) && self::increaseField($item_type, 'likes', 1, $item_id)) $ret = true;
                            MiwoVideos::get('utility')->trigger('onMiwovideosAfterSaveItem', array($item_id, $item_type, 'liked'));
                            break;
                        case -1: # Cancel Like
                            $this->_model->unlikeItem($item_id, $item_type, 1);
                            $ret = self::decreaseField($item_type, 'likes', 1, $item_id);
                            break;
                        default: # Like
                            $ret = self::increaseField($item_type, 'likes', 1, $item_id);
                            $this->_model->likeDislikeItem($user->id, $item_id, $item_type, $type);
                            MiwoVideos::get('utility')->trigger('onMiwovideosAfterSaveItem', array($item_id, $item_type, 'liked'));
                            break;
                    }
                    break;
                case 2: # Dislike
                    switch ($change) {
                        case 1: # Change Like to Dislike
                            self::updateField('likes', 'type', $type, $this->_model);
                            if (self::decreaseField($item_type, 'likes', 1, $item_id) && self::increaseField($item_type, 'dislikes', 1, $item_id)) $ret = true;
                            MiwoVideos::get('utility')->trigger('onMiwovideosAfterSaveItem', array($item_id, $item_type, 'disliked'));
                            break;
                        case -1: # Cancel Dislike
                            $this->_model->unlikeItem($item_id, $item_type, 2);
                            $ret =  self::decreaseField($item_type, 'dislikes', 1, $item_id);
                            break;
                        default: # Dislike
                            $ret = self::increaseField($item_type, 'dislikes', 1, $item_id);
                            $this->_model->likeDislikeItem($user->id, $item_id, $item_type, $type);
                            MiwoVideos::get('utility')->trigger('onMiwovideosAfterSaveItem', array($item_id, $item_type, 'disliked'));
                            break;
                    }
                    break;
            }

            if($ret) $json['success'] = 1;
        } else {
            $json['redirect'] = MiwoVideos::get('utility')->redirectWithReturn();
        }
        echo json_encode($json);
        exit();
    }
	
   	# Save changed record
	public function saveRecord($post, $table, &$id = 0) {
		# Get row
		$row = MiwoVideos::getTable($table);
		
		# Bind the form fields to the table
		if (!$row->bind($post)) {
			return MError::raiseWarning(500, $row->getError());
		}
		
		# Make sure the record is valid
		if (!$row->check()) {
			return MError::raiseWarning(500, $row->getError());
		}
		
		# Save record
		if (!$row->store()) {
			return MError::raiseWarning(500, $row->getError());
		}
		
		if (empty($id)) {
			$id = $row->id;
		}
		
		return true;
	}
	
	# Save changes
	public function save() {
		# Check token
		MRequest::checkToken() or mexit('Invalid Token');

		# Get post
		$post = MRequest::get('post', MREQUEST_ALLOWRAW);

        $cid = $post['cid'];
        $post['id'] = (int) $cid[0];

        if (isset($post['channel_id'])) {
            $post['user_id'] = MiwoVideos::get('channels')->getUserId($post['channel_id']);
        }

        if (empty($post['channel_id']) and empty($post['user_id'])) {
            $user_id = MFactory::getUser()->get('id');
            $post['channel_id'] = MiwoVideos::get('channels')->getDefaultChannel()->id;
            $post['user_id'] = $user_id;
        }

        $table = ucfirst($this->_component).ucfirst($this->_context);
        $row = MiwoVideos::getTable($table);
        $row->load($post['id']);

        # Custom Fields
        if (!empty($post['custom_fields'])) {
  		    $row->fields = json_encode($post['custom_fields']);
        }

		# Save record
		$table = ucfirst($this->_component).ucfirst($this->_context);
		
		if (!self::saveRecord($post, $table, $post['id'])) {
            $msg = MText::_('COM_MIWOVIDEOS_COMMON_RECORD_SAVED_NOT');

			return MError::raiseWarning(500, $msg);
		}
        else {
            MRequest::setVar('id', $post['id']);

            $msg = MText::_('COM_MIWOVIDEOS_COMMON_RECORD_SAVED');

            self::route($msg, $post);
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
                $this->updateField($this->_table, 'thumb', $fileName, $this->_model);
            }
        }

        # Banner Image
        if (isset($_FILES['banner_image']['name'])) {
            $fileExt = strtolower(MFile::getExt($_FILES['banner_image']['name']));
            $supportedTypes = array('jpg', 'png', 'gif');
            if (in_array($fileExt, $supportedTypes)) {
                $fileName = hash('haval256,5', MString::strtolower($_FILES['banner_image']['name'])) . '.' . $fileExt;
                $imagePath = MIWOVIDEOS_UPLOAD_DIR.'/images/'.$this->_context.'/'.$post['id'].'/banner/'.$fileName;
                $thumbPath = MIWOVIDEOS_UPLOAD_DIR.'/images/'.$this->_context.'/'.$post['id'].'/banner/thumb/'.$fileName;
                MFile::upload($_FILES['banner_image']['tmp_name'], $imagePath);
                MFolder::create(MIWOVIDEOS_UPLOAD_DIR.'/images/'.$this->_context.'/'.$post['id'].'/banner/thumb/');
                MiwoVideos::get('utility')->resizeImage($imagePath, $thumbPath, 1070, 175, 95);
                $post['banner'] = $fileName;
                $this->updateField($this->_table, 'banner', $fileName, $this->_model);
            }
        }

        if (isset($post['del_thumb']) and $row->thumb) {
            if (MFile::exists(MIWOVIDEOS_UPLOAD_DIR.'/images/'.$this->_context.'/'.$post['id'].'/orig/'.$row->thumb)) {
                MFile::delete(MIWOVIDEOS_UPLOAD_DIR.'/images/'.$this->_context.'/'.$post['id'].'/orig/'.$row->thumb);
            }

            if (MFile::exists(MIWOVIDEOS_UPLOAD_DIR.'/images/'.$this->_context.'/'.$post['id'].'/'.$thumb_size.'/'.$row->thumb)) {
                MFile::delete(MIWOVIDEOS_UPLOAD_DIR.'/images/'.$this->_context.'/'.$post['id'].'/'.$thumb_size.'/'.$row->thumb);
            }

            $post['thumb'] = '';
        }

        if (isset($post['del_banner']) and $row->banner) {
            if (MFile::exists(MIWOVIDEOS_UPLOAD_DIR.'/images/'.$this->_context.'/'.$post['id'].'/banner/'.$row->banner)) {
                MFile::delete(MIWOVIDEOS_UPLOAD_DIR.'/images/'.$this->_context.'/'.$post['id'].'/banner/'.$row->banner);
            }

            if (MFile::exists(MIWOVIDEOS_UPLOAD_DIR.'/images/'.$this->_context.'/'.$post['id'].'/banner/thumb/'.$row->banner)) {
                MFile::delete(MIWOVIDEOS_UPLOAD_DIR.'/images/'.$this->_context.'/'.$post['id'].'/banner/thumb/'.$row->banner);
            }

            $post['banner'] = '';
        }

        if (($this->_context == 'channels') and ($post['default'] == 1)) {
            MiwoDB::query("UPDATE `#__miwovideos_channels` SET `default` = 0 WHERE user_id = {$post['user_id']} AND id <> {$post['id']}");
        }

        return $msg;
	}
	
	# Update field
	public function updateField($table, $field, $value, $model, $where = true) {
		if ($where === true) {
			$where = self::_getWhere($model);
		}
		
		if (!MiwoDB::query("UPDATE #__{$this->_component}_{$table} SET `{$field}` = '{$value}' {$where}")) {
			return false;
		}

		return true;
	}
	
	# Increase likes,dislikes,hits fields
	public function increaseField($table, $field, $value, $id) {

		if (!MiwoDB::query("UPDATE #__{$this->_component}_{$table} SET `{$field}` = `{$field}` + {$value} WHERE id = {$id}")) {
			return false;
		}

		return true;
	}

    # Decrease likes,dislikes,hits fields
    public function decreaseField($table, $field, $value, $id) {

		if (!MiwoDB::query("UPDATE #__{$this->_component}_{$table} SET `{$field}` = `{$field}` - {$value} WHERE id IN({$id})")) {
			return false;
		}

		return true;
	}
	
	# Update param
	public function updateParam($table, $table_m, $field, $param, $value, $model, $where = true) {
		if (!$ids = self::_getIDs($table, $model, $where)) {
			return;
		}
		
		$row = MiwoVideos::getTable($table_m);
		
		if (!empty($ids) && is_array($ids)) {
			foreach ($ids as $index => $id) {
				if (!$row->load($id)) {
					continue;
				}
				
				$params = new MRegistry($row->$field);
				$params->set($param, $value);
				
				$row->$field = $params->toString();
				
				if (!$row->check()) {
					continue;
				}
				
				if (!$row->store()) {
					continue;
				}
			}
		}
	}
	
	# Get IDs
	public function _getIDs($table, $model, $where = true) {
		if ($where === true) {
			$where = self::_getWhere($model);
		}
		
		if (!$ids = MiwoDB::loadResultArray("SELECT id FROM #__{$this->_component}_{$table} {$where}")) {
			return false;
		}
		
		return $ids;
	}

    public function saveOrder() {
        # Check token
        MRequest::checkToken() or mexit('Invalid Token');

        $ret = $this->_model->saveOrder(ucfirst($this->_component).ucfirst($this->_context));
        if ($ret) {
            $msg = MText::_('COM_MIWOVIDEOS_ORDERING_SAVED');
        }
        else {
            $msg = MText::_('COM_MIWOVIDEOS_ORDERING_SAVING_ERROR');
        }

        $this->setRedirect('index.php?option='.$this->_option.'&view='.$this->_context, $msg);
    }

    public function subscribeToItem() {
        # Get vars
        $user = MFactory::getUser();
        $json = array();
        if($user->id != 0) {
            $type = MiwoVideos::getInput()->getWord('type', 0);
            $item_id = MiwoVideos::getInput()->getInt('id', 0);
                if ($type == "subscribe") {
                    if (is_null($this->_model->checkSubscription($item_id))) {
                    $ret = $this->_model->subscribeToItem($item_id);
                    } else {
                        $json['error'] = MText::_('COM_MIWOVIDEOS_SUBSCRIBER_EXIST');
                        $ret = false;
                    }
                } else {
                    $ret = $this->_model->unsubscribeItem($item_id);
                }

                $subs = $this->_model->getSubscriberCount($item_id);

                if ($ret) {
                    $json['success'] = 1;
                    $json['count'] = $subs;
                }
                else {
                    $json['success'] = 0;
                    $json['count'] = $subs;
                }
        } else {
            $json['redirect'] = MiwoVideos::get('utility')->redirectWithReturn();
        }
        echo json_encode($json);
        exit();
    }
    
    public function orderUp() {
        # Check token
        MRequest::checkToken() or mexit('Invalid Token');

        $this->_model->move(ucfirst($this->_component).ucfirst($this->_context), -1);

        $msg = MText::_('COM_MIWOVIDEOS_ORDERING_UPDATED');

        $this->setRedirect('index.php?option='.$this->_option.'&view='.$this->_context, $msg);
    }

    public function orderDown() {
        # Check token
        MRequest::checkToken() or mexit('Invalid Token');

        $this->_model->move(ucfirst($this->_component).ucfirst($this->_context), 1);

        $msg = MText::_('COM_MIWOVIDEOS_ORDERING_UPDATED');

        $this->setRedirect('index.php?option='.$this->_option.'&view='.$this->_context, $msg);
    }

    public function copy() {
        # Check token
        MRequest::checkToken() or mexit('Invalid Token');

        $this->_model->copy(ucfirst($this->_component).ucfirst($this->_context));

        $msg = MText::_('COM_MIWOVIDEOS_RECORD_COPIED');

        $this->setRedirect('index.php?option='.$this->_option.'&view='.$this->_context, $msg);

        return $msg;
    }

    public function autoComplete() {
        $query = MRequest::getString('query');
        $channels = json_encode($this->_model->autoComplete($query));

        echo $channels;
        exit();
    }

    public function createAutoFieldHtml() {
        $fieldid = MRequest::getInt('fieldid');
        $html = MiwoVideos::get('fields')->createAutoFieldHtml($fieldid);

        echo $html;
        exit();
    }

    function cdn() {

        if ($this->config->get('pid') != MRequest::getVar('token')) {
            die('Invalid token');
        }

        $plugin = MiwoVideos::getPlugin($this->config->get('cdn'));

        if ($plugin) {
            return $plugin->maintenance();
        } else {
            die($this->config->get('cdn') . ' plugin is not published');
        }
    }
}