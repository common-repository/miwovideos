<?php
/*
* @package		MiwoVideos
* @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
* @license		GNU General Public License version 2 or later
*/
# No Permission
defined('MIWI') or die ('Restricted access');

class MiwovideosControllerRestoreMigrate extends MiwovideosController {

    public function __construct() {
        parent::__construct('restoremigrate');
    }

    public function backup() {
		MRequest::checkToken() or mexit('Invalid Token');

		if(!$this->_model->backup()){
			MError::raiseWarning(500, MText::_('COM_MIWOVIDEOS_RESTOREMIGRATE_MSG_BACKUP_NO'));
		}
    }

    public function restore() {
		MRequest::checkToken() or mexit('Invalid Token');

		if(!$this->_model->restore()){
			$msg = MText::_('COM_MIWOVIDEOS_RESTOREMIGRATE_MSG_RESTORE_NO');
		} else {
			$msg = MText::_('COM_MIWOVIDEOS_RESTOREMIGRATE_MSG_RESTORE_OK');
		}

		parent::route($msg);
    }

    public function migrate() {
        MRequest::checkToken() or mexit('Invalid Token');

        if(!$this->_model->migrate()){
            $msg = MText::_('COM_MIWOVIDEOS_RESTOREMIGRATE_MSG_RESTORE_NO');
        } else {
            $msg = MText::_('COM_MIWOVIDEOS_RESTOREMIGRATE_MSG_RESTORE_OK');
        }

        parent::route($msg);
    }
}
