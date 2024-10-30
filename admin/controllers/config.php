<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die;

class MiwovideosControllerConfig extends MiwoVideosController {

	public function __construct($config = array()) {
        parent::__construct('config');
	}

    // Save changes
    function save() {
        // Check token
        MRequest::checkToken() or mexit('Invalid Token');

        $this->_model->save();

        $this->setRedirect('index.php?page=miwovideos', MText::_('COM_MIWOVIDEOS_CONFIG_SAVED'));
    }

    // Apply changes
    function apply() {
        // Check token
        MRequest::checkToken() or mexit('Invalid Token');

        $this->_model->save();

        $this->setRedirect('index.php?page=miwovideos&view=config', MText::_('COM_MIWOVIDEOS_CONFIG_SAVED'));
    }

    // Cancel saving changes
    function cancel() {
        // Check token
        MRequest::checkToken() or mexit('Invalid Token');

        $this->setRedirect('index.php?page=miwovideos', MText::_('COM_MIWOSEARCH_CONFIG_NOT_SAVED'));

    }
}
