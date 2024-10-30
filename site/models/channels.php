<?php
/**
 * @package        MiwoVideos
 * @copyright      Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license        GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die;

class MiwovideosModelChannels extends MiwovideosModel {

	public function __construct() {
		parent::__construct('channels');

		$this->_getUserStates();
		$this->_buildViewQuery();
	}

	public function _getUserStates() {
		$this->filter_order     = parent::_getSecureUserState($this->_option.'.'.$this->_context.'.filter_order', 'filter_order', 'c.title', 'cmd');
		$this->filter_order_Dir = parent::_getSecureUserState($this->_option.'.'.$this->_context.'.filter_order_Dir', 'filter_order_Dir', 'ASC', 'word');
		$this->search           = parent::_getSecureUserState($this->_option.'.'.$this->_context.'.miwovideos_search', 'miwovideos_search', '', 'string');
		$this->search           = MString::strtolower($this->search);
	}


	public function _buildViewQuery() {
		$where = $this->_buildViewWhere();

		if (!empty($this->filter_order) and !empty($this->filter_order_Dir)) {
			$orderby = " ORDER BY {$this->filter_order} {$this->filter_order_Dir}";
		}

		$this->_query = 'SELECT c.*'
		                .' FROM #__miwovideos_channels AS c'
		                .' RIGHT JOIN'
		                .' #__miwovideos_videos AS v'
		                .' ON (v.channel_id = c.id)'
		                .$where
		                .' GROUP BY c.id'
		                .$orderby;
	}

	public function _buildViewWhere() {
		$where = array();
		$user  = MFactory::getUser();

		$where[] = 'c.published=1';
		$where[] = 'c.access IN ('.implode(',', $user->getAuthorisedViewLevels()).')';

		if ($this->_mainframe->getLanguageFilter()) {
			$where[] = 'c.language IN ('.$this->_db->Quote(MFactory::getLanguage()->getTag()).','.$this->_db->Quote('*').')';
		}

		if (!empty($this->search)) {
			$src     = parent::secureQuery($this->search, true);
			$where[] = "(LOWER(c.title) LIKE {$src} OR LOWER(c.introtext) LIKE {$src})";
		}

		$where[] = 'DATE(c.created) <= CURDATE()';

		$where = (count($where) ? ' WHERE '.implode(' AND ', $where) : '');

		return $where;
	}

	public function getTotal() {
		if (empty($this->_total)) {
			$this->_total = MiwoDB::loadResult("SELECT COUNT(DISTINCT c.id) FROM #__miwovideos_{$this->_table} AS c RIGHT JOIN #__miwovideos_videos AS v ON (v.channel_id = c.id)".$this->_buildViewWhere());
		}

		return $this->_total;
	}

	public function getItems() {
		if (empty($this->_data)) {
			$rows = parent::getItems();

			foreach ($rows as $row) {
				$sql = "SELECT COUNT(channel_id) AS subs FROM #__miwovideos_subscriptions WHERE channel_id = {$row->id}";
				$this->_db->setQuery($sql);

				$row->subs = $this->_db->loadResult();

				$sql = "SELECT * FROM #__miwovideos_videos WHERE channel_id = {$row->id} LIMIT 4";
				$this->_db->setQuery($sql);

				$row->videos = $this->_db->loadObjectList();
			}

			$this->_data = $rows;
		}

		return $this->_data;
	}
}