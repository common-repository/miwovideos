<?php
/**
 * @package        MiwoVideos
 * @copyright      Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license        GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die('Restricted Access');

if (!class_exists('MiwisoftComponentRouterBase')) {
	if (class_exists('JComponentRouterBase')) {
		abstract class MiwisoftComponentRouterBase extends JComponentRouterBase {}
	}
	else {
		class MiwisoftComponentRouterBase {}
	}
}

class MiwoVideosRouter extends MiwisoftComponentRouterBase {

	public function build(&$query) {
		return $this->buildRoute($query);
	}

	public function buildRoute(&$query) {
		$segments = array();

		$menu = MiwoVideos::get('utility')->getMenu();

		if (!empty($query['Itemid'])) {
			$Itemid = $query['Itemid'];
		}
		else {
			$Itemid = $this->getItemid($query, null, false);
		}

		if (empty($Itemid)) {
			$a_menu = $menu->getActive();
		}
		else {
			$a_menu = $menu->getItem($Itemid);
		}

		if (isset($query['view']) and !isset($query['dashboard'])) {
			$view = $query['view'];

			switch ($query['view']) {
				case 'video':
				case 'category':
				case 'channel':
				case 'playlist':
					$id_var = $view.'_id';

					if (is_object($a_menu) and ($a_menu->query['view'] == $view) and (@$a_menu->query[ $id_var ] == @$query[ $id_var ])) {
						unset($query[ $id_var ]);
						break;
					}

					$segments[] = $view;

					if (isset($query[ $id_var ])) {
						$alias = $this->getRecordAlias($query[ $id_var ], $view);

						$segments[] = $query[ $id_var ].':'.$alias;
						unset($query[ $id_var ]);
					}

					break;
				default:
					if (is_object($a_menu) and ($a_menu->query['view'] == $view)) {
						$brk = true;

						if (isset($query['layout'])) {
							if (!isset($a_menu->query['layout']) or (isset($a_menu->query['layout']) and ($a_menu->query['layout'] != $query['layout']))) {
								$brk = false;
							}
						}

						if (isset($a_menu->query['layout'])) {
							if (!isset($query['layout']) or (isset($query['layout']) and ($query['layout'] != $a_menu->query['layout']))) {
								$brk = false;
							}
						}

						if ($brk == true) {
							unset($query['layout']);
							break;
						}
					}

					$segments[] = $view;

					if (isset($query['layout'])) {
						$segments[] = $query['layout'];
						unset($query['layout']);
					}

					break;
			}

			unset($query['view']);
		}

		foreach ($segments as $key => $segment) {
			$segments[ $key ] = str_replace(':', '-', $segment);
		}

		return $segments;

	}

	public function getItemid($vars = array(), $params = null, $with_name = true) {
		$ret = '';

		unset($vars['Itemid']);

		$item = $this->findItemid($vars, $params);

		if (!empty($item->id)) {
			if ($with_name == true) {
				$ret = '&Itemid='.$item->id;
			}
			else {
				$ret = $item->id;
			}

			return $ret;
		}

		return $ret;
	}

	public function findItemid($vars = array(), $params = null) {
		static $items;

		$menus    = MiwoVideos::get('utility')->getMenu();
		$menuitem = $menus->getActive();

		if (!isset($items)) {
			$component = MComponentHelper::getComponent('com_miwovideos');
			$items     = $menus->getItems('component_id', $component->id);
		}

		if (empty($items)) {
			return null;
		}

		if (empty($vars) || !is_array($vars)) {
			$vars = array();
		}

		// First check the current menu item (fastest shortcut!)
		if (is_object($menuitem)) {
			if ($this->_checkMenu($menuitem, $vars, $params)) {
				return $menuitem;
			}
		}

		$found_menu = null;
		foreach ($items as $item) {
			if (!is_object($item) or !isset($item->query)) {
				continue;
			}

			if (count($vars) == 1) {
				return $item;
			}

			if (empty($found_menu)) {
				$found_menu = $item;
            }

			if ($item->id == MRequest::getInt('Itemid') and isset($vars['view']) and $vars['view'] == MRequest::getWord('view')) {
				$found_menu = $item;
			}

			if ($this->_checkMenu($item, $vars, $params)) {
				$found_menu = $item;
			}
		}

		return $found_menu;
	}

	protected function _checkMenu($item, $vars, $params = null) {
		$query = $item->query;

		unset($vars['option']);
		unset($query['option']);

		foreach ($vars as $key => $value) {
			if (is_null($value)) {
				continue;
			}

			if (!isset($query[ $key ])) {
				return false;
			}

			if ($query[ $key ] != $value) {
				return false;
			}
		}

		if (!is_null($params)) {
			$menus = MiwoVideos::get('utility')->getMenu();
			$check = $item->params instanceof JParameter ? $item->params : $menus->getParams($item->id);

			foreach ($params as $key => $value) {
				if (is_null($value)) {
					continue;
				}

				if ($check->get($key) != $value) {
					return false;
				}
			}
		}

		return true;
	}

	public function getRecordAlias($id, $type = 'video') {
		$id = intval($id);

		if (empty($id)) {
			return '';
		}

		static $rows = array('video' => array(), 'category' => array(), 'channel' => array(), 'playlist' => array());

		if (!isset($rows[ $type ][ $id ])) {
			$table = $type.'s';

			if ($table == 'categorys') {
				$table = 'categories';
			}

			$_name = MiwoDB::loadResult("SELECT alias FROM #__miwovideos_{$table} WHERE id = '{$id}'");

			$rows[ $type ][ $id ] = MFilterOutput::stringURLSafe(html_entity_decode($_name, ENT_QUOTES, 'UTF-8'));
		}

		return $rows[ $type ][ $id ];
	}

	public function parse(&$segments) {
		return $this->parseRoute($segments);
	}

	public function parseRoute($segments) {
		$count = count($segments);

		if ($count == 1) {
			$vars['view'] = $segments[0];

			return $vars;
		}

		switch ($segments[0]) {
			case 'video':
			case 'category':
			case 'channel':
			case 'playlist':
				$vars['view'] = $segments[0];

				if (isset($segments[2])) {
					$vars['layout'] = $segments[1];
					$alias          = $segments[2];
				}
				else {
					$alias = $segments[1];
				}

				$id                         = explode(':', $alias);
				$vars[ $segments[0].'_id' ] = (int)$id[0];

				break;
			default:
				$vars['view'] = $segments[0];

				if (isset($segments[1])) {
					$vars['layout'] = $segments[1];
				}

				break;
		}

		return $vars;
	}
}