<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
// No direct access to this file
defined('MIWI') or die('Restricted Access');

class TableMiwovideosProcesses extends MTable {

    public $id 	 		    	= 0;
    public $process_type 		= 0;
    public $video_id 	 		= 0;
    public $status 	 			= 0;
    public $attempts		    = 0;
    public $checked_out		    = 0;
    public $checked_out_time	= NULL;
    public $params				= '';
    public $created_user_id		= 0;
    public $created				= NULL;
    public $modified_user_id	= 0;
    public $modified			= NULL;
    public $published			= 1;

	public function __construct($db) {
		parent::__construct('#__miwovideos_processes', 'id', $db);
	}
}
