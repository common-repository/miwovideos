<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die ;

class MiwovideosModelCategories extends MiwovideosModel {

    public function __construct() {
		parent::__construct('categories');

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
        $this->filter_order			= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_order',			'filter_order',			'c.title');
        $this->filter_order_Dir		= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_order_Dir',		'filter_order_Dir',		'ASC');
        $this->filter_parent	    = parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_parent', 	    'filter_parent', 	    '0');
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
            $orderby = " ORDER BY c.parent, {$this->filter_order} {$this->filter_order_Dir}";
        }

        $this->_query = 'SELECT c.*, c.parent AS parent_id, c.title AS title, COUNT(vc.id) AS total_videos '.
                        'FROM #__miwovideos_categories AS c '.
                        'LEFT JOIN #__miwovideos_video_categories AS vc '.
                        'ON c.id = vc.category_id '.
                        $where.' '.
                        'GROUP BY c.id '.
                        $orderby;
    }

    public function _buildViewWhere() {
        $where = array();

        if (!empty($this->search)) {
            $src = parent::secureQuery($this->search, true);
            $where[] = "(LOWER(c.title) LIKE {$src} OR LOWER(c.introtext) LIKE {$src} OR LOWER(c.fulltext) LIKE {$src})";
        }

        if (!empty($this->filter_parent)) {
            $where[] = "c.parent = {$this->filter_parent}";
        }

        if (is_numeric($this->filter_published)) {
            $where[] = 'c.published = '.(int) $this->filter_published;
        }

        if (is_numeric($this->filter_access)) {
            $where[] = 'c.access = '.(int) $this->filter_access;
        }

        if ($this->filter_language) {
            $where[] = 'c.language IN (' . $this->_db->Quote($this->filter_language) . ',' . $this->_db->Quote('*') . ')';
        }

        $where = (count( $where ) ? ' WHERE '. implode(' AND ', $where) : '');

        return $where;
    }

    public function getItems() {
		if (empty($this->_data)) {
			$rows = parent::getItems();

			$children = array();
			
			if (count($rows)) {
				foreach ($rows as $v) {
					$pt = $v->parent;
					$list = @$children[$pt] ? $children[$pt] : array();
					array_push($list, $v);
					$children[$pt] = $list;
				}	
			}

			$list = MHtml::_('menu.treerecurse', $this->filter_parent, '', array(), $children, 9999);

			$pagination = parent::getPagination();
			$list = array_slice($list, 0, $pagination->limit);

			$this->_data = $list;
		}

		return $this->_data;
	}

	public function getTotal() {
		if (empty($this->_total)) {
			$this->_total = MiwoDB::loadResult("SELECT COUNT(*) FROM #__miwovideos_{$this->_table} AS c".$this->_buildViewWhere());
		}
	
		return $this->_total;
	}

    public function getEditData($table = NULL) {
        if (empty($this->_data)) {
            $row = parent::getEditData();

            if (empty($this->_id) and !empty($this->filter_parent)) {
                $row->parent = $this->filter_parent;
            }

            $this->_data = $row;
        }

        return $this->_data;
    }
}