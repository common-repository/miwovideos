<?php
/**
 * @package        MiwoVideos
 * @copyright      Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license        GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die;

class MiwovideosViewVideo extends MiwovideosView {

	public function __construct($config = array()) {
		parent::__construct($config);
	}

	public function display($tpl = null) {
		$user    = MFactory::getUser();
		$user_id = $user->get('id');

		$pathway = $this->_mainframe->getPathway();

		$item = $this->get('Data');

		# OpenGraph Tag
		if (MFactory::getDocument()->getType() != 'raw') {
			MFactory::getDocument()->addCustomTag('<meta property="og:image" content="'.MiwoVideos::get('utility')->getThumbPath($item->id, 'videos', $item->thumb).'" />');
			MFactory::getDocument()->addCustomTag('<meta property="og:description" content="'.$item->introtext.$item->fulltext.'" />');
		}

		if (is_object($item) and !$this->acl->canAccess($item->access)) {
			$this->_mainframe->redirect(MRoute::_('index.php?option=com_miwovideos&view=category'), MText::_('JERROR_ALERTNOAUTHOR'), 'error');
		}

		MiwoVideos::get('utility')->hitsCounter('video');

		$Itemid = MiwoVideos::get('router')->getItemid(array('view' => 'video', 'video_id' => $item->id), null, true);
		if (empty($Itemid)) {
			$Itemid = MiwoVideos::get('router')->getItemid(array('view' => 'category', 'category_id' => $item->category_id), null, true);

			if (empty($Itemid)) {
				$Itemid = MiwoVideos::get('router')->getItemid(array('view' => 'channel', 'channel_id' => $item->channel_id), null, true);
			}
		}

		$item->description = $item->introtext.$item->fulltext;

		$category = Miwovideos::get('utility')->getCategory($item->category_id);

		if ($this->config->get('load_plugins')) {
			$item->description = MHtml::_('content.prepare', $item->description);
		}

		# BreadCrumbs
		$active_menu = $this->_mainframe->getMenu()->getActive();
		if (!isset($active_menu->query['video_id']) or ($active_menu->query['video_id'] != $item->id)) {
			$cats = Miwovideos::get('utility')->getCategories($item->category_id);

			if (!empty($cats)) {
				$break = false;
				foreach ($cats as $cat) {
					if (isset($active_menu->query['category_id']) and $active_menu->query['category_id'] == $cat->id) {
						$break = true;
					}
				}

				if (!$break) {
					asort($cats);
					foreach ($cats as $cat) {
						if (isset($active_menu->query['category_id']) and array_search($active_menu->query['category_id'], $cats)) {
							break;
						}
						$Itemid = MiwoVideos::get('router')->getItemid(array('view' => 'category', 'category_id' => $cat->id), null, true);

						$path_url = MRoute::_('index.php?option=com_miwovideos&view=category&category_id='.$cat->id.$Itemid);
						$pathway->addItem($cat->title, $path_url);
					}
				}

				$pathway->addItem($item->title);
			}
		}

		if ($item->channel_id) {
			$this->channels = "";
		}

		$page_title = MText::_('COM_MIWOVIDEOS_VIDEO_PAGE_TITLE');
		$page_title = str_replace('[VIDEO_TITLE]', $item->title, $page_title);
		$page_title = str_replace('[CATEGORY_NAME]', $category->title, $page_title);

		if ($this->_mainframe->getCfg('sitename_pagetitles', 0) == 1) {
			$page_title = MText::sprintf('MPAGETITLE', $this->_mainframe->getCfg('sitename'), $page_title);
		}
		elseif ($this->_mainframe->getCfg('sitename_pagetitles', 0) == 2) {
			$page_title = MText::sprintf('MPAGETITLE', $page_title, $this->_mainframe->getCfg('sitename'));
		}

		$this->document->setTitle($page_title);
		$this->document->setMetaData('keywords', $item->meta_key);
		$this->document->setMetaData('description', $item->meta_desc);
		$this->document->setMetadata('author', $item->meta_author);

		MHtml::_('behavior.modal');

		$filter_order     = $this->_mainframe->getUserStateFromRequest('com_miwovideos.playlists.filter_order', 'filter_order', 'p.created', 'cmd');
		$filter_order_Dir = $this->_mainframe->getUserStateFromRequest('com_miwovideos.playlists.filter_order_Dir', 'filter_order_Dir', 'DESC', 'word');

		$options   = array();
		$options[] = MHtml::_('select.option', 'p.title_asc', MText::_('COM_MIWOVIDEOS_TITLE_AZ'));
		$options[] = MHtml::_('select.option', 'p.title_desc', MText::_('COM_MIWOVIDEOS_TITLE_ZA'));
		$options[] = MHtml::_('select.option', 'p.access', MText::_('COM_MIWOVIDEOS_ACCESS'));
		$options[] = MHtml::_('select.option', 'p.created_asc', MText::_('COM_MIWOVIDEOS_DATE_CREATED_O_N'));
		$options[] = MHtml::_('select.option', 'p.created_desc', MText::_('COM_MIWOVIDEOS_DATE_CREATED_N_O'));

		$lists                 = array();
		$lists['filter_order'] = MHtml::_('select.genericlist', $options, 'filter_order', ' class="inputbox" style="width: 150px; margin:0;" onchange="ajaxOrder();" ', 'value', 'text', $filter_order.'_'.strtolower($filter_order_Dir));


		$options   = array();
		$reasons   = $this->get('Reasons');
		$options[] = MHtml::_('select.option', '', MText::_('COM_MIWOVIDEOS_SELECT'));
		foreach ($reasons as $reason) {
			$options[] = MHtml::_('select.option', $reason->id, $reason->title);
		}


		$lists['reasons'] = MHtml::_('select.genericlist', $options, 'miwovideos_reasons', ' class="inputbox" style="width: 150px; margin:0;"" ', 'value', 'text');

		

		$this->lists              = $lists;
		$this->item               = $item;
		$this->item->channel      = MiwoVideos::get('channels')->getChannel($item->channel_id);
		$this->item->categories   = $this->get('VideoCategories');
		$this->playlistitems      = $this->getModel('playlists')->getChannelPlaylists();
		$this->view_levels        = $user->getAuthorisedViewLevels();
		$this->checksubscription  = $this->getModel()->checkSubscription($item->channel_id, 'channels');
		$this->checklikesdislikes = $this->getModel()->checkLikesDislikes($item->id, 'videos');
		$this->reasons            = $reasons;
		$this->report             = $this->get('Report');
		$this->Itemid             = $Itemid;
		$this->userId             = $user_id;
		$this->nullDate           = MFactory::getDBO()->getNullDate();
		$this->tmpl               = MRequest::getCmd('tmpl');
		$this->params             = $this->_mainframe->getParams();
		$this->fields             = MiwoVideos::get('fields')->getVideoFields($item->id, "yes");
		

		if (!empty($item->playlist_id)) {
			$this->playlistvideos = $this->getModel('playlists')->_playlistVideos($item->playlist_id);
		}

		if ($this->getModel()->getProcessing($item->id) > 0) {
			MError::raiseNotice('100', MText::_('COM_MIWOVIDEOS_STILL_PROCESSING'));
		}

		if (MiwoVideos::is31()) {
			$this->item->tags = MiwoVideos::get('videos')->getTags($this->item->id);
		}

		if (MRequest::getWord('task') == "ajaxOrder") {
			$json = array();
			if ($user_id) {
				$html = "";
				foreach ($this->playlistitems as $item) {
					$style           = $id = "";
					$playlist_videos = array();
					foreach ($item->videos as $video) {
						$playlist_videos[] = $video->video_id;
					}
					if (!in_array($this->item->id, $playlist_videos)) {
						$style = "visibility: hidden";
					}
					if ($item->type == 1) {
						$id = "type1";
					}
					$html .= "<li class=\"miwovideos_playlist_item\">\r";
					$html .= "   <a class=\"playlist_item\" id=\"playlist_item".$item->id."\">\r";
					$html .= "       <img src=\"".MURL_MIWOVIDEOS."/site/assets/images/tick.png\" style=\"".$style."\"/>\r";
					$html .= "       <span class=\"miwovideos_playlist_title\" id=\"".$id."\">".$item->title."&nbsp;(<span id=\"total_videos\">".$item->total."</span>)</span>\r";
					
					$html .= "       <span class=\"miwovideos_playlist_created\">".MiwoVideos::agoDateFormat($item->created)."</span>\r";
					$html .= "    </a>\r";
					$html .= "</li>\r";
				}
				$json['html'] = $html;
			}
			else {
				$json['redirect'] = MiwoVideos::get('utility')->redirectWithReturn();
			}
			echo json_encode($json);
			exit();
		}
		else {
			parent::display($tpl);
		}
	}

	public function displayPlayer($tpl = null) {
		$this->item = $this->get('Data');
		MiwoVideos::get('utility')->hitsCounter('video');

		parent::display('player');
	}
}