<?php

class MiwovideosModelPopular extends MiwovideosModel {

	public function __construct() {
		parent::__construct('popular', 'videos');

		$this->_getUserStates();
		$this->_buildViewQuery();
	}

	public function _getUserStates() {
		$this->search = parent::_getSecureUserState($this->_option.'.'.$this->_context.'.miwovideos_search', 'miwovideos_search', '', 'string');
		$this->search = MString::strtolower($this->search);
	}

	public function _buildViewQuery() {
		$where = $this->_buildViewWhere();

		$this->_query = 'SELECT * FROM #__miwovideos_videos'
		                .$where
		                .' ORDER BY hits DESC';
	}

	public function _buildViewWhere() {
		$where = array();
		$user  = MFactory::getUser();

		$where[] = 'published=1';
		$where[] = 'access IN ('.implode(',', $user->getAuthorisedViewLevels()).')';

		if ($this->_mainframe->getLanguageFilter()) {
			$where[] = 'language IN ('.$this->_db->Quote(MFactory::getLanguage()->getTag()).','.$this->_db->Quote('*').')';
		}

		if (!empty($this->search)) {
			$src     = parent::secureQuery($this->search, true);
			$where[] = "(LOWER(title) LIKE {$src} OR LOWER(introtext) LIKE {$src})";
		}

		$where[] = 'DATE(created) <= CURDATE()';

		$where = (count($where) ? ' WHERE '.implode(' AND ', $where) : '');

		return $where;
	}

	public function getItems() {
		$items = parent::getItems();

		foreach ($items as $item) {
			$item->channel_title = MiwoDB::loadResult('SELECT title FROM #__miwovideos_channels WHERE id='.$item->channel_id);
		}

		return $items;
	}
}
