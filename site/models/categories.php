<?php
/**
 * @package        MiwoVideos
 * @copyright      Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license        GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die;

class MiwovideosModelCategories extends MiwovideosModel {

	public function __construct() {
		parent::__construct('categories');

		$this->_getUserStates();
		$this->_buildViewQuery();
	}

	public function _buildViewQuery() {
		$where = $this->_buildViewWhere();

		if (MiwoVideos::getConfig()->get('show_empty_cat')) {
			$this->_query = 'SELECT c.* FROM #__miwovideos_categories AS c '.$where.' ORDER BY c.ordering';
		}
		else {
			$this->_query = 'SELECT DISTINCT c.* FROM #__miwovideos_categories AS c RIGHT JOIN #__miwovideos_video_categories AS vc ON (c.id = vc.category_id) '.$where.' ORDER BY c.ordering';
		}
	}

	public function _getUserStates() {
		$this->search = parent::_getSecureUserState($this->_option.'.'.$this->_context.'.miwovideos_search', 'miwovideos_search', '', 'string');
		$this->search = MString::strtolower($this->search);
	}

	public function _buildViewWhere() {

		$where = array();

		if (!empty($this->search)) {
			$src     = parent::secureQuery($this->search, true);
			$where[] = "(LOWER(c.title) LIKE {$src} OR LOWER(c.introtext) OR LOWER(c.fulltext) LIKE {$src})";
		}

		$where[] = 'c.id <> 1';
		$where[] = 'c.parent = 0';
		$where[] = 'c.access IN ('.implode(',', MFactory::getUser()->getAuthorisedViewLevels()).')';
		$where[] = 'c.published = 1';

		if ($this->_mainframe->getLanguageFilter()) {
			$where[] = 'c.language IN ('.$this->_db->Quote(MFactory::getLanguage()->getTag()).','.$this->_db->Quote('*').')';
		}

		$where = (count($where) ? ' WHERE '.implode(' AND ', $where) : '');

		return $where;
	}

	public function getTotal() {
		if (!empty($this->_total)) {
			return $this->_total;
		}

		if (MiwoVideos::getConfig()->get('show_empty_cat')) {
			$this->_total = MiwoDB::loadResult("SELECT COUNT(*) FROM #__miwovideos_categories AS c".$this->_buildViewWhere());
		}
		else {
			$this->_total = MiwoDB::loadResult("SELECT COUNT(*) FROM #__miwovideos_categories AS c RIGHT JOIN #__miwovideos_video_categories AS vc ON (c.id = vc.category_id)".$this->_buildViewWhere());
		}

		return $this->_total;
	}

	public function getItems() {
		$rows = parent::getItems();

		foreach ($rows as $row) {
			$row->total_categories = MiwoDB::loadResult('SELECT COUNT(*) FROM #__miwovideos_categories WHERE parent = '.$row->id.' AND published = 1');
			$row->total_videos     = MiwoVideos::get('videos')->getTotalVideosByCategory($row->id);
		}

		return $rows;
	}
} 