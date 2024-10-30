<?php
/*
* @package		MiwoVideos
* @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
* @license		GNU General Public License version 2 or later
*/
# No Permission
defined('MIWI') or die ('Restricted access');

# Controller Class
class MiwovideosControllerUpgrade extends MiwovideosController {

	# Main constructer
	public function __construct() {
		parent::__construct('upgrade');
	}
	
	# Upgrade
    public function upgrade() {
		# Check token
		MRequest::checkToken() or mexit('Invalid Token');
		
		# Upgrade
		if ($this->_model->upgrade()) {
            $msg = MText::_('COM_MIWOVIDEOS_UPGRADE_SUCCESS');
        }
        else {
            $msg = MText::_('COM_MIWOVIDEOS_UPGRADE_ERROR');
        }
		
		# Return
		$this->setRedirect('index.php?option=com_miwovideos&view=upgrade', $msg);
    }
}