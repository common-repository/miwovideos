<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die('Restricted Access');

require_once(MPATH_WP_PLG.'/miwovideos/admin/library/miwovideos.php');

if (!MiwoVideos::get('utility')->checkRequirements('site')) {
    return;
}

$view = MiwoVideos::getInput()->getCmd('view', '');
$task = MiwoVideos::getInput()->getCmd('task', '');

if (MiwoVideos::isDashboard()) {
    require_once(MPATH_MIWOVIDEOS.'/controllers/dashboard.php');

    $controller = new MiwovideosControllerDashboard();
    $controller->execute($task);
    $controller->redirect();
    return;
}

if (empty($view)) {
    $view = 'categories';
    MRequest::setVar('view', 'categories');
}

if ($view) {
	$path = MPATH_MIWOVIDEOS.'/controllers/'.$view.'.php';

	if (file_exists($path)) {
		require_once($path);
	}
    else {
		$view = '';
	}
}

$class_name = 'MiwovideosController'.$view;

$controller = new $class_name();
$controller->execute($task);
$controller->redirect();