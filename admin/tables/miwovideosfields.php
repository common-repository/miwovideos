<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die('Restricted Access');

class TableMiwovideosFields extends MTable {
    
    public $id 					= 0;
    public $name 				= '';
    public $title			 	= '';
    public $description 		= '';
    public $field_type 			= 1;
    public $values 				= '';
    public $default_values		= '';
    public $rows				= '';
    public $cols				= '';
    public $size				= 25;
    public $css_class			= "inputbox";
    public $field_mapping		= '';
    public $ordering			= 0;
    public $language			= '*';
    public $published 			= 1;

	public function __construct(&$db) {
		parent::__construct('#__miwovideos_fields', 'id', $db);
	}
}