<?php
/**
 * @package        MiwoVideos
 * @copyright      Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license        GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die;

class MiwovideosViewUsers extends MiwovideosView {

	public function display($tpl = null) {
		if ($this->_mainframe->isAdmin()) {
			$this->addToolbar();
		}

		$filter_order     = $this->_mainframe->getUserStateFromRequest($this->_option.'.users.filter_order', 'filter_order', 'u.user_login', 'cmd');
		$filter_order_Dir = $this->_mainframe->getUserStateFromRequest($this->_option.'.users.filter_order_Dir', 'filter_order_Dir', 'DESC', 'word');
		$search           = $this->_mainframe->getUserStateFromRequest($this->_option.'.users.search', 'search', '', 'string');
		$search           = MString::strtolower($search);

		$lists['search']    = $search;
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order']     = $filter_order;

		$this->items      = $this->get('Items');
		$this->state      = $this->get('State');
		$this->lists      = $lists;
		$this->pagination = $this->get('Pagination');

		parent::display($tpl);
	}

	public function displayModal($tpl = null) {
		$this->display($tpl);
	}

	protected function addToolbar() {
		MToolBarHelper::title(MText::_('COM_MIWOVIDEOS_USERS'), 'miwovideos');
	}
}
