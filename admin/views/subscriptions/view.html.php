<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die ;

class MiwovideosViewSubscriptions extends MiwovideosView {
	
	public function display($tpl = null) {
        if ($this->_mainframe->isAdmin()) {
            $this->addToolbar();
        }
		
		$filter_order		= $this->_mainframe->getUserStateFromRequest($this->_option.'.subscriptions.filter_order',	    'filter_order',			's.user_id',	'cmd');
		$filter_order_Dir	= $this->_mainframe->getUserStateFromRequest($this->_option.'.subscriptions.filter_order_Dir',	'filter_order_Dir',     'DESC', 		'word');
        $filter_user 	    = $this->_mainframe->getUserStateFromRequest($this->_option.'.subscriptions.filter_user',		'filter_user',	    	0,		    	'int');
		$search				= $this->_mainframe->getUserStateFromRequest($this->_option.'.subscriptions.search', 			'search', 				'', 			'string');
		$search				= MString::strtolower($search);
		
		$lists['search'] 	= $search;	
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] 	= $filter_order;

        $users = MiwoVideos::get('utility')->getUsers();
        $options = array();
        $options[] = MHtml::_('select.option', '', MText::_('MLIB_FORM_CHANGE_USER'));
        foreach($users as $user){
            $options[] = MHtml::_('select.option',  $user->id, $user->username);
        }
        $lists['filter_user'] = MHtml::_('select.genericlist', $options, 'filter_user', ' class="inputbox" style="width: 220px;"  ', 'value', 'text', $filter_user);

		$options = array();
		$options[] = MHtml::_('select.option', '', MText::_('Bulk Actions'));

				$lists['bulk_actions'] = MHtml::_('select.genericlist', $options, 'bulk_actions', ' class="inputbox"', 'value', 'text', '');
			

		$this->items 	            = $this->get('Items');
		$this->lists 		        = $lists;
		
		$this->pagination 	        = $this->get('Pagination');
			
		parent::display($tpl);				
	}

    public function displayModal($tpl = null){
        $this->display($tpl);
    }

    protected function addToolbar() {
        MToolBarHelper::title(MText::_('COM_MIWOVIDEOS_CPANEL_SUBSCRIPTIONS'), 'miwovideos');

        
        $this->toolbar->appendButton('Popup', 'help1', MText::_('Help'), 'http://miwisoft.com/support/docs/wordpress/miwovideos/user-manual/subscriptions?tmpl=component', 650, 500);
    }
}