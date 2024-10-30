<?php
/**
 * @package        MiwoVideos
 * @copyright    Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license        GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die('Restricted Access');

mimport('framework.filesystem.file');

class MiwovideosFiles {

    protected $_fileset = null;

    public function __construct() {
        $this->config = MiwoVideos::getConfig();
    }

    public function add($video, $fileType, $source, $size, $process_type = null) {
        $date = MFactory::getDate();
        $user = MFactory::getUser();
        $db   = MFactory::getDBO();
        $config = $this->config;

        if (!$process_type) {
            $query = "DELETE FROM #__miwovideos_files WHERE process_type = " . $db->quote($process_type) . " AND video_id = " . $db->quote($video->id);
            $db->setQuery($query);
            if (!$db->query() && $config->get('debug')) {
                MFactory::getApplication()->enqueueMessage(nl2br($db->getErrorMsg()), 'error');
            }
        }

        MTable::addIncludePath(MPATH_WP_PLG.'/miwovideos/admin/tables');
        $row = MTable::getInstance('MiwovideosFiles', 'Table');

        if ($fileType == 'thumb' or $fileType == 'jpg') {
            $item_path = MIWOVIDEOS_UPLOAD_DIR.'/images/videos/'.$video->id.'/'.$size.'/'. $source;
        } else {
            $item_path = MIWOVIDEOS_UPLOAD_DIR.'/videos/'.$video->id.'/'.$size.'/'. $source;
        }

        if (file_exists($item_path)) {
            $post                 = array();
            $post['video_id']     = $video->id;
            $post['process_type'] = $process_type;
            $post['ext']          = $fileType;
            $post['file_size']    = intval(filesize($item_path));
            $post['source']       = $source;
            $post['channel_id']   = MiwoVideos::get('channels')->getDefaultChannel()->id;
            $post['user_id']      = $user->id;
            $post['created']      = $date->format('Y-m-d H:i:s');
            $post['published']    = 1;

            // Bind it to the table
            if (!$row->bind($post)) {
                return MError::raiseWarning(500, $row->getError());
            }

            // Store it in the db
            if (!$row->store()) {
                return MError::raiseError(500, $row->getError());
            }
        } else {
            return MError::raiseError(500, "COM_MIWOVIDEOS_FAILED_TO_ADD_FILE_TO_DATABASE_FILE_DOES_NOT_EXIST");
        }
    }

    public function delete($ids, $img_type = 'videos') {
        if (empty($ids)) {
            return false;
        }

        $files = MiwoDB::loadObjectList("SELECT * FROM #__miwovideos_files WHERE id IN (".implode(',', $ids).")");

        if (empty($files)) {
            return false;
        }

        foreach ($files as $file) {
            if ($file->process_type < 7 or $file->ext == 'jpg') { // Images
                $file_path = MiwoVideos::get('utility')->getThumbPath($file->video_id, $img_type, $file->source, null, 'path');
            } else { // Videos
                if ($file->process_type == 100) { // HTML5 formats
                    $item = MiwoVideos::getTable('MiwovideosVideos');
                    $item->load($file->video_id);
                    $default_size = MiwoVideos::get('utility')->getVideoSize($item->id, $item->source);
                    $file_path = MiwoVideos::get('utility')->getVideoFilePath($file->video_id, $default_size, $file->source, 'path');

                } else {
                    $p_size = MiwoVideos::get('processes')->getTypeSize($file->process_type);
                    $file_path = MiwoVideos::get('utility')->getVideoFilePath($file->video_id, $p_size, $file->source, 'path');
                }
            }

            if (empty($file_path) or !file_exists($file_path)) {
                continue;
            }

            MFile::delete($file_path);
        }

        return true;
    }

    public function getVideoFiles($item_id) {
        if (empty($item_id) || $item_id == 0) return false;

        static $cache = array();

        if (!isset($cache[$item_id])) {
            $cache[$item_id] = MiwoDB::loadObjectList('SELECT * FROM #__miwovideos_files WHERE video_id = ' . (int)$item_id . ' AND published = 1');
        }

        return $cache[$item_id];
    }
}