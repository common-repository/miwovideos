<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die('Restricted access');

require_once(MPATH_WP_PLG.'/miwovideos/admin/library/miwovideos.php');

function MiwovideosBuildRoute(&$query) {
    return MiwoVideos::get('router')->buildRoute($query);
}

function MiwovideosParseRoute($segments) {
    return MiwoVideos::get('router')->parseRoute($segments);
}