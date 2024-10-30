<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die('Restricted Access');

define('MPATH_MIWOVIDEOS', MPATH_WP_PLG.'/miwovideos/site');
define('MPATH_MIWOVIDEOS_ADMIN', MPATH_WP_PLG.'/miwovideos/admin');
define('MPATH_MIWOVIDEOS_LIB', MPATH_MIWOVIDEOS_ADMIN.'/library');
define('MIWOVIDEOS_UPLOAD_DIR', MPATH_MEDIA.'/miwovideos');

if (!class_exists('MiwoDB')) {
	MLoader::register('MiwoDB', MPATH_MIWOVIDEOS_LIB.'/database.php');
}

if (MFactory::$application->isAdmin()) {
    $_side = MPATH_ADMINISTRATOR;
}
else {
    $_side = MPATH_SITE;
}

$_lang = MFactory::getLanguage();
$_lang->load('com_miwovideos', $_side, 'en-GB', true);
$_lang->load('com_miwovideos', $_side, $_lang->getDefault(), true);
$_lang->load('com_miwovideos', $_side, null, true);

MTable::addIncludePath(MPATH_MIWOVIDEOS_ADMIN.'/tables');

MLoader::register('MiwovideosController', MPATH_MIWOVIDEOS_ADMIN.'/library/controller.php');
MLoader::register('MiwovideosModel', MPATH_MIWOVIDEOS_ADMIN.'/library/model.php');
MLoader::register('MiwovideosView', MPATH_MIWOVIDEOS_ADMIN.'/library/view.php');

// Register MiwoVideos logger
MLog::addLogger(array('text_file' => 'miwovideos.log.php', 'text_entry_format' => '{DATETIME} {PRIORITY} {MESSAGE}'), MLog::ALL, array('miwovideos'));

if (!MiwoVideos::is30()) {
	MLoader::register('MHtmlString', MPATH_MIWI.'/framework/html/html/string.php');
}
