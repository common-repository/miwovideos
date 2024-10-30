<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die ;


class MiwovideosViewPlaylists extends MiwovideosView {
	
	public function display($tpl = null) {
        $item = $this->get('EditData');

        if (!$this->acl->canEditOwn($item->user_id)) {
            MFactory::getApplication()->redirect('index.php?option=com_miwovideos', MText::_('JERROR_ALERTNOAUTHOR'));
        }

        $task = MRequest::getCmd('task');
        $text = ($task == 'edit') ? MText::_('COM_MIWOVIDEOS_EDIT') : MText::_('COM_MIWOVIDEOS_NEW');

        if ($this->_mainframe->isAdmin()) {
            MToolBarHelper::title(MText::_('COM_MIWOVIDEOS_CPANEL_PLAYLISTS').': <small><small>[ ' . $text.' ]</small></small>' , 'miwovideos' );
            MToolBarHelper::apply();
            MToolBarHelper::save();
            MToolBarHelper::save2new();
            MToolBarHelper::cancel();
            MToolBarHelper::divider();
            $this->toolbar->appendButton('Popup', 'help1', MText::_('Help'), 'http://miwisoft.com/support/docs/wordpress/miwovideos/user-manual/playlists?tmpl=component', 650, 500);
        }

        $null_date = MFactory::getDbo()->getNullDate();

		if ($task == "edit" and !empty($item->id)){
			$this->playlist = MiwoVideos::get('playlists')->getPlaylist($item->id);
		} else {
			$this->playlist = null; 
		}
		
        $lists['published'] = MiwoVideos::get('utility')->getRadioList('published', $item->published);
        $lists['featured'] = MiwoVideos::get('utility')->getRadioList('featured', $item->featured);
        
        $lists['language'] = MHtml::_('select.genericlist', MHtml::_('contentlanguage.existing', true, true), 'language', ' class="inputbox"', 'value', 'text', $item->language);

        MHtml::_('behavior.tooltip');

        $this->lists 		    = $lists;
		$this->item 		    = $item;
		$this->null_date		= $null_date;
		$this->fields	    	= MiwoVideos::get('fields')->getVideoFields($item->id);
        $this->availableFields  = MiwoVideos::get('fields')->getAvailableFields();

		parent::display($tpl);
	}
}