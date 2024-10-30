<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die ;

class MiwovideosControllerPlaylists extends MiwoVideosController {
	
	public function __construct($config = array()) {
		parent::__construct('playlists');
	}
}