<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die('Restricted Access');

class TableMiwovideosReasons extends MTable {
    
    public $id 					= 0;
    public $parent 				= NULL;
    public $title			 	= '';
    public $alias			 	= '';
    public $description 		= '';
    public $access 				= 0;
    public $language			= '*';
    public $association 		= 0;
    public $published 			= 1;
    public $created				= null;
    public $modified			= null;

	public function __construct(&$db) {
		parent::__construct('#__miwovideos_report_reasons', 'id', $db);
	}
}