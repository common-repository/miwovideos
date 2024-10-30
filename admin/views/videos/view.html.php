<?php
/**
 * @package        MiwoVideos
 * @copyright      Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license        GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die;


class MiwovideosViewVideos extends MiwovideosView {

	public function displayModal($tpl = null) {
		$this->display($tpl);
	}

	public function display($tpl = null) {
		if ($this->_mainframe->isAdmin()) {
			$this->addToolbar();
		}

		$filter_order     = $this->_mainframe->getUserStateFromRequest($this->_option.'.videos.video_filter_order', 'filter_order', 'v.title', 'cmd');
		$filter_order_Dir = $this->_mainframe->getUserStateFromRequest($this->_option.'.videos.filter_order_Dir', 'filter_order_Dir', '', 'word');
		$filter_published = $this->_mainframe->getUserStateFromRequest($this->_option.'.videos.filter_published', 'filter_published', '');
		$filter_category  = $this->_mainframe->getUserStateFromRequest($this->_option.'.videos.filter_category', 'filter_category', 0, 'int');
		$filter_channel   = $this->_mainframe->getUserStateFromRequest($this->_option.'.videos.filter_channel', 'filter_channel', 0, 'int');
		$filter_access    = $this->_mainframe->getUserStateFromRequest($this->_option.'.videos.filter_access', 'filter_access', '');
		$filter_language  = $this->_mainframe->getUserStateFromRequest($this->_option.'.videos.filter_language', 'filter_language', '', 'string');
		$search           = $this->_mainframe->getUserStateFromRequest($this->_option.'.videos.search', 'search', '', 'string');
		$search           = MString::strtolower($search);

		$lists['filter_category'] = MiwoVideos::get('utility')->buildCategoryDropdown($filter_category, 'filter_category', true);

		/*$channels = $this->get('Channels');
		$options = array();
		$options[] = MHtml::_('select.option', '', MText::_('COM_MIWOVIDEOS_SELECT_CHANNEL'));
		foreach($channels as $channel){
			$options[] = MHtml::_('select.option',  $channel->id, $channel->title);
		}
		$lists['filter_channel'] = MHtml::_('select.genericlist', $options, 'filter_channel', ' class="inputbox" style="width: 220px;"  ', 'value', 'text', $filter_channel);
		*/

		$lists['search']    = $search;
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order']     = $filter_order;

		$items = $this->get('Items');

		$pagination = $this->get('Pagination');

		$options                   = array();
		$options[]                 = MHtml::_('select.option', '', MText::_('COM_MIWOVIDEOS_SELECT_STATUS'));
		$options[]                 = MHtml::_('select.option', 1, MText::_('COM_MIWOVIDEOS_PUBLISHED'));
		$options[]                 = MHtml::_('select.option', 0, MText::_('COM_MIWOVIDEOS_UNPUBLISHED'));
		$lists['filter_published'] = MHtml::_('select.genericlist', $options, 'filter_published', ' class="inputbox" style="width: 140px;"  ', 'value', 'text', $filter_published);

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
			


		MHtml::_('behavior.tooltip');

		$this->lists           = $lists;
		$this->items           = $items;
		$this->pagination      = $pagination;
		
		$this->langs           = MiwoVideos::get('utility')->getLanguages();
		$this->filter_language = $filter_language;
		$this->filter_access   = $filter_access;

		parent::display($tpl);
	}

	protected function addToolbar() {
		MToolBarHelper::title(MText::_('COM_MIWOVIDEOS_CPANEL_VIDEOS'), 'miwovideos');

		if ($this->acl->canCreate()) {
			//$this->toolbar->appendButton('link', 'new', 'JTOOLBAR_NEW', MiwoVideos::get('utility')->route('index.php?option=com_miwovideos&view=upload'));
			MToolBarHelper::addNew();
		}

		
		$this->toolbar->appendButton('Popup', 'help1', MText::_('Help'), 'http://miwisoft.com/support/docs/wordpress/miwovideos/user-manual/fields?tmpl=component', 650, 500);
	}
}