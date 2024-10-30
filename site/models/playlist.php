<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die ;

class MiwovideosModelPlaylist extends MiwovideosModel {

    public function __construct() {
		parent::__construct('playlist', 'playlists');

        $this->playlist_id = MRequest::getInt('playlist_id');
        if (!is_null(MRequest::getInt('item_id', null))) {
            $this->playlist_id = MRequest::getInt('item_id', null);
        }
        $this->_getUserStates();
        $this->_buildViewQuery();
	}

    public function getItem() {
        $row = MiwoVideos::getTable('MiwovideosPlaylists');
        $row->load($this->playlist_id);

        return $row;
    }

    public function _getUserStates() {
        $this->filter_order			= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_order',			'filter_order',			'p.title',	'cmd');
        $this->filter_order_Dir		= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_order_Dir',		'filter_order_Dir',		'DESC',     'word');
        $this->search				= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.search', 				'search', 				'',         'string');
        $this->search 	 			= MString::strtolower($this->search);
    }

    public function _buildViewQuery() {
        $where = $this->_buildViewWhere();

        $orderby = "";
        if (!empty($this->filter_order) and !empty($this->filter_order_Dir)) {
            $orderby = " ORDER BY {$this->filter_order} {$this->filter_order_Dir}";
        }

        $this->_query = "SELECT
                    p.*
                FROM #__miwovideos_playlists p" .$where.$orderby;
    }

    public function getTotal() {
        if (empty($this->_total)) {
            $this->_total = MiwoDB::loadResult("SELECT COUNT(*) FROM #__miwovideos_{$this->_table} AS p".$this->_buildViewWhere());
        }

        return $this->_total;
    }

    public function _buildViewWhere() {
        $where = array();

        # Playlist page... Update like or dislike field...
        $sel = MRequest::getVar('selection', 'selected', 'post');
        if ($sel == 'filtered' && !is_null(MRequest::getInt('change', null, 'post'))) {
            $user_id = MFactory::getUser()->get('id');
            $where = "WHERE user_id = {$user_id} AND item_id = {$this->playlist_id}";
        ###########
        } else {

            $where[] = 'p.id='.$this->playlist_id;

            $where[] = 'p.published=1';

            if (!empty($this->search)) {
                $src = parent::secureQuery($this->search, true);
                $where[] = "(LOWER(title) LIKE {$src} OR LOWER(description) LIKE {$src})";
            }
			
			$where[] = 'DATE(created) <= CURDATE()';

            $where = (count($where) ? ' WHERE '. implode(' AND ', $where) : '');
        }

        return $where;
    }

    public function getChannelItem() {

        $channel_id = MiwoDB::loadResult("SELECT channel_id FROM #__miwovideos_playlists WHERE id = {$this->playlist_id}");

        $row = MiwoVideos::getTable('MiwovideosChannels');
        $row->load($channel_id);

        $row->subs = MiwoVideos::get('model')->getSubscriberCount($channel_id);

        return $row;
    }
}