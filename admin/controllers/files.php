<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die;

class MiwovideosControllerFiles extends MiwoVideosController {

	public function __construct($config = array()) {
		parent::__construct('files');
	}

    public function delete() {
        MRequest::checkToken() or mexit('Invalid Token');

        $cid = MRequest::getVar('cid', array(), 'post', 'array');
        MArrayHelper::toInteger($cid);

        if (!$this->_model->delete($cid)) {
            $msg = MText::_('COM_MIWOVIDEOS_COMMON_RECORDS_DELETED_NOT');
        } else {
            $msg = MText::_('COM_MIWOVIDEOS_COMMON_RECORDS_DELETED');
        }

        parent::route($msg);

        return $msg;
    }
}