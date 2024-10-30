<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die ;

class MiwovideosModelSubscriptions extends MiwovideosModel {

    public function __construct() {
        parent::__construct('subscriptions');

        $this->acl = MiwoVideos::get('acl');
        $this->user = MFactory::getUser();

        $task = MRequest::getCmd('task');
        $tasks = array('apply', 'save', 'save2new');

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
        $this->filter_order            	= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_order',         	'filter_order',         's.user_id');
        $this->filter_order_Dir        	= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_order_Dir',        	'filter_order_Dir',     'ASC');
        $this->filter_user        		= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_user',         		'filter_user',     		'');
        $this->search                	= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.search',                 	'search',               '');
        $this->search                  	= MString::strtolower($this->search);
    }
	
    public function _buildViewQuery() {
        $where = $this->_buildViewWhere();

        $orderby = "";
        if (!empty($this->filter_order) and !empty($this->filter_order_Dir)) {
            $orderby = " ORDER BY {$this->filter_order} {$this->filter_order_Dir}";
        }

        $this->_query = "SELECT s.id, s.channel_id, c.title, u.ID AS user_id, u.user_login username ".
                        "FROM #__miwovideos_subscriptions s ".
                        "LEFT JOIN #__users AS u ON (s.user_id = u.ID) ".
                        "LEFT JOIN #__miwovideos_channels AS c ON (c.id = s.channel_id) ".
                        $where.
                        $orderby;
        $this->_query;
    }

    public function _buildViewWhere() {
        $where = array();
        
        if ($this->search) {
        	$search = parent::secureQuery(trim($this->search), true);
        	
            if ($search != NULL){
            	$where[] = "(c.title LIKE {$search})";
            } else {
                $where[] = null;
            }
        }

        if (!$this->acl->canManage()) {
            $where[] = 's.user_id = '.$this->user->get('id');
        }
        else if ($this->filter_user) {
            $where[] = 's.user_id = '.$this->filter_user;
        }
        
        $where = (count($where) ? ' WHERE '. implode(' AND ', $where) : '');

        return $where;
    }
    
	public function getTotal() {
		if (empty($this->_total)) {	
			$this->_total = MiwoDB::loadResult("SELECT COUNT(*) FROM #__miwovideos_subscriptions s ".$this->_buildViewWhere());	
		}
		
		return $this->_total;
	}
}