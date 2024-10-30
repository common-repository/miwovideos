<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die('Restricted Access');

class TableMiwovideosSubscriptions extends MTable {

    public $id 	 		    	= 0;
    public $item_id 		 	= null;
    public $item_type 		 	= 'channels';
    public $user_id 		 	= null;
    public $channel_id 	 		= null;
    public $created 		 	= null;

	public function __construct($db) {
		parent::__construct('#__miwovideos_subscriptions', 'id', $db);
	}
	
	public function check() {
        return true;
    }
}