<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die ;

class MiwovideosViewFiles extends MiwovideosView {
	
	public function display($tpl = null) {
        if ($this->_mainframe->isAdmin()) {
            $this->addToolbar();
        }
		
		$filter_order		= $this->_mainframe->getUserStateFromRequest($this->_option.'.files.filter_order',	    'filter_order',		    'f.id',	    'cmd');
		$filter_order_Dir	= $this->_mainframe->getUserStateFromRequest($this->_option.'.files.filter_order_Dir',	'filter_order_Dir',     'DESC', 	'word');
        $filter_published   = $this->_mainframe->getUserStateFromRequest($this->_option.'.files.filter_published',	'filter_published',	    '');
        //$filter_process     = $this->_mainframe->getUserStateFromRequest($this->_option.'.files.filter_process',	'filter_process',	    '');
		$search				= $this->_mainframe->getUserStateFromRequest($this->_option.'.files.search',			'search', 				'', 		'string');
		$search				= MString::strtolower($search);
		
		$lists['search'] 	= $search;	
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] 	= $filter_order;
        
        # Publish Options
        $options = array();
        $options[] = MHtml::_('select.option', '', MText::_('MOPTION_SELECT_PUBLISHED'));
        $options[] = MHtml::_('select.option', 1, MText::_('COM_MIWOVIDEOS_PUBLISHED'));
        $options[] = MHtml::_('select.option', 0, MText::_('COM_MIWOVIDEOS_UNPUBLISHED'));
        $lists['filter_published'] = MHtml::_('select.genericlist', $options, 'filter_published', ' class="inputbox"  ', 'value', 'text', $filter_published);

		$options = array();
		$options[] = MHtml::_('select.option', '', MText::_('Bulk Actions'));

				$lists['bulk_actions'] = MHtml::_('select.genericlist', $options, 'bulk_actions', ' class="inputbox"', 'value', 'text', '');
			

        MHtml::_('behavior.tooltip');

        $this->items 	            = $this->get('Items');
		$this->lists 		        = $lists;
		
		$this->pagination 	        = $this->get('Pagination');
        $this->acl                  = MiwoVideos::get('acl');
			
		parent::display($tpl);				
	}

    protected function addToolbar() {
        MToolBarHelper::title(MText::_('COM_MIWOVIDEOS_CPANEL_FILES'), 'miwovideos');

        
        $this->toolbar->appendButton('Popup', 'help1', MText::_('Help'), 'http://miwisoft.com/support/docs/wordpress/miwovideos/user-manual/files?tmpl=component', 650, 500);
    }
}