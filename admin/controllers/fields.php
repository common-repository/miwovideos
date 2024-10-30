<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die;

class MiwovideosControllerFields extends MiwoVideosController {

	public function __construct($config = array()) {
		parent::__construct('fields');
	}

	public function save() {
		MRequest::checkToken() or mexit('Invalid Token');
		$post = MRequest::get('post', MREQUEST_ALLOWRAW);
		$cid = $post['cid'];
		$post['id'] = (int) $cid[0];

		$ret = $this->_model->store($post);
		if ($ret) {
			$msg = MText::_('COM_MIWOVIDEOS_COMMON_RECORD_SAVED');
		}
        else {
			$msg = MText::_('COM_MIWOVIDEOS_COMMON_RECORD_SAVED_NOT');
		}

		parent::route($msg, $post);

        return $msg;
	}
}