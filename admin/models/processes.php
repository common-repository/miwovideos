<?php
/**
 * @package        MiwoVideos
 * @copyright      Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license        GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die;

class MiwovideosModelProcesses extends MiwovideosModel {

	public function __construct() {
		parent::__construct('processes');

		$task  = MRequest::getCmd('task');
		$tasks = array('delete');

		if (in_array($task, $tasks)) {
			$cid = MRequest::getVar('cid', array(0), '', 'array');
			$this->setId((int)$cid[0]);
		}
		else {
			$this->_getUserStates();
			$this->_buildViewQuery();
		}
	}

	public function _getUserStates() {
		$this->filter_order        = parent::_getSecureUserState($this->_option.'.'.$this->_context.'.filter_order', 'filter_order', 'pt.title');
		$this->filter_order_Dir    = parent::_getSecureUserState($this->_option.'.'.$this->_context.'.filter_order_Dir', 'filter_order_Dir', 'ASC');
		$this->filter_process_type = parent::_getSecureUserState($this->_option.'.'.$this->_context.'.filter_process_type', 'filter_process_type', 0);
		$this->filter_status       = parent::_getSecureUserState($this->_option.'.'.$this->_context.'.filter_status', 'filter_status', '');
		$this->filter_published    = parent::_getSecureUserState($this->_option.'.'.$this->_context.'.filter_published', 'filter_published', '');
		$this->search              = parent::_getSecureUserState($this->_option.'.'.$this->_context.'.search', 'search', '');
		$this->search              = MString::strtolower($this->search);
	}

	public function _buildViewQuery() {
		$where = $this->_buildViewWhere();

		$orderby = "";
		if (!empty($this->filter_order) and !empty($this->filter_order_Dir)) {
			$orderby = " ORDER BY {$this->filter_order} {$this->filter_order_Dir}";
		}

		$this->_query = "SELECT p.*, pt.title ".
		                "FROM #__miwovideos_processes p ".
		                "LEFT JOIN #__miwovideos_videos v ON (p.video_id = v.id) ".
		                "LEFT JOIN #__miwovideos_process_type pt ON (p.process_type = pt.id) ".
		                $where.$orderby;
	}

	public function _buildViewWhere() {
		$where = array();

		if ($this->search) {
			$src     = parent::secureQuery($this->search, true);
			$where[] = "(LOWER(pt.title) LIKE {$src})";
		}

		if (is_numeric($this->filter_status)) {
			$where[] = 'p.status = '.(int)$this->filter_status;
		}

		if (is_numeric($this->filter_published)) {
			$where[] = 'p.published = '.(int)$this->filter_published;
		}
		else {
			$where[] = 'p.published = 1';
		}


		$where = (count($where) ? ' WHERE '.implode(' AND ', $where) : '');

		return $where;
	}

	public function getTotal() {
		if (empty($this->_total)) {
			$this->_total = MiwoDB::loadResult("SELECT COUNT(*) FROM #__miwovideos_{$this->_table} AS p LEFT JOIN #__miwovideos_process_type AS pt ON pt.id = p.process_type".$this->_buildViewWhere());
		}

		return $this->_total;
	}

	public function getVideos() {
		return MiwoDB::loadObjectList('SELECT id, title FROM #__miwovideos_videos WHERE published = 1 ORDER BY title');
	}

	public function getSuccessful($id = null) {
		$query = 'SELECT COUNT(*) FROM #__miwovideos_processes WHERE status = 1';

		if ($id) {
			$query .= ' AND id = '.$id;
		}

		return MiwoDB::loadResult($query);
	}

	public function getProcessing($video_id = null) {
		return MiwoDB::loadResult('SELECT COUNT(*) FROM #__miwovideos_processes WHERE status = 3 AND published = 1 AND video_id = '.$video_id);
	}

	public function getLogs() {
		$cid = MiwoVideos::getInput()->getVar('cid', array(0), '', 'array');
		$query = "SELECT pl.*, p.status ".
	                "FROM #__miwovideos_process_log pl ".
	                "JOIN #__miwovideos_processes p ".
	                "ON (pl.process_id = p.id) ".
		            "WHERE pl.process_id =".$cid[0];
		return MiwoDB::loadObjectList($query);
	}

	public function getProcess() {
		$cid = MiwoVideos::getInput()->getVar('cid', array(0), '', 'array');
		$query = "SELECT p.*, pt.title, v.title video_title ".
	                "FROM #__miwovideos_processes p ".
	                "LEFT JOIN #__miwovideos_videos v ON (p.video_id = v.id) ".
	                "LEFT JOIN #__miwovideos_process_type pt ON (p.process_type = pt.id) ".
		            "WHERE p.id =".$cid[0];
		return MiwoDB::loadObject($query);
	}

	public function changeProcessStatus($ids, $status = 2) {
		$query = "UPDATE #__miwovideos_processes ".
	                "SET status = ".$status.
		            " WHERE id IN (".implode(",", $ids).")";
		return MiwoDB::execute($query);
	}
}