<?php
/**
 * @package        MiwoVideos
 * @copyright      Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license        GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die;


class MiwovideosControllerReasons extends MiwoVideosController {

	public function __construct($config = array()) {
		parent::__construct('reasons', 'report_reasons');
	}

	public function save() {
		$post = MRequest::get('post', MREQUEST_ALLOWRAW);
		$cid  = MRequest::getVar('cid', array(), 'post');

		if (!empty($cid[0])) {
			$post['id'] = $cid[0];
		}

		$table = ucfirst($this->_component).ucfirst($this->_context);
		$row   = MiwoVideos::getTable($table);
		$row->load($post['id']);

		$ret = $this->_model->store($post);

		MRequest::setVar('cid', $post['id'], 'post');

		if (empty($post['association'])) {
			parent::updateField('report_reasons', 'association', $post['id'], 'reasons');
		}

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