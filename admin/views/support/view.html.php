<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die('Restricted Access');

# View Class
class MiwovideosViewSupport extends MiwovideosView {

	public function display($tpl = null) {
        if ($this->_mainframe->isAdmin()) {
            MToolBarHelper::title(MText::_('COM_MIWOVIDEOS_CPANEL_SUPPORT'), 'miwovideos');
            MToolBarHelper::back(MText::_('Back'), 'index.php?option=com_miwovideos');
        }
		
		if (MRequest::getCmd('task', '') == 'translators') {
			$this->document->setCharset('iso-8859-9');
		}
		
		parent::display($tpl);
	}
}