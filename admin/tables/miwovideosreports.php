<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die('Restricted Access');

class TableMiwovideosReports extends MTable {

    public $id 	 		    	= 0;
    public $channel_id 	 		= 0;
    public $user_id 	 		= 0;
    public $item_id 	 		= 0;
    public $item_type 	 		= '';
    public $reason_id	 		= 0;
    public $note	 			= '';
    public $created				= null;
    public $modified			= null;

	public function __construct($db) {
		parent::__construct('#__miwovideos_reports', 'id', $db);
	}
}