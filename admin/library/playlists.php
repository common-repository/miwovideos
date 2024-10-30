<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die('Restricted Access');

class MiwovideosPlaylists {

    public function __construct() {
		$this->config = MiwoVideos::getConfig();
	}

    public function getPlaylist($id) {
        static $cache = array();

        if (!isset($cache[$id])) {
            $cache[$id] = MiwoDB::loadObject('SELECT * FROM #__miwovideos_playlists WHERE id = '.$id);
        }

        return $cache[$id];
    }

    public function getTotalPlaylists($video_id = 0, $status = 3) {
        static $cache = array();

        if (!isset($cache[$video_id][$status])) {
			$where = array();
		
            if ($video_id != 0) {
                $where[] = 'video_id = '.$video_id;
            }
		
            if ($status != 0) {
                $where[] = 'status = '.$status;
            }

			$where = count($where) ? ' WHERE '. implode(' AND ', $where) : '';

            $cache[$video_id][$status] = (int)MiwoDB::loadResult('SELECT COUNT(*) AS total_playlists FROM #__miwovideos_playlists '.$where);
        }

        return $cache[$video_id][$status];
    }
}