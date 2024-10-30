<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die ;

class MiwovideosViewFields extends MiwovideosView {

	public function display($tpl = null){
        if ($this->_mainframe->isAdmin()) {
            $this->addToolbar();
        }
		
		$filter_order		= $this->_mainframe->getUserStateFromRequest($this->_option.'.fields.filter_order',			'filter_order',		'ordering',	'cmd');
		$filter_order_Dir	= $this->_mainframe->getUserStateFromRequest($this->_option.'.fields.filter_order_Dir',		'filter_order_Dir',	'',		'word');
        $filter_type	    = $this->_mainframe->getUserStateFromRequest($this->_option.'.fields.filter_type',		    'filter_type',	    '',		'word');
		$filter_published	= $this->_mainframe->getUserStateFromRequest($this->_option.'.fields.filter_published',		'filter_published',	'');
		$filter_language	= $this->_mainframe->getUserStateFromRequest($this->_option.'.fields.filter_language',		'filter_language',	'',		'string');
        $search				= $this->_mainframe->getUserStateFromRequest($this->_option.'.fields.search',				'search',			'',		'string');
		$search				= MString::strtolower($search);

        $options = array();
        $options[] = MHtml::_('select.option', '', 						MText::_('COM_MIWOVIDEOS_SEL_FIELD_TYPE'));
        $options[] = MHtml::_('select.option', 'text', 					MText::_('COM_MIWOVIDEOS_FIELDS_TEXT'));
        $options[] = MHtml::_('select.option', 'textarea', 				MText::_('COM_MIWOVIDEOS_FIELDS_TEXTAREA'));
        $options[] = MHtml::_('select.option', 'radio', 				MText::_('COM_MIWOVIDEOS_FIELDS_RADIO'));
        $options[] = MHtml::_('select.option', 'list', 					MText::_('COM_MIWOVIDEOS_FIELDS_LIST'));
        $options[] = MHtml::_('select.option', 'multilist', 			MText::_('COM_MIWOVIDEOS_FIELDS_MULTILIST'));
        $options[] = MHtml::_('select.option', 'checkbox', 				MText::_('COM_MIWOVIDEOS_FIELDS_CHECKBOX'));
        $options[] = MHtml::_('select.option', 'calendar', 				MText::_('COM_MIWOVIDEOS_FIELDS_CALENDAR'));
        $options[] = MHtml::_('select.option', 'miwovideoscountries',	MText::_('COM_MIWOVIDEOS_FIELDS_MIWOVIDEOSCOUNTRIES'));
        $options[] = MHtml::_('select.option', 'email', 				MText::_('COM_MIWOVIDEOS_FIELDS_EMAIL'));
        $options[] = MHtml::_('select.option', 'language', 				MText::_('COM_MIWOVIDEOS_FIELDS_LANGUAGE'));
        $options[] = MHtml::_('select.option', 'timezone', 				MText::_('COM_MIWOVIDEOS_FIELDS_TIMEZONE'));
        $lists['filter_type'] = MHtml::_('select.genericlist', $options, 'filter_type', ' class="inputbox" ', 'value', 'text', $filter_type);

		$options = array();
		$options[] = MHtml::_('select.option', '', MText::_('Bulk Actions'));

		if ($this->acl->canEditState()) {
			$options[] = MHtml::_('select.option', 'publish', MText::_('MTOOLBAR_PUBLISH'));
			$options[] = MHtml::_('select.option', 'unpublish', MText::_('MTOOLBAR_UNPUBLISH'));
		}

		if ($this->acl->canCreate()) {
			$options[] = MHtml::_('select.option', 'copy', MText::_('Copy'));
		}
			

		if ($this->acl->canDelete()) {
			$options[] = MHtml::_('select.option', 'delete', MText::_('MTOOLBAR_DELETE'));
		}

		$lists['bulk_actions'] = MHtml::_('select.genericlist', $options, 'bulk_actions', ' class="inputbox"', 'value', 'text', '');
			

        $options = array();
        $options[] = MHtml::_('select.option', '',	MText::_('MOPTION_SELECT_PUBLISHED'));
        $options[] = MHtml::_('select.option', 1, 	MText::_('COM_MIWOVIDEOS_PUBLISHED'));
        $options[] = MHtml::_('select.option', 0, 	MText::_('COM_MIWOVIDEOS_UNPUBLISHED'));
        $lists['filter_published'] = MHtml::_('select.genericlist', $options, 'filter_published', ' class="inputbox"  ', 'value', 'text', $filter_published);
		
		$lists['order_Dir']			= $filter_order_Dir;
		$lists['order'] 			= $filter_order;
		$lists['filter_language'] 	= $filter_language;
		$lists['search'] 			= $search;

        MHtml::_('behavior.tooltip');

        $this->lists				= $lists;
		$this->items				= $this->get('Items');
		$this->pagination			= $this->get('Pagination');
        $this->langs 				= MiwoVideos::get('utility')->getLanguages();
		
		parent::display($tpl);
	}

    protected function addToolbar() {
        MToolBarHelper::title(MText::_('COM_MIWOVIDEOS_CPANEL_FIELDS'), 'miwovideos');

        if ($this->acl->canCreate()) {
            MToolBarHelper::addNew();
        }

        
        $this->toolbar->appendButton('Popup', 'help1', MText::_('Help'), 'http://miwisoft.com/support/docs/wordpress/miwovideos/user-manual/fields?tmpl=component', 650, 500);
    }
}