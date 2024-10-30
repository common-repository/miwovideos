<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die ;

class MiwovideosModelFiles extends MiwovideosModel {

    public function __construct() {
		parent::__construct('files');

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
		$this->filter_order			= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_order',			'filter_order',			'id');
		$this->filter_order_Dir		= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_order_Dir',		'filter_order_Dir',		'ASC');
		//$this->filter_process	    = parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_process', 	    'filter_process', 	    '');
		$this->filter_published	    = parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_published', 	'filter_published', 	'');
		$this->search				= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.search', 				'search', 				'');
		$this->search 	 			= MString::strtolower($this->search);
	}

    public function getTotal() {
        if (empty($this->_total)) {
            $this->_total = MiwoDB::loadResult("SELECT COUNT(f.id) FROM #__miwovideos_files AS f");
        }

        return $this->_total;
    }

    public function _buildViewQuery() {
        $where = self::_buildViewWhere();

        $orderby = "";
        if (!empty($this->filter_order) and !empty($this->filter_order_Dir)) {
            $orderby = " ORDER BY {$this->filter_order} {$this->filter_order_Dir}";
        }

        $this->_query = 'SELECT f.*, v.title AS video_title, v.user_id '.
                        'FROM #__miwovideos_files AS f '.
                        'LEFT JOIN #__miwovideos_videos AS v '.
                        'ON f.video_id = v.id '.
                        $where.' '.
                        $orderby;
    }

    public function _buildViewWhere() {
		$where = array();			
		
		if ($this->search) {
            $src = parent::secureQuery($this->search, true);
			$where[] = "LOWER(v.title) LIKE {$src}";
		}

		/*if (!empty($this->filter_process)) {
			$where[] = 'process_type = "'.$this->filter_process.'"';
		}*/

		if (is_numeric($this->filter_published)) {
			$where[] = 'f.published = '.(int) $this->filter_published;
		}
						
		$where = (count($where) ? ' WHERE '. implode(' AND ', $where) : '');
		
		return $where;
	}

    public function delete($ids) {
        if (empty($ids)) {
            return false;
        }

        if (!MiwoVideos::get('files')->delete($ids)) {
            return false;
        }

        if (!MiwoDB::query("DELETE FROM #__miwovideos_files WHERE id IN (".implode(',', $ids).")")) {
            return false;
        }

        return true;
    }
}