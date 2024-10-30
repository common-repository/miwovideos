<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die('Restricted Access');

class TableMiwovideosCategories extends MTable {

    public $id 	 		    = 0;
    public $parent 			= 0;
    public $title 			= '';
    public $alias			= '';
    public $thumb			= null;
    public $introtext		= '';
    public $fulltext		= '';
    public $ordering		= 0;
    public $access		    = 1;
    public $language		= '*';
    public $created			= null;
    public $modified		= null;
    public $meta_desc 		= '';
    public $meta_key		= '';
    public $meta_author		= '';
    public $published		= 1;

	public function __construct(&$db) {
		parent::__construct('#__miwovideos_categories', 'id', $db);
	}

    public function check() {
        # Set title
        $this->title = htmlspecialchars($this->title, ENT_QUOTES);

        # Set alias
        $this->alias = MApplication::stringURLSafe($this->alias);
        if (empty($this->alias)) {
            $this->alias = MApplication::stringURLSafe($this->title);
        }

        if (!$this->id) {
            $where = '`parent` = ' . (int) $this->parent;
            $this->ordering = $this->getNextOrder($where);
        }
        
    	# Description Exploding
		$delimiter = "<hr id=\"system-readmore\" />";
				
		if(strpos($this->introtext, $delimiter) == true){
			$exp = explode($delimiter, $this->introtext);
			$this->introtext	= $exp[0];
			$this->fulltext		= $exp[1];
		} else {
			$this->fulltext		= "";
		}

        $this->introtext = htmlspecialchars($this->introtext, ENT_QUOTES);
        $this->fulltext  = htmlspecialchars($this->fulltext, ENT_QUOTES);

        return true;
    }
}