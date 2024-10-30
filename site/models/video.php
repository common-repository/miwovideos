<?php
/**
 * @package        MiwoVideos
 * @copyright      Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license        GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die;

class MiwovideosModelVideo extends MiwovideosModel {

	public function __construct() {
		parent::__construct('video', 'videos');

		$this->video_id = MRequest::getInt('video_id');
		if (!is_null(MRequest::getInt('item_id', null)) and MRequest::getWord('format') == 'raw') {
			$this->video_id = MRequest::getInt('item_id', null);
		}
		$this->playlist_id = MRequest::getInt('playlist_id', null);
	}

	public function getData() {
		if (empty($this->_data)) {
			$nullDate = $this->_db->getNullDate();
			$user_id  = MFactory::getUser()->get('id');

			$this->_data = MiwoVideos::get('videos')->getVideo($this->video_id);

			$this->_data->category_id = Miwovideos::get('utility')->getVideoCategory($this->video_id)->id;

			$sql = "SELECT COUNT(channel_id) AS channel_videos_count FROM #__miwovideos_videos WHERE channel_id = {$this->_data->channel_id}";
			$this->_db->setQuery($sql);

			$this->_data->channel_videos_count = $this->_db->loadResult();

			$this->_data->playlist_id = $this->playlist_id;

			/*$sql = "SELECT COUNT(*) AS channel_subs FROM #__miwovideos_subscriptions WHERE channel_id = {$this->_data->channel_id}";
			$this->_db->setQuery($sql);*/


			$this->_data->channel_subs = MiwoVideos::get('model')->getSubscriberCount($this->_data->channel_id);
		}

		return $this->_data;
	}

	public function getTotalVideos() {
		return MiwoDB::loadResult("SELECT COUNT(*) FROM #__miwovideos_videos".$this->_buildViewWhere());
	}

	# Get Total Videos

	public function _buildViewWhere() {
		$where = array();
		$user  = MFactory::getUser();

		# Video page... Update like or dislike field...
		$sel = MRequest::getVar('selection', 'selected', 'post');
		if ($sel == 'filtered' && !is_null(MRequest::getInt('change', null, 'post'))) {
			return $where = "WHERE user_id = {$user->id} AND item_id = {$this->video_id}";
			###########
		}
		else {
			if (!empty($this->video_id)) { # Video Page
				$where[] = 'v.id='.$this->video_id;
			}
			else { # Playlist Page
				$where [] = 'pv.playlist_id='.$this->playlist_id;
			}
			$where[] = 'v.published=1';
			$where[] = 'v.access IN ('.implode(',', $user->getAuthorisedViewLevels()).')';

			if ($this->_mainframe->getLanguageFilter()) {
				$where[] = 'v.language IN ('.$this->_db->Quote(MFactory::getLanguage()->getTag()).','.$this->_db->Quote('*').')';
			}

			if (!empty($this->search)) {
				$src     = parent::secureQuery($this->search, true);
				$where[] = "(LOWER(title) LIKE {$src} OR LOWER(description) LIKE {$src})";
			}

			$where[] = 'DATE(v.created) <= CURDATE()';

			$where = (count($where) ? ' WHERE '.implode(' AND ', $where) : '');
		}

		return $where;
	}

	# Get Video Product ID from Miwoshop

	public function getProductID() {
		return MiwoDB::loadResult("SELECT product_id FROM #__miwovideos_videos WHERE id = {$this->video_id} ORDER BY id DESC LIMIT 1");
	}

	public function getVideoCategories() {
		return MiwoDB::loadObjectList('SELECT c.title,c.id FROM #__miwovideos_video_categories vc LEFT JOIN #__miwovideos_categories c ON vc.category_id=c.id WHERE video_id='.$this->video_id);
	}

	public function getPlaylistVideos() {
		$result = MiwoDB::loadObjectList("SELECT v.*
                            FROM #__miwovideos_videos v
                            LEFT JOIN #__miwovideos_playlist_videos pv ON (pv.video_id=v.id)"
		                                 .$this->_buildViewWhere().' ORDER BY pv.ordering ASC');

		return $result;
	}

	public function getTotalPlaylistVideos() {
		$result = MiwoDB::loadResult("SELECT COUNT(*)
                                FROM #__miwovideos_videos v
                                LEFT JOIN #__miwovideos_playlist_videos pv ON (pv.video_id=v.id)
                                ".$this->_buildViewWhere()."
                                GROUP BY pv.playlist_id ");

		return $result;
	}

	public function getReasons() {
		$user = MFactory::getUser();

		$where[] = 'rs.published=1';
		$where[] = 'rs.access IN ('.implode(',', $user->getAuthorisedViewLevels()).')';

		if ($this->_mainframe->getLanguageFilter()) {
			$where[] = 'rs.language IN ('.$this->_db->Quote(MFactory::getLanguage()->getTag()).','.$this->_db->Quote('*').')';
		}
		$where = (count($where) ? ' WHERE '.implode(' AND ', $where) : '');

		$result = MiwoDB::loadObjectList("SELECT rs.*
                                FROM #__miwovideos_report_reasons rs
                                ".$where);

		return $result;
	}

	public function submitReport($post) {
		$date    = MFactory::getDate();
		$user_id = MFactory::getUser()->get('id');
		$row     = MiwoVideos::getTable('MiwovideosReports');

		$data               = array();
		$data['channel_id'] = MiwoVideos::get('channels')->getDefaultChannel()->id;
		$data['user_id']    = $user_id;
		$data['item_id']    = $post['item_id'];
		$data['item_type']  = $post['item_type'];
		$data['reason_id']  = $post['miwovideos_reasons'];
		$data['note']       = $post['miwovideos_report'];
		$data['created']    = $date->format('Y-m-d H:i:s');
		$data['modified']   = $date->format('Y-m-d H:i:s');
		if (!$row->bind($data)) {
			$this->setError($row->getError());
			return false;
		}

		if (!$row->store()) {
			$this->setError($row->getError());
			return false;
		}

		//MiwoDB::query("INSERT INTO #__miwovideos_reports (user_id, item_id, item_type, reason_id, note, created, modified) VALUES ({$user_id}, {$post['item_id']}, '{$post['item_type']}', {$post['miwovideos_reasons']}, '{$post['miwovideos_report']}', NOW(), NOW())");
		return true;
	}

	public function getProcessing($video_id = null) {
		return MiwoDB::loadResult('SELECT COUNT(*) FROM #__miwovideos_processes WHERE status = 3 AND published = 1 AND video_id = '.$video_id);
	}

	public function getReport() {
		$video_id = MRequest::getInt('video_id');
		$user_id  = MFactory::getUser()->get('id');
		$date     = MFactory::getDate()->toUnix() - 604800;
		$date     = gmdate("Y-m-d H:i:s", $date);
		return MiwoDB::loadObject('SELECT r.note, rr.title, r.created FROM #__miwovideos_reports AS r LEFT JOIN #__miwovideos_report_reasons AS rr ON (rr.id = r.reason_id) WHERE r.user_id = '.$user_id.' AND r.item_id = '.$video_id.' AND r.created > "'.$date.'"');
	}
}