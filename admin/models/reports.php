<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die ;

class MiwovideosModelReports extends MiwovideosModel {
	
	public $process;
	
    public function __construct() {
		parent::__construct('reports');
		
		$this->_getUserStates();
		$this->_buildViewQuery();
	}
	
	public function _getUserStates(){
		$this->filter_order			= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_order',			'filter_order',			'r.created');
		$this->filter_order_Dir		= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_order_Dir',		'filter_order_Dir',		'DESC');
		$this->filter_reason	    = parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_reason', 	    'filter_reason', 	    '');
		$this->filter_type	    	= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_type', 	    	'filter_type', 	    	'');
		$this->filter_language	    = parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_language', 	    'filter_language', 	    '');
		$this->search				= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.search', 				'search', 				'');
		$this->search 	 			= MString::strtolower($this->search);
	}

    public function getItems() {
        if (empty($this->_data)) {
            $rows = parent::getItems();

            foreach ($rows as $row) {
                $channel = MiwoVideos::get('channels')->getDefaultChannel();
                $row->channel_title = $channel->title;

                if ($row->item_type == "videos") {
                    $sql = "SELECT v.title FROM #__miwovideos_videos AS v WHERE v.id = {$row->item_id}";
                } else {
                    $sql = "SELECT c.title FROM #__miwovideos_channels AS c WHERE c.id = {$row->item_id}";
                }
                $this->_db->setQuery($sql);

                $row->item_title = $this->_db->loadResult();
            }

            $pagination = parent::getPagination();
            $rows = array_slice($rows, 0, $pagination->limit);

            $this->_data = $rows;
        }

        return $this->_data;
    }

    public function _buildViewQuery() {
        $where = self::_buildViewWhere();

        $orderby = "";
        if (!empty($this->filter_order) and !empty($this->filter_order_Dir)) {
            $orderby = " ORDER BY {$this->filter_order} {$this->filter_order_Dir}";
        }

        $this->_query = 'SELECT rs.*,r.* FROM #__miwovideos_reports AS r '
            .'LEFT JOIN #__miwovideos_report_reasons AS rs '
            .'ON (rs.id = r.reason_id) '
            . $where
            .' GROUP BY r.id '
            . $orderby
        ;
    }

	public function _buildViewWhere() {
		$where = array();

        if ($this->search) {
            $src = parent::secureQuery($this->search, true);
            $where[] = "LOWER(rs.title) LIKE {$src}";
        }

		if ($this->filter_reason) {
            $where[] = 'rs.association IN ('.$this->filter_reason.')';
        }

		if ($this->filter_type) {
            $where[] = 'r.item_type="'.$this->filter_type.'"';
        }

        if ($this->filter_language) {
            $where[] = 'rs.language IN (' . $this->_db->Quote($this->filter_language) . ',' . $this->_db->Quote('*') . ')';
        }
			
		$where = (count($where) ? ' WHERE '. implode(' AND ', $where) : '');
		
		return $where;
	}

    public function getTotal() {
		if (empty($this->_total)) {
			$this->_total = MiwoDB::loadResult("SELECT COUNT(*) FROM #__{$this->_component}_{$this->_table} AS r LEFT JOIN #__miwovideos_report_reasons AS rs ON (rs.id=r.reason_id)".$this->_buildViewWhere());
		}

		return $this->_total;
	}

    public function getReasons() {
		return MiwoDB::loadObjectList('SELECT id, title FROM #__miwovideos_report_reasons WHERE language IN ('. $this->_db->Quote('*') .', '. $this->_db->Quote(MFactory::getLanguage()->getTag()) .')');
	}

   
	######################################################################################################################################################################################################
}