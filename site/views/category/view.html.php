<?php
/**
 * @package        MiwoVideos
 * @copyright      Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license        GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die;

class MiwovideosViewCategory extends MiwovideosView {

	public function display($tpl = null) {
		$pathway  = $this->_mainframe->getPathway();
		$category_id = MiwoVideos::getInput()->getInt('category_id', 0);
		if(!$category_id) {
			MFactory::getApplication()->redirect(MRoute::_('index.php?option=com_miwovideos&view=categories'));
		}

		$category    = Miwovideos::get('utility')->getCategory($category_id);

		if (is_object($category) and !$this->acl->canAccess($category->access)) {
			$this->_mainframe->redirect(MRoute::_('index.php?option=com_miwovideos&view=category'), MText::_('JERROR_ALERTNOAUTHOR'), 'error');
		}

		$Itemid = MiwoVideos::get('router')->getItemid(array('view' => 'category', 'category_id' => $category_id), null, true);
		$this->getModel()->setState($this->_option.'.videos.limit', $this->config->get('videos_per_page'));

		$videos     = $this->get('Videos');
		$categories = $this->get('Categories');

		$page_title = MText::_('COM_MIWOVIDEOS_CATEGORY_PAGE_TITLE');
		$page_title = str_replace('[CATEGORY_NAME]', $category->title, $page_title);

		if ($this->_mainframe->getCfg('sitename_pagetitles', 0) == 1) {
			$page_title = MText::sprintf('MPAGETITLE', $this->_mainframe->getCfg('sitename'), $page_title);
		}
		elseif ($this->_mainframe->getCfg('sitename_pagetitles', 0) == 2) {
			$page_title = MText::sprintf('MPAGETITLE', $page_title, $this->_mainframe->getCfg('sitename'));
		}

		$this->document->setTitle($page_title);
		$this->document->setMetadata('description', $category->meta_desc);
		$this->document->setMetadata('keywords', $category->meta_key);
		$this->document->setMetadata('author', $category->meta_author);


		if ($this->config->get('load_plugins')) {
			$n = count($videos);

			for ($i = 0; $i < $n; $i++) {
				$item = &$videos[ $i ];

				$item->introtext = MHtml::_('content.prepare', $item->introtext);
			}

			if ($category) {
				$category->description = MHtml::_('content.prepare', $category->introtext.$category->fulltext);
			}
		}

		# BreadCrumbs
		$active_menu = $this->_mainframe->getMenu()->getActive();
		if (!isset($active_menu->query['category_id']) or ($active_menu->query['category_id'] != $category_id)) {
			$cats = Miwovideos::get('utility')->getCategories($category_id);

			if (!empty($cats)) {
				asort($cats);

				foreach ($cats as $cat) {
					if ($cat->id != $category_id) {
						$Itemid = MiwoVideos::get('router')->getItemid(array('view' => 'category', 'category_id' => $cat->id), null, true);

						$path_url = MRoute::_('index.php?option=com_miwovideos&view=category&category_id='.$cat->id.$Itemid);
						$pathway->addItem($cat->title, $path_url);
					}
				}

				$pathway->addItem($category->title);
			}
		}

		$lists['search'] = $this->_mainframe->getUserStateFromRequest('com_miwovideos.categories.miwovideos_search', 'miwovideos_search', '', 'string');

		MHtml::_('behavior.modal');

		$this->pagination  = $this->get('Pagination');
		$this->lists       = $lists;
		$this->items       = $videos;
		$this->categories  = $categories;
		$this->display     = $this->_mainframe->getUserStateFromRequest('com_miwovideos.history.display', 'display', ''.$this->config->get('listing_style', 'list').'', 'string');
		$this->Itemid      = $Itemid;
		$this->category    = $category;
		$this->params      = $this->_mainframe->getParams();

		parent::display($tpl);
	}
}