<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die( 'Restricted access' );

class MiwovideosControllerMiwovideos extends MiwovideosController {

	# Main constructer
    public function __construct() {
        parent::__construct('miwovideos');
    }
	
	public function savePersonalID() {
		# Check token
		MRequest::checkToken() or mexit('Invalid Token');

		$msg = $this->_model->savePersonalID();
        
        $this->setRedirect('index.php?option=com_miwovideos', $msg);
    }
	
	public function jusersync() {
		# Check token
		MRequest::checkToken() or mexit('Invalid Token');

		$msg = $this->_model->jusersync();

        $this->setRedirect('index.php?option=com_miwovideos', MText::sprintf('COM_MIWOVIDEOS_ACCOUNT_SYNC_DONE'));
    }
}