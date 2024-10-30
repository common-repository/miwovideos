<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die('Restricted Access');

mimport('framework.application.component.model');

if (!class_exists('MiwisoftModel')) {
	if (interface_exists('MModel')) {
		abstract class MiwisoftModel extends MModelLegacy {}
	}
	else {
		class MiwisoftModel extends MModel {}
	}
}

class MiwovideosModel extends MiwisoftModel {
	
	public $_query;
	public $_id = null;
	public $_data = null;
	public $_total = null;
	public $_pagination = null;
	public $_context;
	public $_mainframe;
	public $_option;
	public $_table;
	
    public function __construct($context = '', $table = '') 	{
		parent::__construct();

		# Get config object
		$this->config = MiwoVideos::getConfig();
		
		# Get global vars
		$this->_mainframe = MFactory::getApplication();
		if ($this->_mainframe->isAdmin()) {
			$this->_option = MiwoVideos::get('utility')->findOption();
		} else {
			$this->_option = MRequest::getCmd('option');
		}
		
		$this->_component = str_replace('com_', '', $this->_option);
		
		$this->_context = $context;
		
		$this->_table = $table;
		if ($table == '' and $this->_context != '') {
			$this->_table = $this->_context;
		}
		
		# Pagination
		if ($this->_context != '') {
			# Get the pagination request variables
			$limit		= $this->_mainframe->getUserStateFromRequest($this->_option . '.' . $this->_context . '.limit', 'limit', $this->_mainframe->getCfg('list_limit'), 'int');
			$limitstart	= $this->_mainframe->getUserStateFromRequest($this->_option . '.' . $this->_context . '.limitstart', 'limitstart', 0, 'int');
			
			# Limit has been changed, adjust it
			$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
			
			$this->setState($this->_option . '.' . $this->_context . '.limit', $limit);
			$this->setState($this->_option . '.' . $this->_context . '.limitstart', $limitstart);
		}
	}

    public function setId($id) {
        $this->_id		= $id;
        $this->_data	= null;
    }
	
    public function _buildViewQuery() {
		$where = $this->_buildViewWhere();
		
		$orderby = "";
		if (!empty($this->filter_order) and !empty($this->filter_order_Dir)) {
			$orderby = " ORDER BY {$this->filter_order} {$this->filter_order_Dir}";
		}
		
		$this->_query = "SELECT * FROM #__{$this->_component}_{$this->_table} {$where}{$orderby}";
	}
	
	public function _buildViewWhere() {
		return '';
	}
	
    public function getItems() {
		if (empty($this->_data)) {
			$this->_data = $this->_getList($this->_query, $this->getState($this->_option.'.' . $this->_context . '.limitstart'), $this->getState($this->_option.'.' . $this->_context . '.limit'));
		}
		
		return $this->_data;
	}
		
    public function getPagination() {
		if (empty($this->_pagination)) {
			mimport('framework.html.pagination');
			$this->_pagination = new MPagination($this->getTotal(), $this->getState($this->_option.'.' . $this->_context . '.limitstart'), $this->getState($this->_option.'.' . $this->_context . '.limit'));
		}
		
		return $this->_pagination;
	}
	
    public function getTotal() {
		if (empty($this->_total)) {			
			$this->_total = MiwoDB::loadResult("SELECT COUNT(*) FROM #__{$this->_component}_{$this->_table}".$this->_buildViewWhere());	
		}
		
		return $this->_total;
	}
	
    public function getEditData($table = null) {
		# Get vars
		$cid = MRequest::getVar('cid', array(0), 'method', 'array');
		$id = $cid[0];

        if (empty($table)) {
            $table = ucfirst($this->_component).ucfirst($this->_context);
        }
		
		# Load the record
		if (is_numeric($id)) {
			$row = MiwoVideos::getTable($table); 
			$row->load($id);
		}
	
		return $row;
	}

	public function subscribeToItem($item_id, $item_type = 'channels') {
        $date = MFactory::getDate();
        $user_id = MFactory::getUser()->get('id');
        $channel_id = MiwoVideos::get('channels')->getDefaultChannel()->id;
        $row = MiwoVideos::getTable('MiwovideosSubscriptions');
        $data = array();
        $data['item_id'] = $item_id;
        $data['item_type'] = $item_type;
        $data['user_id'] = $user_id;
        $data['channel_id'] = $channel_id;
        $data['created'] = $date->format('Y-m-d H:i:s');
        if (!$row->bind($data)) {
            $this->setError($row->getError());
            return false;
        }

        if (!$row->store()) {
            $this->setError($row->getError());
            return false;
        }
        //MiwoDB::query("INSERT INTO #__miwovideos_subscriptions (item_id, user_id, channel_id,) VALUES ({$user_id}, {$channel_id})");
        return true;
    }

    public function unsubscribeItem($item_id, $item_type = 'channels') {
        $channel_id = MiwoVideos::get('channels')->getDefaultChannel()->id;
        MiwoDB::query("DELETE FROM #__miwovideos_subscriptions WHERE channel_id = {$channel_id} AND item_id = {$item_id} AND item_type = '{$item_type}'");
        return true;
    }

    public function likeDislikeItem($user_id, $item_id, $item_type, $type) {
        $channel_id = MiwoVideos::get('channels')->getDefaultChannel()->id;
        MiwoDB::query("INSERT INTO #__miwovideos_likes (channel_id, user_id, item_id, item_type, type) VALUES ({$channel_id}, {$user_id}, {$item_id}, '{$item_type}', {$type})");
        return true;
    }

