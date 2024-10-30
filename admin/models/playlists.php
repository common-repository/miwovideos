<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die ;

class MiwovideosModelPlaylists extends MiwovideosModel {

    public function __construct() {
        parent::__construct('playlists');

        $this->acl = MiwoVideos::get('acl');
        $this->user = MFactory::getUser();

        $task = MRequest::getCmd('task');
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

    public function _getUserStates(){
        $this->filter_order            	= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_order',            	'filter_order',         'p.title');
        $this->filter_order_Dir        	= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_order_Dir',        	'filter_order_Dir',     'ASC');
        $this->filter_status        	= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_published',         'filter_published',     '');
        $this->filter_access            = parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_access',         	'filter_access',        '');
        $this->filter_language        	= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_language',          'filter_language',     '');
        $this->search                	= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.search',                 	'search',               '');
        $this->search                  	= MString::strtolower($this->search);
    }

    public function _buildViewQuery() {
        $where = $this->_buildViewWhere();

        $orderby = "";
        if (!empty($this->filter_order) and !empty($this->filter_order_Dir)) {
            $orderby = " ORDER BY {$this->filter_order} {$this->filter_order_Dir}";
        }

        $this->_query = "SELECT p.*, c.title AS channel_title, u.ID user_id, u.user_login username ".
                        "FROM #__miwovideos_playlists p ".
                        "LEFT JOIN #__users AS u ON (p.channel_id = u.ID) ".
                        "LEFT JOIN #__miwovideos_channels AS c ON (p.channel_id = c.id) ".
                        $where.
                        $orderby;

        return $this->_query;
    }

    public function _buildViewWhere() {
        $where = array();

        $where[] = 'p.type=0';

        if ($this->search) {
            $src = parent::secureQuery($this->search, true);
            $where[] = "(LOWER(p.title) LIKE {$src})";
        }

        if (is_numeric($this->filter_status)) {
            $where[] = 'p.published = '.(int) $this->filter_status;
        }

        if (is_numeric($this->filter_access)) {
            $where[] = 'p.access = '.(int) $this->filter_access;
        }

        if ($this->filter_language) {
            $where[] = 'c.language IN (' . $this->_db->Quote($this->filter_language) . ',' . $this->_db->Quote('*') . ')';
        }

        if (!$this->acl->canManage()) {
            $where[] = 'p.user_id = '.$this->user->get('id');
        }

        $where = (count($where) ? ' WHERE '. implode(' AND ', $where) : '');

        return $where;
    }

    public function getTotal() {
        if (empty($this->_total)) {
            $this->_total = MiwoDB::loadResult("SELECT COUNT(*) FROM #__miwovideos_{$this->_table} AS p".$this->_buildViewWhere());
        }

        return $this->_total;
    }

    public function getChannels() {
        return MiwoDB::loadObjectList('SELECT id, title FROM #__miwovideos_channels');
    }

    public function getEditData($table = NULL) {
        if (empty($this->_data)) {
            if (!empty($this->_id)) {
                $this->_data = parent::getEditData();
            }
            else {
                $row = parent::getEditData();
                $row->video_id = 0;
                $this->_data = $row;
            }
        }

        return $this->_data;
    }

    public function getFields() {
        $allData = MiwoDB::loadObjectList("SELECT * FROM #__miwovideos_fields WHERE display_in = 1 ORDER By ordering ASC");

        # Sort Array Data
        asort($allData);
		
        return $allData;
    }

    public function getColumns() {
        if ($this->config->get('search_list_fields')) {
            $list = $this->config->get('search_list_fields')->list;
        }
        else {
            $list = NULL;
        }

        if ($list != NULL){
            foreach ($list as $key => $value){
                $allData[] = MiwoDB::loadObject("SELECT ordering, name, title FROM #__miwovideos_fields WHERE name = '{$key}' ORDER By ordering ASC");
            }

            # Sort Array Data
            asort($allData);

            # table list
            foreach ($allData as $cName){ $columnTitle[]	=  $cName->title; }
            foreach ($allData as $cName){ $columnName[]   	=  $cName->name; }

            $this->columnData[0] = $columnTitle;
            $this->columnData[1] = $columnName;
            $this->columnData[2] = $this->getItems();
        }
        else {
            $this->columnData = NULL;
        }

        return $this->columnData;
    }
}