<?php
/**
 * @package        MiwoVideos
 * @copyright      Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license        GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die;

class MiwovideosViewProcesses extends MiwovideosView {

	public function display($tpl = null) {
		if ($this->_mainframe->isAdmin()) {
			$this->addToolbar();
		}

		$filter_order     = $this->_mainframe->getUserStateFromRequest($this->_option.'.processes.filter_order', 'filter_order', 'pt.title', 'cmd');
		$filter_order_Dir = $this->_mainframe->getUserStateFromRequest($this->_option.'.processes.filter_order_Dir', 'filter_order_Dir', 'DESC', 'word');
		$filter_status    = $this->_mainframe->getUserStateFromRequest($this->_option.'.processes.filter_status', 'filter_status', '');
		$search           = $this->_mainframe->getUserStateFromRequest($this->_option.'.processes.search', 'search', '', 'string');
		$search           = MString::strtolower($search);

		$lists['search']    = $search;
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order']     = $filter_order;

		$options                = array();
		$options[]              = MHtml::_('select.option', '', MText::_('MOPTION_SELECT_PUBLISHED'));
		$options[]              = MHtml::_('select.option', 1, MText::_('COM_MIWOVIDEOS_SUCCESSFUL'));
		$options[]              = MHtml::_('select.option', 2, MText::_('COM_MIWOVIDEOS_FAILED'));
		$options[]              = MHtml::_('select.option', 3, MText::_('COM_MIWOVIDEOS_QUEUED'));
		$lists['filter_status'] = MHtml::_('select.genericlist', $options, 'filter_status', ' class="inputbox"  ', 'value', 'text', $filter_status);

		$options = array();
		$options[] = MHtml::_('select.option', '', MText::_('Bulk Actions'));

		if ($this->acl->canEdit()) {
			$options[] = MHtml::_('select.option', 'process', 'Process');
			$options[] = MHtml::_('select.option', 'processAll', 'Process All');
		}
			

		if ($this->acl->canDelete()) {
			$options[] = MHtml::_('select.option', 'delete', MText::_('MTOOLBAR_DELETE'));
		}

		$lists['bulk_actions'] = MHtml::_('select.genericlist', $options, 'bulk_actions', ' class="inputbox"', 'value', 'text', '');
			

		$this->items = $this->get('Items');

		MHtml::_('behavior.tooltip');

		$this->lists      = $lists;
		
		$this->pagination = $this->get('Pagination');
		$this->acl        = MiwoVideos::get('acl');

		parent::display($tpl);
	}

	protected function addToolbar() {
		MToolBarHelper::title(MText::_('COM_MIWOVIDEOS_CPANEL_PROCESSES'), 'miwovideos');

		
		$this->toolbar->appendButton('Popup', 'help1', MText::_('Help'), 'http://miwisoft.com/support/docs/wordpress/miwovideos/user-manual/processes?tmpl=component', 650, 500);
	}
}