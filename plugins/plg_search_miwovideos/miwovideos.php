<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die('Restricted access');

mimport('framework.plugin.plugin');
require_once(MPATH_WP_PLG.'/miwovideos/admin/library/miwovideos.php');

class plgSearchMiwovideos extends MPlugin {
	
	public function __construct(&$subject, $config) {
		parent::__construct($subject, $config);
		
		$this->loadLanguage();
	}

	public function onContentSearchAreas()	{
		static $areas = array('miwovideos' => 'COM_MIWOVIDEOS_VIDEOS');
		
		return $areas;
	}

	public function onContentSearch($text, $phrase = '', $ordering = '', $areas = null, $context = null) {
        if ($context != 'miwovideos') {
            return array();
        }
		if (is_array($areas)) {
			if (!array_intersect($areas, array_keys($this->onContentSearchAreas()))) {
				return array();
			}
		}
		
		$text = trim($text);
		if ($text == '') {
			return array();
		}
		
		$db	= MFactory::getDBO();
		$user = MFactory::getUser();
		$limit = $this->params->get('search_limit', 50);
		$Itemid = MiwoVideos::get('router')->getItemid();
		
		$section = MText::_('COM_MIWOVIDEOS_VIDEOS');

        $wheres = array();
		
		switch ($phrase) {
			case 'exact':
				$text = $db->Quote('%'.$db->escape($text, true).'%', false);

                $wheres[] 	= 'a.title LIKE '.$text;
                $wheres[] 	= 'a.introtext LIKE '.$text;
                $wheres[] 	= 'a.fulltext LIKE '.$text;
				
				$where = '(' . implode(') OR (', $wheres) . ')';

				break;
			case 'all':
			case 'any':
			default:
				$words = explode(' ', $text);

				foreach ($words as $word) {
					$word = $db->Quote('%'.$db->escape($word, true).'%', false);
					
					$wheres2 	= array();
					$wheres2[] 	= 'a.title LIKE '.$word;
					$wheres2[] 	= 'a.introtext LIKE '.$word;
					$wheres2[] 	= 'a.fulltext LIKE '.$word;

					$wheres[] 	= implode(' OR ', $wheres2);
				}
				
				$where = '(' . implode(($phrase == 'all' ? ') AND (' : ') OR ('), $wheres) . ')';

				break;
		}
	
		switch ($ordering) {
			case 'oldest':
				$order = 'a.created DESC';
				break;		
			case 'alpha':
				$order = 'a.title ASC';
				break;
			case 'newest':
				$order = 'a.created ASC';
                break;
			default:
				$order = 'a.created';
		}
		
		$query = 'SELECT a.id AS ID, a.title AS post_title, a.introtext AS post_content, created AS `post_date`, '.$db->Quote($section).' AS section, "0" AS browsernav '
				.'FROM #__miwovideos_videos AS a '
				.'WHERE ('.$where.') AND  a.published = 1 '
				.'ORDER BY '.$order;
				
		$db->setQuery($query, 0, $limit);
		$rows = $db->loadObjectList();
		
		if (count($rows)) {
			foreach($rows as $key => $row) {
				$rows[$key]->href = MRoute::_('index.php?option=com_miwovideos&view=video&video_id='.$row->ID.$Itemid);
                  $rows[$key]->post_title   = html_entity_decode($row->post_title);
                $rows[$key]->post_content = html_entity_decode($row->post_content);
				$rows[$key]->text = strip_tags(html_entity_decode($rows[$key]->text));
			}
		}
		
		return $rows;
	}	
}
