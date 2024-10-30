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
        if ($this->_mainframe->isAdmin()) {
            $this->addToolbar();
        }
		
		$filter_order		= $this->_mainframe->getUserStateFromRequest($this->_option.'.playlists.filter_order',	        'filter_order',		    'p.title',	'cmd');
		$filter_order_Dir	= $this->_mainframe->getUserStateFromRequest($this->_option.'.playlists.filter_order_Dir',	    'filter_order_Dir',     'DESC', 	'word');
        $filter_published   = $this->_mainframe->getUserStateFromRequest($this->_option.'.playlists.filter_published',	    'filter_published',	    '');
        $filter_access      = $this->_mainframe->getUserStateFromRequest($this->_option.'.playlists.filter_access',	        'filter_access',	    '');
		$filter_language	= $this->_mainframe->getUserStateFromRequest($this->_option.'.playlists.filter_language',		'filter_language',	    '',		    'string');
		$search				= $this->_mainframe->getUserStateFromRequest($this->_option.'.playlists.search', 			    'search', 				'', 		'string');
		$search				= MString::strtolower($search);
		
		$lists['search'] 	= $search;	
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] 	= $filter_order;

        $options = array();
        $options[] = MHtml::_('select.option', '', MText::_('MOPTION_SELECT_PUBLISHED'));
        $options[] = MHtml::_('select.option', 1, MText::_('COM_MIWOVIDEOS_PUBLISHED'));
        $options[] = MHtml::_('select.option', 0, MText::_('COM_MIWOVIDEOS_UNPUBLISHED'));
        $lists['filter_published'] = MHtml::_('select.genericlist', $options, 'filter_published', ' class="inputbox"  ', 'value', 'text', $filter_published);

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
			
		
		$this->columnData = $this->get('Columns');
		
		if ($this->columnData != NULL){
			$this->cTitle		= $this->columnData[0];
			$this->cFields		= $this->columnData[1];
			$this->cPlaylists 	= $this->columnData[2];
		} else {
			$this->cTitle		= NULL;
			$this->cFields		= NULL;
			$this->cPlaylists 	= $this->get('Items');
		}

        MHtml::_('behavior.tooltip');

        $this->filter_access        = $filter_access;
		$this->filter_language      = $filter_language;
		$this->lists 		        = $lists;
		
		$this->pagination 	        = $this->get('Pagination');
			
		parent::display($tpl);				
	}

    protected function addToolbar() {
        MToolBarHelper::title(MText::_('COM_MIWOVIDEOS_CPANEL_PLAYLISTS'), 'miwovideos');

        if ($this->acl->canCreate()) {
            MToolBarHelper::addNew();
        }

        
        $this->toolbar->appendButton('Popup', 'help1', MText::_('Help'), 'http://miwisoft.com/support/docs/wordpress/miwovideos/user-manual/playlists?tmpl=component', 650, 500);
    }
}