<?php
/**
 * @package        MiwoVideos
 * @copyright      Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license        GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die;

class MiwovideosModelCategory extends MiwovideosModel {

	public function __construct() {
		parent::__construct('category', 'videos');

		if (MRequest::getVar('limitstart', 0, '', 'int') == 0) MRequest::setVar('limitstart', 0);
		$limit      = $this->_mainframe->getUserStateFromRequest($this->_option.'.'.$this->_context.'.limit', 'limit', $this->config->get('videos_per_page'), 'int');
		$limitstart = $this->_mainframe->getUserStateFromRequest($this->_option.'.'.$this->_context.'.limitstart', 'limitstart', 0, 'int');

		# Limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState($this->_option.'.'.$this->_context.'.limit', $limit);
		$this->setState($this->_option.'.'.$this->_context.'.limitstart', $limitstart);

		$this->_getUserStates();
		$this->_buildViewQuery();
	}

	public function _getUserStates() {
		$this->search           = parent::_getSecureUserState($this->_option.'.'.$this->_context.'.miwovideos_search', 'miwovideos_search', '', 'string');
		$this->search           = MString::strtolower($this->search);
	}

	public function _buildViewQuery() {
		$where = $this->_buildViewWhere();

		$orderby = MiwoVideos::getConfig()->get('order_videos', 'v.title');
		if ($orderby == 'v.hits' || $orderby == 'v.likes' || $orderby == 'v.created_newest') {
			$order_dir = ' DESC';
		}
		elseif ($orderby == 'v.created_oldest') {
			$order_dir = ' ASC';
		}
		else {
			$order_dir = ' ASC';
		}

		$orderby = str_replace('_oldest', '', $orderby);
		$orderby = str_replace('_newest', '', $orderby);

		$this->_query = 'SELECT v.*, c.title channel_title, c.id channel_id'
		                .' FROM #__miwovideos_videos AS v '
		                .' LEFT JOIN #__miwovideos_channels AS c '
		                .' ON (c.id = v.channel_id)'
		                .$where
		                .' GROUP BY v.id '
		                .' ORDER BY '.$orderby.$order_dir;
	}

	public function _buildViewWhere() {
		$category_id = MRequest::getInt('category_id');

		$where = array();

		$where[] = 'v.published = 1';
		$where[] = 'v.access IN ('.implode(',', MFactory::getUser()->getAuthorisedViewLevels()).')';

		if (!empty($this->search)) {
			$src     = parent::secureQuery($this->search, true);
			$where[] = "(LOWER(v.title) LIKE {$src} OR LOWER(v.introtext) OR LOWER(v.fulltext) LIKE {$src})";
		}

		if ($this->_mainframe->getLanguageFilter()) {
			$where[] = 'v.language IN ('.$this->_db->Quote(MFactory::getLanguage()->getTag()).','.$this->_db->Quote('*').')';
		}

		if ($category_id) {
			$where[] = 'v.id IN (SELECT video_id FROM #__miwovideos_video_categories WHERE category_id='.$category_id.')';
		}

		$where[] = 'DATE(v.created) <= CURDATE()';


		$where = (count($where) ? ' WHERE '.implode(' AND ', $where) : '');

		return $where;
	}

	public function getVideos() {
		if (empty($this->_data)) {
			$this->_data = MiwoDB::loadObjectList($this->_query, '', $this->getState($this->_option.'.'.$this->_context.'.limitstart'), $this->getState($this->_option.'.'.$this->_context.'.limit'));
		}

		return $this->_data;
	}

	public function getTotal() {
		$this->_total = MiwoDB::loadResult("SELECT COUNT(*) FROM #__miwovideos_{$this->_table} AS v".$this->_buildViewWhere());
		return $this->_total;
	}

	public function _buildCategoriesWhere() {
		$category_id = MiwoVideos::getInput()->getInt('category_id');

		$where = array();

		$where[] = 'c.id <> 1';
		$where[] = 'c.parent = '.$category_id;
		$where[] = 'c.access IN ('.implode(',', MFactory::getUser()->getAuthorisedViewLevels()).')';
		$where[] = 'c.published = 1';

		if ($this->_mainframe->getLanguageFilter()) {
			$where[] = 'c.language IN ('.$this->_db->Quote(MFactory::getLanguage()->getTag()).','.$this->_db->Quote('*').')';
		}

		$where = (count($where) ? ' WHERE '.implode(' AND ', $where) : '');

		return $where;
	}

	public function getCategories() {
		$c_id = MRequest::getCmd('category_id');
		if (empty($c_id)) {
			$rows = MiwoDB::loadObjectList($this->_buildCategoriesQuery()/*, '', $this->getState($this->_option.'.'.$this->_context.'.limitstart'), $this->getState($this->_option.'.'.$this->_context.'.limit')*/);
		}
		else {
			$rows = MiwoDB::loadObjectList($this->_buildCategoriesQuery());
		}

		foreach ($rows as $row) {
			$row->total_categories = MiwoDB::loadResult('SELECT COUNT(*) FROM #__miwovideos_categories WHERE parent = '.$row->id.' AND published = 1');
			$row->total_videos     = MiwoVideos::get('videos')->getTotalVideosByCategory($row->id);
		}

		return $rows;
	}

	public function _buildCategoriesQuery() {
		$where = $this->_buildCategoriesWhere();

		if (MiwoVideos::getConfig()->get('show_empty_cat')) {
			$query = 'SELECT c.* FROM #__miwovideos_categories AS c '.$where.' ORDER BY c.ordering';
		}
		else {
			$query = 'SELECT DISTINCT c.* FROM #__miwovideos_categories AS c RIGHT JOIN #__miwovideos_video_categories AS vc ON (c.id = vc.category_id) '.$where.' ORDER BY c.ordering';
		}

		return $query;
	}
} 