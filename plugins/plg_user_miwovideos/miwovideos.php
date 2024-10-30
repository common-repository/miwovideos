<?php
/*
* @package		MiwoVideos
* @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
* @license		GNU General Public License version 2 or later
*/

// No Permission
defined('MIWI') or die ('Restricted access');

mimport('framework.plugin.plugin');

class plgUserMiwovideos extends MPlugin {
	
	public function __construct(& $subject, $config) {
		parent::__construct($subject, $config);
		
        $miwovideos = MPATH_WP_PLG.'/miwovideos/admin/library/miwovideos.php';
        if (!file_exists($miwovideos)) {
            return;
        }

        require_once($miwovideos);
	}

	public function onUserAfterSave($user_id, $user = null) {
        if (empty($user_id)) {
            return false;
        }


    if (empty($user)) {
        $user = new stdClass();
        $user->ID = $user_id;
        $user->user_login = MRequest::getString('user_login');
    }

        $db = MFactory::getDBO();

        if(!$this->_hasChannel($db, $user->ID)){
            return $this->_newUser($db, $user);
        }

        return $this->_oldUserUpdate($db, $user);
    }
	
	public function onUserAfterDelete($user_id, $user = null) {
		if (empty($user_id)) {
			return false;
		}


    if (empty($user)) {
        $user = new stdClass();
        $user->ID = $user_id;
        $user->user_login = MRequest::getString('user_login');
    }

        $db = MFactory::getDBO();

        $st = MiwoVideos::getConfig()->get('ondelete_channel_status', 1);
        if($st == 0){
            $this->_delete($db, 'channels', $user->ID);

            $this->_delete($db, 'channel_subscriptions', $user->ID);

            $this->_deleteLikes($db, 'channels', $user->ID);
        }
        else if($st == 1){
            $this->_unpublish($db, 'channels', $user->ID);
        }


        $st = MiwoVideos::getConfig()->get('ondelete_playlists_status', 1);
        if($st == 0){
            $this->_delete($db, 'playlists', $user->ID);

            $this->_deletePlaylistRels($db, $user->ID);

            $this->_deleteLikes($db, 'playlists', $user->ID);
        }
        else if($st == 1){
            $this->_unpublish($db, 'playlists', $user->ID);
        }

        $st = MiwoVideos::getConfig()->get('ondelete_videos_status', 1);
        if($st == 0){
            $this->_delete($db, 'videos', $user->ID);

            $this->_deleteVideoRels($db, 'video_categories', $user->ID);
            $this->_deleteVideoRels($db, 'playlist_videos', $user->ID);

            $this->_deleteLikes($db, 'videos', $user->ID);
        }
        else if($st == 1){
            $this->_unpublish($db, 'videos', $user->ID);
        }

		return true;
	}

    private function _deleteLikes($db, $type, $user_id){
        $db->setQuery("SELECT id FROM `#__miwovideos_{$type}` WHERE `user_id` = {$user_id} ");
        $ids = $db->loadColumn();

        if(empty($ids)){
            return;
        }

        $ids = implode(',', $ids);
        $db->setQuery("DELETE FROM `#__miwovideos_likes` WHERE `user_id` = {$user_id} AND `item_type` = '{$type}' AND item_id IN ({$ids})");
        $db->execute();
    }

    private function _deleteVideoRels($db, $type, $user_id){
        $db->setQuery("SELECT id FROM `#__miwovideos_videos` WHERE `user_id` = {$user_id} ");
        $ids = $db->loadColumn();

        if(empty($ids)){
            return;
        }

        $ids = implode(',', $ids);
        $db->setQuery("DELETE FROM `#__miwovideos_{$type}` WHERE video_id IN ({$ids})");
        $db->execute();
    }

    private function _deletePlaylistRels($db, $user_id){
        $db->setQuery("SELECT id FROM `#__miwovideos_playlists` WHERE `user_id` = {$user_id} ");
        $ids = $db->loadColumn();

        if(empty($ids)){
            return;
        }

        $ids = implode(',', $ids);
        $db->setQuery("DELETE FROM `#__miwovideos_playlist_videos` WHERE playlist_id IN ({$ids})");
        $db->execute();
    }

