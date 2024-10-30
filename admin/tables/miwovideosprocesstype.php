<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
// No direct access to this file
defined('MIWI') or die('Restricted Access');

class TableMiwovideosProcesstype extends MTable {

    public $id 	 		    = 0;
    public $title 			= '';
    public $alias			= '';
    public $filetype		= null;
    public $size			= null;
    public $ordering		= 0;
    public $published		= 1;

	public function __construct(&$db) {
		parent::__construct('#__miwovideos_process_type', 'id', $db);
	}

    public function check() {
        # Set title
        $this->title = htmlspecialchars($this->title, ENT_QUOTES);

        # Set alias
        $this->alias = MApplication::stringURLSafe($this->alias);
        if (empty($this->alias)) {
            $this->alias = MApplication::stringURLSafe($this->title);
        }
	}
}
