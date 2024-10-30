<?php
/**
 * @package        MiwoVideos
 * @copyright      Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license        GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die('Restricted access');

mimport('framework.plugin.plugin');

class MiwovideosCdn extends MPlugin {

    public $url;
    public $buffer;

    public function __construct(&$subject, $config) {
        parent::__construct($subject, $config);
    }

    public function maintenance() {
    }

    public function getCdnLocation() {
    }

    public function createCdnLocation() {
    }

    public function getCdnContents() {
    }

    public function putFile() {
    }

    public function publicUrl($video = null, $fileType = null) {
    }

    public function getLocalQueue() {
        $db = MFactory::getDBO();

        // Select locally videos from the table.
        $query = 'SELECT v.* FROM #__miwovideos_videos AS v WHERE v.source NOT LIKE \'http%\' AND v.source NOT LIKE \'<iframe%\' AND v.source NOT LIKE \'<object%\' AND v.published = 1 ORDER BY v.created ASC';

        $db->setQuery($query);

        return $db->loadObjectList();
    }
}