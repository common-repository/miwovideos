<?php
/**
 * @package        MiwoVideos
 * @copyright      Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license        GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die;

class MiwovideosViewCategories extends MiwovideosView {

	public function display($tpl = null) {
		$page_title = MText::_('COM_MIWOVIDEOS_CATEGORIES_PAGE_TITLE');

		if ($this->_mainframe->getCfg('sitename_pagetitles', 0) == 1) {
			$page_title = MText::sprintf('MPAGETITLE', $this->_mainframe->getCfg('sitename'), $page_title);
		}
		elseif ($this->_mainframe->getCfg('sitename_pagetitles', 0) == 2) {
			$page_title = MText::sprintf('MPAGETITLE', $page_title, $this->_mainframe->getCfg('sitename'));
		}

		$this->document->setTitle($page_title);

		MHtml::_('behavior.modal');

		$lists['search'] = $this->_mainframe->getUserStateFromRequest('com_miwovideos.categories.miwovideos_search', 'miwovideos_search', '', 'string');

		$this->lists      = $lists;
		$this->pagination = $this->get('Pagination');
		$this->items      = $this->get('Items');
		$this->display    = $this->_mainframe->getUserStateFromRequest('com_miwovideos.history.display', 'display', ''.$this->config->get('listing_style', 'list').'', 'string');
		$this->Itemid     = MiwoVideos::get('router')->getItemid(array('view' => 'categories'), null, true);
		$this->params     = $this->_mainframe->getParams();

		parent::display($tpl);
	}
}