    private function _delete($db, $type, $user_id){
        $db->setQuery("DELETE FROM `#__miwovideos_{$type}` WHERE `user_id` = {$user_id}");
        $db->execute();
    }

    private function _unpublish($db, $type, $user_id){
        $db->setQuery("UPDATE `#__miwovideos_{$type}` SET `published` = 0 WHERE user_id = {$user_id}");
        $db->execute();
    }

    private function _newUser($db, $user){
        $published = 0;
        if($user['block'] == 0){
            $published = 1;
        }

        # Add Channel
        $db->setQuery("INSERT INTO `#__miwovideos_channels` (`user_id`, `title`, `alias`, `introtext`, `fulltext`, `thumb`, `banner`, `fields`,`likes`, `dislikes`, `hits`, `params`, `ordering`, `access`, `language`, `created`, `modified`, `featured`, `published`, `default`, `share_others`, `meta_desc`, `meta_key`, `meta_author`) VALUES
        ({$user->ID}, '{$user->user_login}', '{$user->user_login}', '{$user->user_login}', '', '', '', '', 0, 0, 0, '', '', 1, '*', NOW(), NOW(), 0, {$published}, 1, 0, '', '', '')");
        $db->execute();

        $channel_id = $db->insertid();
        if(empty($channel_id)){
            return false;
        }

        # Add Watch Later
        $db->setQuery("INSERT INTO `#__miwovideos_playlists` (`channel_id`, `user_id`, `type`, `title`, `alias`, `introtext`, `fulltext`, `thumb`, `fields`,`likes`, `dislikes`, `hits`, `subscriptions`, `params`, `ordering`, `access`, `language`, `created`, `modified`, `meta_desc`, `meta_key`, `meta_author`, `share_others`, `featured`, `published`) VALUES
        ({$channel_id}, {$user->ID}, 1, 'Watch Later', 'watch-later', '', '', '', '', '', 0, 0, 0, '', '', 1, '*', NOW(), NOW(), '', '', '', 0, 0, {$published})");
        $db->execute();

        # Add Favorite Videos
        $db->setQuery("INSERT INTO `#__miwovideos_playlists` (`channel_id`, `user_id`,  `type`, `title`, `alias`, `introtext`, `fulltext`, `thumb`, `fields`,`likes`, `dislikes`, `hits`, `subscriptions`, `params`, `ordering`, `access`, `language`, `created`, `modified`, `meta_desc`, `meta_key`, `meta_author`, `share_others`, `featured`, `published`) VALUES
        ({$channel_id}, {$user->ID}, 2, 'Favorite Videos', 'favorite-videos', '', '', '', '', '', 0, 0, 0, '', '', 1, '*', NOW(), NOW(), '', '', '', 0, 0, {$published})");
        $db->execute();

        return true;
    }

    private function _oldUserUpdate($db, $user){
        
        $st = MiwoVideos::getConfig()->get('onupdate_channel_status', 1);
        if($st == 1){
            $this->_unpublish($db, 'channels', $user->ID);
        }

        $st = MiwoVideos::getConfig()->get('onupdate_playlists_status', 1);
        if($st == 1){
            $this->_unpublish($db, 'playlists', $user->ID);
        }

        $st = MiwoVideos::getConfig()->get('onupdate_videos_status', 1);
        if($st == 1){
            $this->_unpublish($db, 'videos', $user->ID);
        }

        return true;
    }

    private function _hasChannel($db, $user_id){
        $db->setQuery("SELECT id FROM `#__miwovideos_channels` WHERE `user_id` = {$user_id} ");
        $ids = $db->loadColumn();

        if(empty($ids)){
            return false;
        }

        return true;
    }

}

//todo:: add to config
/**********
ondelete_channel_status
ondelete_videos_status
ondelete_playlists_status

onupdate_channel_status
onupdate_playlists_status
onupdate_videos_status
*/