<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die ;

class MiwovideosViewChannels extends MiwovideosView {

	public function display($tpl = null) {
        //$Itemid = '&Itemid='.MiwoVideos::getInput()->getInt('Itemid', 0);
        $Itemid = MiwoVideos::get('router')->getItemid(array('view' => 'channels'), null, true);

        $filter_order		= $this->_mainframe->getUserStateFromRequest('com_miwovideos.history.filter_order',         'filter_order',             'title',  'cmd');
        $filter_order_Dir	= $this->_mainframe->getUserStateFromRequest('com_miwovideos.history.filter_order_Dir',     'filter_order_Dir',         'DESC',             'word');
        $search				= $this->_mainframe->getUserStateFromRequest('com_miwovideos.history.miwovideos_search',    'miwovideos_search',        '',                 'string');
        $display			= $this->_mainframe->getUserStateFromRequest('com_miwovideos.history.display',              'display',                  ''.$this->config->get('listing_style').'',                 'string');
        $search				= MiwoVideos::get('utility')->cleanUrl(MString::strtolower($search));

        $lists = array();
        $lists['search'] = $search;
        $lists['order_Dir'] = $filter_order_Dir;
        $lists['order'] = $filter_order;

        $this->lists                = $lists;
        $this->display              = $display;
		$this->items                = $this->get('Items');
        $this->checksubscription    = $this->getModel()->checkSubscription();
		$this->pagination           = $this->get('Pagination');
        $this->params               = $this->_mainframe->getParams();
        $this->Itemid               = $Itemid;
		
		parent::display($tpl);				
	}	
}