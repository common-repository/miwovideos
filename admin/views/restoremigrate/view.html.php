<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die('Restricted Access');

class MiwovideosViewRestoreMigrate extends MiwovideosView {

	public function display($tpl = null) {
        if (!MiwoVideos::get('acl')->canAdmin()) {
            MFactory::getApplication()->redirect('index.php?option=com_miwovideos', MText::_('JERROR_ALERTNOAUTHOR'));
        }

        if ($this->_mainframe->isAdmin()) {
            MToolBarHelper::title(MText::_('COM_MIWOVIDEOS_CPANEL_RESTORE'), 'miwovideos');
            $this->toolbar->appendButton('Popup', 'help1', MText::_('Help'), 'http://miwisoft.com/support/docs/miwovideos/user-manual/restore-migrate?tmpl=component', 650, 500);
        }

		parent::display($tpl);
	}
}
