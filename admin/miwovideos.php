<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die('Restricted Access');

# Access check
if (!MFactory::getUser()->authorise('core.manage', 'com_miwovideos')) {
	return MError::raiseWarning(404, MText::_('JERROR_ALERTNOAUTHOR'));
}

MHtml::_('behavior.framework');

require_once(MPATH_WP_PLG.'/miwovideos/admin/library/miwovideos.php');


if (!MiwoVideos::get('utility')->checkRequirements('admin')) {
    return;
}


$task = MiwoVideos::getInput()->getCmd('task', '');

if (!(($task == 'add' or $task == 'edit') and MiwoVideos::is30())) {
    require_once(MPATH_MIWOVIDEOS_ADMIN.'/toolbar.php');
}

if ($view = MiwoVideos::getInput()->getCmd('view', '')) {
    if ($view == 'videos' and $task == 'add') {
        $view = 'upload';
        MiwoVideos::getInput()->setVar('view', $view);
    }

    $path = MPATH_MIWOVIDEOS_ADMIN.'/controllers/'.$view.'.php';

	if (file_exists($path)) {
		require_once($path);
	} else {
		$view = '';
	}
}

$class_name = 'MiwovideosController'.$view;

$controller = new $class_name();
$controller->execute(MiwoVideos::getInput()->getCmd('task', ''));
$controller->redirect();