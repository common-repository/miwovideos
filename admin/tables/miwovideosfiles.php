<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die('Restricted Access');

class TableMiwovideosFiles extends MTable {
    
    public $id 					= 0;
    public $video_id 			= 0;
    public $process_type		= 0;
    public $ext 				= '';
    public $file_size 			= null;
    public $source 				= null;
    public $channel_id			= null;
    public $user_id				= null;
    public $created				= null;
    public $modified			= null;
    public $published 			= 1;

	public function __construct(&$db) {
		parent::__construct('#__miwovideos_files', 'id', $db);
	}
}