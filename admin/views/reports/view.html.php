<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die ;


class MiwovideosViewReports extends MiwovideosView {
	
	function display($tpl = null) {
        if ($this->_mainframe->isAdmin()) {
            $this->addToolbar();
        }
		
		$filter_order		= $this->_mainframe->getUserStateFromRequest($this->_option.'.reports.video_filter_order',	'filter_order',		    'r.created',	'cmd');
		$filter_order_Dir	= $this->_mainframe->getUserStateFromRequest($this->_option.'.reports.filter_order_Dir',	'filter_order_Dir',	    'DESC',		    'word');
        $filter_reason		= $this->_mainframe->getUserStateFromRequest($this->_option.'.reports.filter_reason',	    'filter_reason',		'',				'string');
        $filter_type 		= $this->_mainframe->getUserStateFromRequest($this->_option.'.reports.filter_type',			'filter_type',	    	'',		    	'string');
        $filter_language	= $this->_mainframe->getUserStateFromRequest($this->_option.'.reports.filter_language',		'filter_language',	    '',		    	'string');
		$search				= $this->_mainframe->getUserStateFromRequest($this->_option.'.reports.search',				'search',			    '',		    	'string');
		$search				= MString::strtolower($search);

        $reasons = $this->get('Reasons');
        $options = array();
        $options[] = MHtml::_('select.option', '', MText::_('COM_MIWOVIDEOS_SELECT_REASON'));
        foreach($reasons as $reason){
            $options[] = MHtml::_('select.option',  $reason->id, $reason->title);
        }
        $lists['filter_reason'] = MHtml::_('select.genericlist', $options, 'filter_reason', ' class="inputbox" style="width: 220px;"  ', 'value', 'text', $filter_reason);

        $lists['search']		 	= $search ;
		$lists['order_Dir']			= $filter_order_Dir;
		$lists['order'] 		 	= $filter_order;

		$options = array();
		$options[] = MHtml::_('select.option', '', MText::_('COM_MIWOVIDEOS_SELECT_TYPE'));
		$options[] = MHtml::_('select.option',  'video', MText::_('COM_MIWOVIDEOS_VIDEO'));
		$options[] = MHtml::_('select.option',  'channel', MText::_('COM_MIWOVIDEOS_CHANNEL'));
		$lists['filter_type'] = MHtml::_('select.genericlist', $options, 'filter_type', ' class="inputbox" style="width: 140px;"  ', 'value', 'text', $filter_type);

		$options = array();
		$options[] = MHtml::_('select.option', '', MText::_('Bulk Actions'));

				$lists['bulk_actions'] = MHtml::_('select.genericlist', $options, 'bulk_actions', ' class="inputbox"', 'value', 'text', '');
			


        MHtml::_('behavior.tooltip');
		
		$this->lists 			= $lists;
		$this->items 			= $this->get('Items');
		$this->pagination 		= $this->get('Pagination');
        $this->langs            = MiwoVideos::get('utility')->getLanguages();
		$this->filter_language 	= $filter_language;

		parent::display($tpl);				
	}

    function displayDetails($tpl = null) {
        $this->items = $this->get('Items');
		parent::display($tpl);
	}

    protected function addToolbar() {
        MToolBarHelper::title(MText::_('COM_MIWOVIDEOS_CPANEL_REPORTS'), 'miwovideos');

        $reasons_icon = 'icon-32-miwovideos-reasons';
        if (MiwoVideos::is30()){
            $reasons_icon = 'checkbox-partial';
        }

	    $this->toolbar->appendButton('link', $reasons_icon, MText::_('COM_MIWOVIDEOS_REASONS'), MiwoVideos::get('utility')->route('index.php?option=com_miwovideos&view=reasons'));

        
        $this->toolbar->appendButton('Popup', 'help1', MText::_('Help'), 'http://miwisoft.com/support/docs/wordpress/miwovideos/user-manual/fields?tmpl=component', 650, 500);
    }
}