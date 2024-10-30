<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die ;

class MiwovideosModelReasons extends MiwovideosModel {
	
	public $process;
	
    public function __construct() {
		parent::__construct('reasons');

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

    public function store(&$data) {
        $row = MiwoVideos::getTable('MiwovideosReasons');

        if (!$row->bind($data)) {
            return false;
        }

        if (!$row->check($data)) {
            return false;
        }

        if (!$row->store()) {
            return false;
        }

        $data['id'] = $row->id;

        return true;
    }
	
	public function _getUserStates(){
		$this->filter_order			= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_order',			'filter_order',			'rs.title');
		$this->filter_order_Dir		= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_order_Dir',		'filter_order_Dir',		'ASC');
		$this->filter_published	    = parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_published', 	'filter_published', 	'');
		$this->filter_access	    = parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_access', 	    'filter_access', 	    '');
		$this->filter_language	    = parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_language', 	    'filter_language', 	    '');
		$this->search				= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.search', 				'search', 				'');
		$this->search 	 			= MString::strtolower($this->search);
	}	

    public function _buildViewQuery() {
        $where = self::_buildViewWhere();

        $orderby = "";
        if (!empty($this->filter_order) and !empty($this->filter_order_Dir)) {
            $orderby = " ORDER BY {$this->filter_order} {$this->filter_order_Dir}";
        }

        $this->_query = 'SELECT rs.* FROM #__miwovideos_report_reasons AS rs '
            . $where
            .' GROUP BY rs.id '
            . $orderby
        ;
    }

	public function _buildViewWhere() {
		$where = array();

        if ($this->search) {
            $src = parent::secureQuery($this->search, true);
            $where[] = "LOWER(rs.title) LIKE {$src}";
        }
				
		if (is_numeric($this->filter_published)) {
			$where[] = 'rs.published = '.(int) $this->filter_published;
		}

        if (is_numeric($this->filter_access)) {
            $where[] = 'rs.access = '.(int) $this->filter_access;
        }

        if ($this->filter_language) {
            $where[] = 'rs.language IN (' . $this->_db->Quote($this->filter_language) . ',' . $this->_db->Quote('*') . ')';
        }
			
		$where = (count($where) ? ' WHERE '. implode(' AND ', $where) : '');
		
		return $where;
	}

    public function getTotal() {
		if (empty($this->_total)) {
			$this->_total = MiwoDB::loadResult("SELECT COUNT(*) FROM #__{$this->_component}_report_{$this->_table} AS rs".$this->_buildViewWhere());
		}

		return $this->_total;
	}

    public function getAssociation() {
        $config = MFactory::getConfig();
        $lang = $config->get('language');
        $cid = MRequest::getVar('cid', array(0), '', 'array');
		$result = MiwoDB::loadObjectList("SELECT * FROM #__{$this->_component}_report_reasons AS rs WHERE rs.language = '{$lang}' AND rs.id <> {$cid[0]}");

		return $result;
	}
    

   
	######################################################################################################################################################################################################
}