<?php
/**
 * @package        MiwoVideos
 * @copyright      Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license        GNU General Public License version 2 or later
 */

defined('MIWI') or die;

class MiwovideosViewLatest extends MiwovideosView {

	public function display($tpl = null) {
		$item = $this->get('Item');

		if (is_object($item) and !$this->acl->canAccess($item->access)) {
			$this->_mainframe->redirect(MRoute::_('index.php?option=com_miwovideos&view=category'), MText::_('JERROR_ALERTNOAUTHOR'), 'error');
		}

		$Itemid = MiwoVideos::get('router')->getItemid(array('view' => 'latest'), null, true);

		$search          = $this->_mainframe->getUserStateFromRequest('com_miwovideos.history.miwovideos_search', 'miwovideos_search', '', 'string');
		$display         = $this->_mainframe->getUserStateFromRequest('com_miwovideos.history.display', 'display', ''.$this->config->get('listing_style').'', 'string');
		$search          = MiwoVideos::get('utility')->cleanUrl(MString::strtolower($search));
		$lists['search'] = $search;

		$this->lists      = $lists;
		$this->item       = $item;
		$this->params     = $this->_mainframe->getParams();
		$this->Itemid     = $Itemid;
		$this->display    = $display;
		$this->items      = $this->get('Items');
		$this->pagination = $this->get('Pagination');

		parent::display($tpl);

	}
}