<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die('Restricted Access');

class TableMiwovideosVideos extends MTable {
	
    public $id									= 0;
    public $user_id								= null;
    public $channel_id							= 0;
    public $product_id							= 0;
    public $title								= null;
    public $alias								= '';
    public $introtext							= null;
    public $fulltext							= null;
    public $source								= null;
    public $likes								= 0;
    public $dislikes							= 0;
    public $hits								= 0;
    public $created								= null;
    public $modified							= null;
    public $featured							= 0;
    public $published							= 1;
    public $ordering							= 0;
    public $fields								= null;
    public $thumb								= null;
    public $meta_desc 							= '';
    public $meta_key							= '';
    public $meta_author							= '';
    public $language							= '*';

    public $_tableName = 'miwovideos_videos';
    public $tagsHelper = null;
    public $newTags = null;

    protected $isNew = true;

	public function __construct(&$db) {
		parent::__construct('#__miwovideos_videos', 'id', $db);

		if (MiwoVideos::is31()) {
			mimport('framework.helper.tags');
			
			$this->tagsHelper = new JHelperTags();
			$this->tagsHelper->typeAlias = 'com_miwovideos.video';
		}
	}

 	public function check() {
        # Set title
        $this->title = htmlspecialchars($this->title, ENT_QUOTES);

        # Set alias
        $this->alias = MApplication::stringURLSafe($this->alias);
        if (empty($this->alias)) {
            $this->alias = MApplication::stringURLSafe($this->title);
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

    public function bind($src, $ignore = array()) {
        if (isset($src['id']) and !$src['id']) {
            $this->isNew = false;
        }
        return parent::bind($src, $ignore = array());
    }

    public function store($updateNulls = false) {
		if (MiwoVideos::is31()) {
			mimport('framework.helper.tags');
			
			if (!is_object($this->tagsHelper)) {
				$this->tagsHelper = new JHelperTags();
				$this->tagsHelper->typeAlias = 'com_miwovideos.video';
			}

			$this->tagsHelper->preStoreProcess($this);

			parent::store($updateNulls);

			$result = $this->tagsHelper->postStoreProcess($this);
		}
		else {
            $result = parent::store($updateNulls);
		}

        if ($result and $this->isNew) {
            MiwoVideos::get('utility')->trigger('onMiwovideosAfterSaveItem', array($this->id, 'videos', 'uploaded'));
        }

        return $result;
    }
}