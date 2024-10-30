<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die ;

class MiwovideosViewCategories extends MiwovideosView {

	public function display($tpl = null) {
        if (!$this->acl->canEdit()) {
            MFactory::getApplication()->redirect('index.php?option=com_miwovideos', MText::_('JERROR_ALERTNOAUTHOR'));
        }

        $task = MRequest::getCmd('task');
        $text = ($task == 'edit') ? MText::_('COM_MIWOVIDEOS_EDIT') : MText::_('COM_MIWOVIDEOS_NEW');

        if ($this->_mainframe->isAdmin()) {
            MToolBarHelper::title(MText::_('COM_MIWOVIDEOS_CATEGORY').': <small><small>[ ' . $text.' ]</small></small>', 'miwovideos');
            MToolBarHelper::apply();
            MToolBarHelper::save();
            MToolBarHelper::save2new();
            MToolBarHelper::cancel();
            MToolBarHelper::divider();
            $this->toolbar->appendButton('Popup', 'help1', MText::_('Help'), 'http://miwisoft.com/support/docs/wordpress/miwovideos/user-manual/categories?tmpl=component', 650, 500);
        }
        
		$item = $this->get('EditData');			
		
		$options = array() ;
		$options[] = MHtml::_('select.option', '', MText::_('Default Layout')) ;
		$options[] = MHtml::_('select.option', 'table', MText::_('Table Layout')) ;
		$options[] = MHtml::_('select.option', 'calendar', MText::_('Calendar Layout')) ;

		$lists['parent'] = MiwoVideos::get('utility')->buildParentCategoryDropdown($item);
		$lists['published'] = MiwoVideos::get('utility')->getRadioList('published', $item->published);
		
		$lists['language'] = MHtml::_('select.genericlist', MHtml::_('contentlanguage.existing', true, true), 'language', ' class="inputbox"', 'value', 'text', $item->language);

        MHtml::_('behavior.tooltip');

		$this->item = $item;
		$this->lists = $lists;
		
		parent::display($tpl);				
	}
}