    public function unlikeItem($item_id, $item_type, $type) {
        $channel_id = MiwoVideos::get('channels')->getDefaultChannel()->id;
        MiwoDB::query("DELETE FROM #__miwovideos_likes WHERE channel_id = {$channel_id} AND item_id = {$item_id} AND item_type = '{$item_type}' AND type = {$type}");
        return true;

    }

    public function saveOrder($table) {
        $order = MRequest::getVar('order', array(), 'post');
        $cid = MRequest::getVar('cid', array(), 'post');
        MArrayHelper::toInteger($order);
        MArrayHelper::toInteger($cid);

        $row = MiwoVideos::getTable($table);
        $groupings = array();

        # update ordering values
        $n = count($cid);
        for ($i=0; $i < $n; $i++) {
            $row->load( (int) $cid[$i] );
            # track parents
            if ($this->_context == "categories"){
            	$groupings[] = $row->parent;
            }
            
            if ($row->ordering != $order[$i]) {
                $row->ordering = $order[$i];

                if (!$row->store()) {
                    $this->setError($this->_db->getErrorMsg());
                    return false;
                }
            }
        }

        # execute updateOrder for each parent group
        $groupings = array_unique($groupings);
        foreach ($groupings as $group){
            $row->reorder('parent = '.(int) $group);
        }

        return true;
    }

    public function move($table, $direction) {
        $cid = MRequest::getVar('cid', array(), 'post');
        MArrayHelper::toInteger($cid);
        $id = $cid[0] ;

        $row = MiwoVideos::getTable($table);
        $row->load($id);
        
        $where = '';
        if ($this->_context == "categories"){
        	$where = ' parent = '.(int) $row->parent.' AND ';
        }

        if (!$row->move($direction, $where.' published >= 0 ')) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        return true;
    }

    public function copy($table) {
        $cid = MRequest::getVar('cid', array(), 'post');
        MArrayHelper::toInteger($cid);

        foreach ($cid as $id) {
            $rowOld = MiwoVideos::getTable($table);
            $row = MiwoVideos::getTable($table);

            $rowOld->load($id) ;

            $data = MArrayHelper::fromObject($rowOld) ;
            $data['id'] = 0 ;
            $data['title'] = $data['title']. ' - Copy';

            //Get next ordering
            if ($this->_context == "categories") {
                $sql = "SELECT MAX(ordering + 1) FROM #__{$this->_component}_{$this->_table} WHERE parent = ".$rowOld->parent;
            } else {
                $sql = "SELECT MAX(ordering + 1) FROM #__{$this->_component}_{$this->_table}";
            }

            $this->_db->setQuery($sql) ;
            $data['ordering'] = $this->_db->loadResult();

            $row->bind($data) ;
            $row->store();
        }

        return true ;
    }
	
	public function secureQuery($text, $all = false) {
		static $db;
		
		if (!isset($db)) {
			$db = MFactory::getDBO();
		}
		
		$text = $db->escape($text, true);
		
		if ($all) {
			$text = $db->Quote("%".$text."%", false);
		} else {
			$text = $db->Quote($text, false);
		}
		
		return $text;
	}
	
	public function _getSecureUserState($long_name, $short_name, $default = null, $type = 'none') {
		$request = $this->_mainframe->getUserStateFromRequest($long_name, $short_name, $default, $type);

        $request = MiwoVideos::get('utility')->cleanUrl($request);
		
		if (is_string($request)) {
			$request = str_replace('"', '', $request);
		}
		
		return $request;
	}
	
	# Custom Fields Auto Complete // jQuery
	public function autoComplete($query){
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

    public function getSubscriberCount($item_id, $item_type = 'channels') {
        $sql = "SELECT COUNT(id) FROM #__miwovideos_subscriptions WHERE item_id = {$item_id} AND item_type = '{$item_type}'";
        $this->_db->setQuery($sql);
        return $this->_db->loadResult();
    }

	public function checkSubscription($item_id = NULL, $item_type = 'channels') {
        $channel_id = MiwoVideos::get('channels')->getDefaultChannel()->id;
        if (empty($item_id)) {
            $sql = "SELECT item_id FROM #__miwovideos_subscriptions WHERE channel_id = {$channel_id} AND item_type = '{$item_type}'";
            $this->_db->setQuery($sql);
            return $this->_db->loadAssocList();
        } else {
            $sql = "SELECT * FROM #__miwovideos_subscriptions WHERE item_id = {$item_id} AND item_type = '{$item_type}' AND channel_id = {$channel_id}";
            $this->_db->setQuery($sql);
            return $this->_db->loadResult();
        }
    }

    public function checkLikesDislikes($item_id, $item_type) {
        $channel_id = MiwoVideos::get('channels')->getDefaultChannel()->id;
        $sql = "SELECT type FROM #__miwovideos_likes WHERE channel_id = {$channel_id} AND item_id = {$item_id} AND item_type = '{$item_type}'";
        $this->_db->setQuery($sql);
        return $this->_db->loadResult();
    }

    public function getVideosCategoriesCount($category_id) {
        $where[] = 'v.access IN ('.implode(',', MFactory::getUser()->getAuthorisedViewLevels()).')';
        if ($this->_mainframe->getLanguageFilter()) {
            $where[] = 'v.language IN ('.$this->_db->Quote(MFactory::getLanguage()->getTag()).','.$this->_db->Quote('*').')';
        }
        $where[] = 'vc.category_id = '. $category_id;
        $where[] = 'v.published = 1';

        $where = (count( $where ) ? ' WHERE '. implode(' AND ', $where) : '');

        $sql = "SELECT COUNT(vc.id) FROM #__miwovideos_video_categories vc LEFT JOIN #__miwovideos_videos v ON (vc.video_id = v.id) ".$where;
        $this->_db->setQuery($sql);
        return $this->_db->loadResult();
    }
}