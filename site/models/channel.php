<?php
/**
 * @package        MiwoVideos
 * @copyright      Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license        GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die;

class MiwovideosModelChannel extends MiwovideosModel {

	public function __construct() {
		parent::__construct('channel', 'channels');

		$this->_getUserStates();
		$this->_buildViewQuery();
	}

	public function _getUserStates() {
		$this->filter_order     = parent::_getSecureUserState($this->_option.'.'.$this->_context.'.filter_order', 'filter_order', MiwoVideos::getConfig()->get('order_videos', 'v.title'), 'string');
		$this->filter_order_Dir = parent::_getSecureUserState($this->_option.'.'.$this->_context.'.filter_order_Dir', 'filter_order_Dir', 'DESC', 'word');
		$this->search           = parent::_getSecureUserState($this->_option.'.'.$this->_context.'.miwovideos_search', 'miwovideos_search', '', 'string');
		$this->search           = MString::strtolower($this->search);
	}

	public function getItem() {
		$channel_id = MRequest::getInt('channel_id');

		$row = MiwoVideos::getTable('MiwovideosChannels');
		$row->load($channel_id);

		$row->subs = MiwoVideos::get('model')->getSubscriberCount($channel_id);

		return $row;
	}

	public function getChannelVideos() {
		$query = $this->_buildChannelVideosQuery();
		$this->_db->setQuery($query);

		$rows = MiwoDB::loadObjectList($query, '', $this->getState('limitstart'), $this->getState('limit'));

		return $rows;
	}

	public function _buildChannelVideosQuery() {
		$where = $this->_buildChannelVideosWhere();

		$query = 'SELECT v.*, c.title channel_title'
		         .' FROM  #__miwovideos_videos AS v '
		         .' LEFT JOIN #__miwovideos_channels AS c '
		         .' ON (c.id = v.channel_id)'
		         .$where
		         .' GROUP BY v.id ';

		if (!empty($this->filter_order) and !empty($this->filter_order_Dir)) {
			$this->filter_order_Dir = str_replace('_oldest', '', $this->filter_order_Dir);
			$this->filter_order_Dir = str_replace('_newest', '', $this->filter_order_Dir);
			switch ($this->filter_order) {
				case 'v.week' :
				case 'v.month' :
				case 'v.created_oldest' :
				case 'v.created_newest' :
					$query .= " ORDER BY v.created {$this->filter_order_Dir}";
					break;
				default :
					$query .= " ORDER BY {$this->filter_order} {$this->filter_order_Dir}";
					break;
			}
		}

		return $query;
	}

	public function _buildChannelVideosWhere() {
		$db         = MFactory::getDbo();
		$user       = MFactory::getUser();
		$app        = MFactory::getApplication();
		$channel_id = MRequest::getInt('channel_id', 0);

		$where = array();

		$where[] = 'v.published = 1';
		$where[] = 'v.access IN ('.implode(',', $user->getAuthorisedViewLevels()).')';

		if ($channel_id) {
			$where[] = 'v.channel_id = '.$channel_id;
		}

		$where[] = 'DATE(v.created) <= CURDATE()';

		if ($app->getLanguageFilter()) {
			$where[] = 'v.language IN ('.$db->Quote(MFactory::getLanguage()->getTag()).','.$db->Quote('*').')';
		}

		if (!empty($this->search)) {
			$src     = parent::secureQuery($this->search, true);
			$where[] = "(LOWER(v.title) LIKE {$src} OR LOWER(v.introtext) LIKE {$src})";
		}

		if (!empty($this->filter_order)) {
			switch ($this->filter_order) {
				case 'v.week' :
					$where[] = 'YEARWEEK(v.created) = YEARWEEK(curdate())';
					break;
				case 'v.month' :
					$where[] = 'MONTH(v.created) = MONTH(CURDATE()) AND YEAR(v.created) = YEAR(CURDATE())';
					break;
			}
		}

		$where = (count($where) ? ' WHERE '.implode(' AND ', $where) : '');

		return $where;
	}

	public function getChannelVideosPagination() {
		mimport('framework.html.pagination');
		$pagination = new MPagination($this->getChannelVideosTotal(), $this->getState($this->_option.'.'.$this->_context.'.limitstart'), $this->getState($this->_option.'.'.$this->_context.'.limit'));

		return $pagination;
	}

	public function getChannelVideosTotal() {
		$total = MiwoDB::loadResult('SELECT COUNT(*) FROM #__miwovideos_videos AS v '.$this->_buildChannelVideosWhere());

		return $total;
	}
} 