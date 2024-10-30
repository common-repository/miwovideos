<?php
/**
 * @package        MiwoVideos
 * @copyright      Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license        GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die;

class MiwovideosModelUsers extends MiwovideosModel {

	public function __construct() {
		parent::__construct('users');

		$this->_getUserStates();
		$this->_buildViewQuery();
	}

	public function _getUserStates() {
		$this->filter_order     = parent::_getSecureUserState($this->_option.'.'.$this->_context.'.filter_order', 'filter_order', 'u.user_login');
		$this->filter_order_Dir = parent::_getSecureUserState($this->_option.'.'.$this->_context.'.filter_order_Dir', 'filter_order_Dir', 'ASC');
		$this->search           = parent::_getSecureUserState($this->_option.'.'.$this->_context.'.search', 'search', '');
		$this->search           = MString::strtolower($this->search);
	}

	public function _buildViewQuery() {
		$where = $this->_buildViewWhere();

		$orderby = "";
		if (!empty($this->filter_order) and !empty($this->filter_order_Dir)) {
			$orderby = " ORDER BY {$this->filter_order} {$this->filter_order_Dir}";
		}

		$this->_query = "SELECT
                u.ID id, u.display_name, u.user_login
            FROM #__users u".$where.$orderby;
	}

	public function _buildViewWhere() {
		$where = array();

		if ($this->search) {
			$src     = parent::secureQuery($this->search, true);
			$where[] = "(LOWER(u.user_login) LIKE {$src})";
		}

		$where = (count($where) ? ' WHERE '.implode(' AND ', $where) : '');

		return $where;
	}

	public function getTotal() {
		if (empty($this->_total)) {
			$this->_total = MiwoDB::loadResult("SELECT COUNT(*) FROM #__users AS u".$this->_buildViewWhere());
		}

		return $this->_total;
	}
}
