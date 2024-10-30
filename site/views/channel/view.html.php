<?php
/**
 * @package        MiwoVideos
 * @copyright      Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license        GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die;

class MiwovideosViewChannel extends MiwovideosView {

	public function display($tpl = null) {
		$pathway = $this->_mainframe->getPathway();

		$channel = $this->get('Item');

		if (is_object($channel) and !$this->acl->canAccess($channel->access)) {
			$this->_mainframe->redirect(MRoute::_('index.php?option=com_miwovideos&view=category'), MText::_('JERROR_ALERTNOAUTHOR'), 'error');
		}

		MiwoVideos::get('utility')->hitsCounter('channel');

		$Itemid = MiwoVideos::get('router')->getItemid(array('view' => 'channel', 'channel_id' => $channel->id), null, true);

		$channel->description = $channel->introtext.$channel->fulltext;

		$page_title = $channel->title;

		if ($this->_mainframe->getCfg('sitename_pagetitles', 0) == 1) {
			$page_title = MText::sprintf('MPAGETITLE', $this->_mainframe->getCfg('sitename'), $page_title);
		}
		elseif ($this->_mainframe->getCfg('sitename_pagetitles', 0) == 2) {
			$page_title = MText::sprintf('MPAGETITLE', $page_title, $this->_mainframe->getCfg('sitename'));
		}

		$this->document->setTitle($page_title);
		$this->document->setMetadata('description', $channel->meta_desc);
		$this->document->setMetadata('keywords', $channel->meta_key);
		$this->document->setMetadata('author', $channel->meta_author);

		# BreadCrumbs
		$active_menu = $this->_mainframe->getMenu()->getActive();
		if (!isset($active_menu->query['channel_id']) or ($active_menu->query['channel_id'] != $channel->id)) {
			$pathway->addItem($channel->title);
		}

		$filter_videos    = $this->_mainframe->getUserStateFromRequest('com_miwovideos.history.filter_videos', 'filter_videos', 'uploads', 'string');
		$filter_order     = $this->_mainframe->getUserStateFromRequest('com_miwovideos.history.filter_order', 'filter_order', MiwoVideos::getConfig()->get('order_videos', 'v.title'), 'cmd');
		$filter_order_Dir = $this->_mainframe->getUserStateFromRequest('com_miwovideos.history.filter_order_Dir', 'filter_order_Dir', 'DESC', 'word');
		$search           = $this->_mainframe->getUserStateFromRequest('com_miwovideos.history.miwovideos_search', 'miwovideos_search', '', 'string');
		$display          = $this->_mainframe->getUserStateFromRequest('com_miwovideos.history.display', 'display', ''.$this->config->get('listing_style').'', 'string');
		$search           = MiwoVideos::get('utility')->cleanUrl(MString::strtolower($search));

		$pagenum     = MiwoVideos::getInput()->getInt('page', 1, 'post');
		$rowsperpage = $this->config->get('videos_per_page');
		$offset      = ($pagenum - 1) * $rowsperpage;

		$options   = array();
		$options[] = MHtml::_('select.option', 'uploads', MText::_('COM_MIWOVIDEOS_UPLOADS'));
		if ($this->config->get('playlists')) {
			$options[] = MHtml::_('select.option', 'playlists', MText::_('COM_MIWOVIDEOS_PLAYLISTS'));
		}

		$lists                  = array();
		$lists['filter_videos'] = MHtml::_('select.genericlist', $options, 'filter_videos', ' class="inputbox" style="width: 100px;" onchange="miwovideossubmit();" ', 'value', 'text', $filter_videos);

		$this->fields            = MiwoVideos::get('fields')->getVideoFields($channel->id, "yes");
		$this->display           = $filter_videos.'_'.$display;
		$this->item              = $channel;
		$this->params            = $this->_mainframe->getParams();
		$this->Itemid            = $Itemid;
		$this->checksubscription = $this->getModel()->checkSubscription($channel->id, 'channels');

		$options = array();
		switch ($filter_videos) {
			case 'uploads' :
				$this->getModel()->setState('limitstart', $offset);
				$this->getModel()->setState('limit', $rowsperpage);
				$this->total_items = $this->get('ChannelVideosTotal');
				$this->items       = $this->get('ChannelVideos');
				$this->total_pages = ceil($this->total_items / $rowsperpage);
				$options[]         = MHtml::_('select.option', 'v.title_asc', MText::_('COM_MIWOVIDEOS_TITLE_AZ'));
				$options[]         = MHtml::_('select.option', 'v.title_desc', MText::_('COM_MIWOVIDEOS_TITLE_ZA'));
				$options[]         = MHtml::_('select.option', 'v.created_asc', MText::_('COM_MIWOVIDEOS_DATE_CREATED_O_N'));
				$options[]         = MHtml::_('select.option', 'v.created_desc', MText::_('COM_MIWOVIDEOS_DATE_CREATED_N_O'));
				$options[]         = MHtml::_('select.option', 'v.hits_desc', MText::_('COM_MIWOVIDEOS_MOST_POPULAR'));
				$options[]         = MHtml::_('select.option', 'v.likes_desc', MText::_('COM_MIWOVIDEOS_MOST_FAVORITE'));
				$options[] = MHtml::_('select.option', 'v.week_desc', MText::_('COM_MIWOVIDEOS_THIS_WEEK'));
				$options[] = MHtml::_('select.option', 'v.month_desc', MText::_('COM_MIWOVIDEOS_THIS_MONTH'));
				break;
			case 'playlists' :
				$this->getModel('playlists')->setState('limitstart', $offset);
				$this->getModel('playlists')->setState('limit', $rowsperpage);
				MRequest::setVar('channel_id', $channel->id, 'post');
				$this->total_items = $this->getModel('playlists')->getTotal();
				$this->items       = $this->getModel('playlists')->getChannelPlaylists();
				$this->total_pages = ceil($this->total_items / $rowsperpage);
				$options[]         = MHtml::_('select.option', 'p.title_asc', MText::_('COM_MIWOVIDEOS_TITLE_AZ'));
				$options[]         = MHtml::_('select.option', 'p.title_desc', MText::_('COM_MIWOVIDEOS_TITLE_ZA'));
				$options[]         = MHtml::_('select.option', 'p.created_asc', MText::_('COM_MIWOVIDEOS_DATE_CREATED_O_N'));
				$options[]         = MHtml::_('select.option', 'p.created_desc', MText::_('COM_MIWOVIDEOS_DATE_CREATED_N_O'));
				$options[]         = MHtml::_('select.option', 'p.hits_desc', MText::_('COM_MIWOVIDEOS_MOST_POPULAR'));
				$options[]         = MHtml::_('select.option', 'p.likes_desc', MText::_('COM_MIWOVIDEOS_MOST_FAVORITE'));
				$options[] = MHtml::_('select.option', 'p.week_desc', MText::_('COM_MIWOVIDEOS_THIS_WEEK'));
				$options[] = MHtml::_('select.option', 'p.month_desc', MText::_('COM_MIWOVIDEOS_THIS_MONTH'));
				break;
		}

		$lists['order']     = MHtml::_('select.genericlist', $options, 'filter_order', ' class="inputbox" style="width: 200px;" onchange="miwovideossubmit();" ', 'value', 'text', $filter_order.'_'.strtolower($filter_order_Dir));
		$lists['search']    = $search;
		$lists['order_Dir'] = $filter_order_Dir;

		$this->lists = $lists;

		if ($pagenum > 1) {
			ob_start();
			parent::display($filter_videos.'_'.$display);
			$result = ob_get_contents();
			ob_end_clean();
			echo json_encode($result);
			exit();
		}
		else {
			parent::display($tpl);
		}
	}
}