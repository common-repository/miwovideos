<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die ;

class MiwovideosViewPlaylist extends MiwovideosView {

	public function display($tpl = null) {
        $pathway = $this->_mainframe->getPathway();
        if(!MRequest::getInt('channel_id')) {
            //MFactory::getApplication()->redirect(MRoute::_('index.php?option=com_miwovideos'));
        }

        $playlist = $this->get('Item');

        if (is_object($playlist) and !$this->acl->canAccess($playlist->access)) {
            $this->_mainframe->redirect(MRoute::_('index.php?option=com_miwovideos&view=category'), MText::_('JERROR_ALERTNOAUTHOR'), 'error');
        }

        MiwoVideos::get('utility')->hitsCounter('playlist');

        $Itemid = MiwoVideos::get('router')->getItemid(array('view' => 'playlist', 'playlist_id' => $playlist->id), null, true);

        $page_title = $playlist->title;

        if ($this->_mainframe->getCfg('sitename_pagetitles', 0) == 1) {
            $page_title = MText::sprintf('MPAGETITLE', $this->_mainframe->getCfg('sitename'), $page_title);
        }
        elseif ($this->_mainframe->getCfg('sitename_pagetitles', 0) == 2) {
            $page_title = MText::sprintf('MPAGETITLE', $page_title, $this->_mainframe->getCfg('sitename'));
        }

        $this->document->setTitle($page_title);
        $this->document->setMetadata('description', $playlist->meta_desc);
        $this->document->setMetadata('keywords', 	$playlist->meta_key);
        $this->document->setMetadata('author', 		$playlist->meta_author);

        # BreadCrumbs
        $active_menu = $this->_mainframe->getMenu()->getActive();
        if (!isset($active_menu->query['playlist_id']) or ($active_menu->query['playlist_id'] != $playlist->id)) {
            $pathway->addItem($playlist->title);
        }

        $filter_order		= $this->_mainframe->getUserStateFromRequest('com_miwovideos.history.filter_order',         'filter_order',             'title',  'cmd');
        $filter_order_Dir	= $this->_mainframe->getUserStateFromRequest('com_miwovideos.history.filter_order_Dir',     'filter_order_Dir',         'DESC',             'word');
        $search				= $this->_mainframe->getUserStateFromRequest('com_miwovideos.history.search',               'search',                   '',                 'string');
        $display			= $this->_mainframe->getUserStateFromRequest('com_miwovideos.history.display',              'display',                  ''.$this->config->get('listing_style').'',                 'string');
        $search				= MiwoVideos::get('utility')->cleanUrl(MString::strtolower($search));

        $lists = array();
        $lists['search'] = $search;
        $lists['order_Dir'] = $filter_order_Dir;
        $lists['order'] = $filter_order;

        $this->lists                = $lists;
        $this->fields               = MiwoVideos::get('fields')->getVideoFields($playlist->id,"yes");
        $this->display              = $display;
		$this->item                 = $playlist;
		$this->channelitem          = $this->get('ChannelItem');
		$this->items                = $this->getModel('video')->getPlaylistVideos();
        $this->totalvideos          = $this->getModel('video')->getTotalPlaylistVideos();
        $this->checksubscription    = $this->getModel()->checkSubscription($this->channelitem->id, 'channels');
        $this->checklikesdislikes   = $this->getModel()->checkLikesDislikes($playlist->id, 'playlists');
		//$this->pagination           = $this->get('Pagination');
        $this->params               = $this->_mainframe->getParams();
        $this->Itemid               = $Itemid;
		
		parent::display($tpl);				
	}	
}