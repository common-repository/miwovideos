<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die('Restricted access');


if ($params->get('position') != "0" and !@isset($attribs['home']) and !@$attribs['home']) {
    return false;
}

require_once(MPATH_WP_PLG.'/miwovideos/admin/library/miwovideos.php');

$db 		= MFactory::getDBO();
$user 		= MFactory::getUser();
$document 	= MFactory::getDocument();
$app 		= MFactory::getApplication();
$utility 	= MiwoVideos::get('utility');
$config 	= MiwoVideos::getConfig();
$width 		= $params->get('thumb_width', 130);
$height 	= $params->get('thumb_height', 100);
$tmpl 		= $app->getTemplate();

if (file_exists(MPATH_WP_CNT.'/themes/'.$tmpl.'/html/com_miwovideos/assets/css/modules.css') and !MiwoVideos::isDashboard()) {
    $document->addStyleSheet(MURL_WP_CNT.'/themes/'.$tmpl.'/html/com_miwovideos/assets/css/modules.css');
} else {
    $document->addStyleSheet(MURL_MIWOVIDEOS.'/site/assets/css/miwovideosmodules.css');
}

$numberVideos = $params->get('number_videos', 6);
$filterby = $params->get('filterby', 1);

if ($filterby == 1) {
    $filterbywhere = 'hits';
} else{
    $filterbywhere = 'likes';
}

$extraWhere = '' ;
if ($app->getLanguageFilter()) {
	$extraWhere = ' AND language IN (' . $db->Quote(MFactory::getLanguage()->getTag()) . ',' . $db->Quote('*') . ')';
}

$sql = 'SELECT * FROM #__miwovideos_videos'
		.' WHERE published = 1 '
		.' AND access IN ('.implode(',', $user->getAuthorisedViewLevels()).')' . $extraWhere
		.' ORDER BY '.$filterbywhere . ' DESC'
		.($numberVideos ? ' LIMIT '.$numberVideos : '');

$db->setQuery($sql) ;
$rows = $db->loadObjectList() ;

require(MModuleHelper::getLayoutPath('mod_miwovideos_videos_popular', 'default'));