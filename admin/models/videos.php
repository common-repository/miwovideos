<?php
/**
 * @package        MiwoVideos
 * @copyright      Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license        GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die;

class MiwovideosModelVideos extends MiwovideosModel {

	public $process;

	public function __construct() {
		parent::__construct('videos');

		$this->acl  = MiwoVideos::get('acl');
		$this->user = MFactory::getUser();

		$task  = MRequest::getCmd('task');
		$tasks = array('edit', 'apply', 'save', 'save2new');

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
		$this->filter_order     = parent::_getSecureUserState($this->_option.'.'.$this->_context.'.filter_order', 'filter_order', 'v.title');
		$this->filter_order_Dir = parent::_getSecureUserState($this->_option.'.'.$this->_context.'.filter_order_Dir', 'filter_order_Dir', 'ASC');
		$this->filter_category  = parent::_getSecureUserState($this->_option.'.'.$this->_context.'.filter_category', 'filter_category', 0);
		$this->filter_channel   = parent::_getSecureUserState($this->_option.'.'.$this->_context.'.filter_channel', 'filter_channel', 0);
		$this->filter_published = parent::_getSecureUserState($this->_option.'.'.$this->_context.'.filter_published', 'filter_published', '');
		$this->filter_access    = parent::_getSecureUserState($this->_option.'.'.$this->_context.'.filter_access', 'filter_access', '');
		$this->filter_language  = parent::_getSecureUserState($this->_option.'.'.$this->_context.'.filter_language', 'filter_language', '');
		$this->search           = parent::_getSecureUserState($this->_option.'.'.$this->_context.'.search', 'search', '');
		$this->search           = MString::strtolower($this->search);
	}

	public function _buildViewQuery() {
		$where = self::_buildViewWhere();

		$orderby = "";
		if (!empty($this->filter_order) and !empty($this->filter_order_Dir)) {
			$orderby = " ORDER BY {$this->filter_order} {$this->filter_order_Dir}";
		}

		$this->_query = 'SELECT v.* FROM #__miwovideos_videos AS v '
		                .$where
		                .' GROUP BY v.id '
		                .$orderby;
	}

	public function _buildViewWhere() {
		$where = array();

		if ($this->search) {
			$src     = parent::secureQuery($this->search, true);
			$where[] = "LOWER(v.title) LIKE {$src}";
		}

		if ($this->filter_category) {
			$where[] = 'v.id IN (SELECT video_id FROM #__miwovideos_video_categories WHERE category_id='.$this->filter_category.')';
		}

		if ($this->filter_channel) {
			$where[] = 'v.channel_id IN (SELECT channel_id FROM #__miwovideos_channels WHERE channel_id='.$this->filter_channel.')';
		}

		if (is_numeric($this->filter_published)) {
			$where[] = 'v.published = '.(int)$this->filter_published;
		}

		if (is_numeric($this->filter_access)) {
			$where[] = 'v.access = '.(int)$this->filter_access;
		}

		if ($this->filter_language) {
			$where[] = 'v.language IN ('.$this->_db->Quote($this->filter_language).','.$this->_db->Quote('*').')';
		}

		if (!$this->acl->canManage()) {
			$where[] = 'v.user_id = '.$this->user->get('id');
		}

		$where = (count($where) ? ' WHERE '.implode(' AND ', $where) : '');

		return $where;
	}

	public function getItems() {
		if (empty($this->_data)) {
			$rows = parent::getItems();

			static $cache = array();

			foreach ($rows as $row) {
				if (!isset($cache[ $row->id.'c' ])) {
					$sql = "SELECT c.title FROM #__miwovideos_categories AS c, #__miwovideos_video_categories AS ec WHERE c.id = ec.category_id AND ec.video_id = {$row->id}";
					$this->_db->setQuery($sql);

					$cache[ $row->id.'c' ] = implode(' | ', $this->_db->loadColumn());
				}

				$row->categories = $cache[ $row->id.'c' ];

				if (!isset($cache[ $row->channel_id ])) {
					$sql = "SELECT c.title FROM #__miwovideos_channels AS c WHERE c.id = {$row->channel_id}";
					$this->_db->setQuery($sql);
					$cache[ $row->channel_id ] = $this->_db->loadResult();
				}

				$row->channel_title = $cache[ $row->channel_id ];

			}

			$pagination = parent::getPagination();
			$rows       = array_slice($rows, 0, $pagination->limit);

			$this->_data = $rows;
		}

		return $this->_data;
	}

	public function getTotal() {
		if (empty($this->_total)) {
			$this->_total = MiwoDB::loadResult("SELECT COUNT(*) FROM #__{$this->_component}_{$this->_table} AS v".$this->_buildViewWhere());
		}

		return $this->_total;
	}

	public function getCategories() {
		return MiwoDB::loadObjectList('SELECT id, parent, parent AS parent_id, title FROM #__miwovideos_categories');
	}

	public function getChannels() {
		return MiwoDB::loadObjectList('SELECT id, title FROM #__miwovideos_channels');
	}

	public function getVideoCategories() {
		return MiwoDB::loadResultArray('SELECT category_id FROM #__miwovideos_video_categories WHERE video_id='.$this->_id);
	}

	public function getFiles() {
		return MiwoDB::loadObjectList('SELECT * FROM #__miwovideos_files WHERE video_id = '.$this->_id);
	}

	public function store(&$data) {
		$row = MiwoVideos::getTable('MiwovideosVideos');

		if (isset($data['channel_id'])) {
			$data['user_id'] = MiwoVideos::get('channels')->getUserId($data['channel_id']);
		}

		if (isset($data['channel_id'])) {
			$data['fields'] = json_encode($data['custom_fields']);
		}

		if ((!empty($data['tags']) and $data['tags'][0] != '')) {
			if (!is_array($data['tags'])) {
				$data['tags'] = array($data['tags']);
			}

			$row->newTags = $data['tags'];
		}

		if (!$row->bind($data)) {
			return false;
		}

		if (!$row->check($data)) {
			return false;
		}

		if (!$row->store()) {
			return false;
		}

		if (!empty($row->id)) {
			MiwoDB::execute("DELETE FROM `#__miwovideos_video_categories` WHERE video_id = {$row->id}");

			if (!empty($data['video_categories'])) {
				foreach ($data['video_categories'] as $category_id) {
					MiwoDB::execute("INSERT INTO `#__miwovideos_video_categories` SET video_id = {$row->id}, category_id = {$category_id}");
				}
			}
		}

		$data['id'] = $row->id;

		return true;
	}

	public function getEditData($table = null) {
		if (empty($this->_data)) {
			$row = parent::getEditData();

			$this->_data = $row;
		}

		return $this->_data;
	}

	public function copy($id) {
		$rowOld = MiwoVideos::getTable('MiwovideosVideos');
		$rowOld->load($id);

		$row  = MiwoVideos::getTable('MiwovideosVideos');
		$data = MArrayHelper::fromObject($rowOld);
		$row->bind($data);

		$row->id    = 0;
		$row->title = $row->title.' - Copy';
		$row->store();

		# Need to enter categories for this video
		$sql = 'INSERT INTO #__miwovideos_video_categories(video_id, category_id) '
		       .' SELECT '.$row->id.' , category_id FROM #__miwovideos_video_categories '
		       .' WHERE video_id='.$id;

		$this->_db->setQuery($sql);
		$this->_db->query();

		return $row->id;
	}

	public function getProductID() {
		if ($this->_id) {
			$sql = "SELECT product_id FROM #__miwovideos_videos WHERE id= {$this->_id} ORDER BY id DESC LIMIT 1";
			$this->_db->setQuery($sql);
			$productID = $this->_db->loadResult();
		}
		else {
			$productID = 0;
		}
		return $productID;
	}

	public function autoComplete($query) {
		if (!empty($query)) {
			$sql = "SELECT id, name FROM #__miwovideos_fields WHERE LOWER(name) LIKE '%".strtolower($query)."%' ORDER BY name DESC";
			$this->_db->setQuery($sql);
			$videos = $this->_db->loadAssocList();
		}
		else {
			$videos = array();
		}

		return $videos;
	}

	public function getFields() {
		return MiwoDB::loadObjectList("SELECT * FROM #__miwovideos_fields WHERE display_in = 1 AND published = 1 ORDER BY ordering");
	}

	public function delete($ids) {
		if (!MiwoDB::query('DELETE FROM #__miwovideos_video_categories WHERE video_id IN ('.implode(',', $ids).')')) {
			return false;
		}

		if (!MiwoDB::query('DELETE FROM #__miwovideos_videos WHERE id IN ('.implode(',', $ids).')')) {
			return false;
		}

		if (!MiwoDB::query('DELETE FROM #__miwovideos_files WHERE video_id IN ('.implode(',', $ids).')')) {
			return false;
		}

		if (!MiwoDB::query('DELETE FROM #__miwovideos_playlist_videos WHERE video_id IN ('.implode(',', $ids).')')) {
			return false;
		}

		if (!MiwoDB::query('DELETE FROM #__miwovideos_processes WHERE video_id IN ('.implode(',', $ids).')')) {
			return false;
		}

		if (!MiwoDB::query('DELETE FROM #__miwovideos_process_log WHERE video_id IN ('.implode(',', $ids).')')) {
			return false;
		}

		return true;

	}

	public function getLinkedVideos() {
		$p_id    = MRequest::getInt('playlist_id');
		$where[] = "pv.playlist_id  = ".$p_id;
		if (!empty($this->search)) {
			$src     = parent::secureQuery($this->search, true);
			$where[] = "LOWER(v.title) LIKE {$src}";
		}

		$where = ' WHERE '.implode(' AND ', $where);

		$rows = MiwoDB::loadObjectList('SELECT v.* FROM #__miwovideos_playlist_videos AS pv LEFT JOIN #__miwovideos_videos AS v ON (pv.video_id = v.id)'.$where);
		static $cache = array();

		foreach ($rows as $row) {
			if (!isset($cache[ $row->id.'c' ])) {
				$sql = "SELECT c.title FROM #__miwovideos_categories AS c, #__miwovideos_video_categories AS ec WHERE c.id = ec.category_id AND ec.video_id = {$row->id}";
				$this->_db->setQuery($sql);

				$cache[ $row->id.'c' ] = implode(' | ', $this->_db->loadColumn());
			}

			$row->categories = $cache[ $row->id.'c' ];

			if (!isset($cache[ $row->channel_id ])) {
				$sql = "SELECT c.title FROM #__miwovideos_channels AS c WHERE c.id = {$row->channel_id}";
				$this->_db->setQuery($sql);
				$cache[ $row->channel_id ] = $this->_db->loadResult();
			}

			$row->channel_title = $cache[ $row->channel_id ];

		}

		return $rows;
	}
}