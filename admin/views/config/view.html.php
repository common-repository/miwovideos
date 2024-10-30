<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die;

class MiwovideosViewConfig extends MiwovideosView {
	
	public function display($tpl = null) {
        $form = MForm::getInstance('config', MPATH_WP_PLG.'/miwovideos/admin/config.xml', array(), false, '/config');
        $params = MiwoVideos::getConfig();
        $form->bind($params);

        if ($this->_mainframe->isAdmin()) {
            MToolBarHelper::title(MText::_('Configuration').':' , 'miwovideos' );
            MToolBarHelper::apply();
            MToolBarHelper::save();
            MToolBarHelper::cancel();
        }

        $this->form = $form;
			
		parent::display($tpl);				
	}

}
