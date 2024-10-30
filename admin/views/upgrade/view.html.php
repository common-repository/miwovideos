<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die('Restricted Access');

# View Class
class MiwovideosViewUpgrade extends MiwovideosView {
	
	public function display($tpl = null) {
        if ($this->_mainframe->isAdmin()) {
            MToolBarHelper::title(MText::_('COM_MIWOVIDEOS_CPANEL_UPGRADE'), 'miwovideos');
            $this->toolbar->appendButton('Popup', 'help1', MText::_('Help'), 'http://miwisoft.com/support/docs/wordpress/miwovideos/user-manual/upgrade?tmpl=component', 650, 500);
        }
		parent::display($tpl);
	}
}
