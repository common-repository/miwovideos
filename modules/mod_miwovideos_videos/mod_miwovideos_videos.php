<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die('Restricted access');

require_once(MPATH_WP_PLG.'/miwovideos/admin/library/miwovideos.php');

$db 		= MFactory::getDBO();
$user 		= MFactory::getUser();
$document 	= MFactory::getDocument();
$app 		= MFactory::getApplication();
$utility    = MiwoVideos::get('utility');
$config 	= MiwoVideos::getConfig();
$width = $params->get('thumb_width', 130);
$height = $params->get('thumb_height', 100);
$numberVideos = $params->get('number_videos', 6);
$categoryIds = $params->get('category_ids', '');
$showCategory = $params->get('show_category', 1);
$showChannel = $params->get('show_channel') ;
$showDescription = $params->get('show_description', 0) ;
$tmpl = $app->getTemplate();
if (file_exists(MPATH_WP_CNT.'/themes/'.$tmpl.'/html/com_miwovideos/assets/css/modules.css') and !MiwoVideos::isDashboard()) {
    $document->addStyleSheet(MURL_WP_CNT.'/themes/'.$tmpl.'/html/com_miwovideos/assets/css/modules.css');
} else {
    $document->addStyleSheet(MURL_MIWOVIDEOS.'/site/assets/css/miwovideosmodules.css');
}
$where = array() ;
$where[] = 'a.published = 1';
//$where[] = 'DATE(created) >= CURDATE()';
//$where[] = '(created = "'.$db->getNullDate().'" OR DATE(created) >= CURDATE())';

if ($categoryIds != '') {
	$where[] = ' a.id IN (SELECT video_id FROM #__miwovideos_video_categories WHERE category_id IN ('.$categoryIds.'))' ;	
}

$where[] = ' a.access IN ('.implode(',', $user->getAuthorisedViewLevels()).')';
if ($app->getLanguageFilter()) {
	$where[] = 'a.language IN (' . $db->Quote(MFactory::getLanguage()->getTag()) . ',' . $db->Quote('*') . ')';
}

$sql = 'SELECT a.id, a.title, a.channel_id, a.created, a.thumb, c.title AS channel_title, a.introtext FROM #__miwovideos_videos AS a '
	 . ' LEFT JOIN #__miwovideos_channels AS c '
	 . ' ON a.channel_id = c.id '
	 . ' WHERE '.implode(' AND ', $where)
	 . ' ORDER BY a.created '
	 . ' LIMIT '.$numberVideos		
;	
$db->setQuery($sql) ;	
$rows = $db->loadObjectList();

for ($i = 0, $n = count($rows); $i < $n; $i++) {
	$row = $rows[$i];

	$sql = 'SELECT a.id, a.title FROM #__miwovideos_categories AS a INNER JOIN #__miwovideos_video_categories AS b ON a.id = b.category_id WHERE b.video_id='.$row->id;
	$db->setQuery($sql) ;
	$categories = $db->loadObjectList();

	if (count($categories)) {
		$itemCategories = array();

		foreach ($categories as  $category) {
            $Itemid = MiwoVideos::get('router')->getItemid(array('view' => 'category', 'category_id' => $category->id), null, true);

			$itemCategories[] = '<a href="'.MRoute::_('index.php?option=com_miwovideos&view=category&category_id='.$category->id . $Itemid).'"><strong>'.$category->title.'</strong></a>';
		}

		$row->categories = implode('&nbsp;|&nbsp;', $itemCategories) ;
	}		
}

$document->addStyleSheet(MURL_WP_CNT.'/miwi/modules/mod_miwovideos_videos/css/style.css');

require(MModuleHelper::getLayoutPath('mod_miwovideos_videos', 'default'));