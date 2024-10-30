<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die('Restricted Access');

# Backup/Restore class
class MiwovideosBackupRestore {
	
	protected $_dbprefix;
	protected $_table;
	protected $_where;
	protected $_jstatus;

    public function __construct($options = "") {
		if (is_array($options)) {
			if (isset($options['_table'])) {
				$this->_table = $options['_table'];
			}
			
			if (isset($options['_where'])) {
				$this->_where = $options['_where'];
			}
		}
		
		$this->_jstatus = MiwoVideos::is30();
		
		$this->_dbprefix = MFactory::getConfig()->get('dbprefix');
	}

    # MiwoVideos : Backup
    public function backupCategories() {
        $filename = "miwovideos_categories.sql";
        $fields = array('id', 'parent', 'title', 'alias', 'description', 'thumb', 'introtext', 'fulltext', 'ordering', 'access', 'language', 'created', 'modified', 'type', 'meta_desc', 'meta_key', 'meta_author', 'published');
        $line = "INSERT IGNORE INTO {$this->_dbprefix}miwovideos_{$this->_table} (".implode(', ', $fields).")";
        $query = "SELECT `id`, `parent`, `title`, `alias`, `description`, `thumb`, `introtext`, `fulltext`, `ordering`, `access`, `language`, `created`, `modified`, `type`, `meta_desc`, `meta_key`, `meta_author`, `published` FROM {$this->_dbprefix}miwovideos_{$this->_table} {$this->_where}";

        return array($query, $filename, $fields, $line);
    }

    public function backupChannels() {
        $filename = "miwovideos_channels.sql";
        $fields = array('id', 'user_id', 'title', 'alias', 'introtext', 'fulltext', 'thumb', 'banner', 'fields', 'likes', 'dislikes', 'hits', 'params', 'ordering', 'access', 'language', 'created', 'modified', 'category',
            'meta_desc', 'meta_key', 'meta_author', 'share_others', 'featured', 'published', 'default');
        $line = "INSERT IGNORE INTO {$this->_dbprefix}miwovideos_{$this->_table} (".implode(', ', $fields).")";
        $query = "SELECT `id`, `user_id`, `title`, `alias`, `introtext`, `fulltext`, `thumb`, `banner`, `fields`, `likes`, `dislikes`, `hits`, `params`, `ordering`, `access`, `language`, `created`, `modified`, `category`,".
            "`meta_desc`, `meta_key`, `meta_author`, `share_others`, `featured`, `published`, `default` FROM {$this->_dbprefix}miwovideos_{$this->_table} {$this->_where}";

        return array($query, $filename, $fields, $line);
    }

    public function backupPlaylists() {
        $filename = "miwovideos_playlists.sql";
        $fields = array('id', 'channel_id', 'user_id', 'type', 'title', 'alias', 'introtext', 'fulltext', 'thumb', 'fields', 'likes', 'dislikes', 'hits', 'subscriptions', 'params',
            'ordering', 'access', 'language', 'created', 'modified', 'meta_desc', 'meta_key', 'meta_author', 'share_others', 'featured', 'published');
        $line = "INSERT IGNORE INTO {$this->_dbprefix}miwovideos_{$this->_table} (".implode(', ', $fields).")";
        $query = "SELECT `id`, `channel_id`, `user_id`, `type`, `title`, `alias`, `introtext`, `fulltext`, `thumb`, `fields`, `likes`, `dislikes`, `hits`, `subscriptions`, `params`,".
            " `ordering`, `access`, `language`, `created`, `modified`, `meta_desc`, `meta_key`, `meta_author`, `share_others`, `featured`, `published` FROM {$this->_dbprefix}miwovideos_{$this->_table} {$this->_where}";

        return array($query, $filename, $fields, $line);
    }

    public function backupPlaylistvideos() {
        $filename = "miwovideos_playlist_videos.sql";
        $fields = array('id', 'playlist_id', 'video_id', 'ordering');
        $line = "INSERT IGNORE INTO {$this->_dbprefix}miwovideos_playlist_categories (".implode(', ', $fields).")";
        $query = "SELECT `id`, `playlist_id`, `video_id`, `ordering` FROM {$this->_dbprefix}miwovideos_playlist_videos {$this->_where}";

        return array($query, $filename, $fields, $line);
    }

    public function backupVideos() {
        $filename = "miwovideos_videos.sql";
        $fields = array('id', 'user_id', 'channel_id', 'product_id', 'title', 'alias', 'introtext', 'fulltext', 'source', 'duration', 'likes', 'dislikes', 'hits', 'access', 'price',
            'created', 'modified', 'featured', 'published', 'fields', 'thumb', 'meta_desc', 'meta_key', 'meta_author', 'params', 'language');
        $line = "INSERT IGNORE INTO {$this->_dbprefix}miwovideos_{$this->_table} (".implode(', ', $fields).")\n";
        $query = "SELECT `id`, `user_id`, `channel_id`, `product_id`, `title`, `alias`, `introtext`, `fulltext`, `source`, `duration`, `likes`, `dislikes`, `hits`, `access`, `price`,".
            " `created`, `modified`, `featured`, `published`, `fields`, `thumb`, `meta_desc`, `meta_key`, `meta_author`, `params`, `language` FROM {$this->_dbprefix}miwovideos_{$this->_table} {$this->_where}";

        return array($query, $filename, $fields, $line);
    }

    public function backupVideocategories() {
        $filename = "miwovideos_video_categories.sql";
        $fields = array('id', 'video_id', 'category_id');
        $line = "INSERT IGNORE INTO {$this->_dbprefix}miwovideos_video_categories (".implode(', ', $fields).")";
        $query = "SELECT `id`, `video_id`, `category_id` FROM {$this->_dbprefix}miwovideos_video_categories {$this->_where}";

        return array($query, $filename, $fields, $line);
    }

    public function backupSubscriptions() {
        $filename = "miwovideos_channel_subscriptions.sql";
        $fields = array('id', 'item_id', 'item_type', 'user_id', 'channel_id', 'created');
        $line = "INSERT IGNORE INTO {$this->_dbprefix}miwovideos_{$this->_table} (".implode(', ', $fields).")";
        $query = "SELECT `id`, `item_id`, `item_type`, `user_id`, `channel_id`, `created` FROM {$this->_dbprefix}miwovideos_{$this->_table} {$this->_where}";

        return array($query, $filename, $fields, $line);
    }

    public function backupLikes() {
        $filename = "miwovideos_likes.sql";
        $fields = array('id', 'channel_id', 'user_id', 'item_id', 'item_type', 'type', 'created');
        $line = "INSERT IGNORE INTO {$this->_dbprefix}miwovideos_{$this->_table} (".implode(', ', $fields).")";
        $query = "SELECT `id`, `channel_id`, `user_id`, `item_id`, `item_type`, `type`, `created` FROM {$this->_dbprefix}miwovideos_{$this->_table} {$this->_where}";

        return array($query, $filename, $fields, $line);
    }

    public function backupReports() {
        $filename = "miwovideos_reports.sql";
        $fields = array('id', 'channel_id', 'user_id', 'item_id', 'item_type', 'reason_id', 'note', 'created', 'modified');
        $line = "INSERT IGNORE INTO {$this->_dbprefix}miwovideos_{$this->_table} (".implode(', ', $fields).")";
        $query = "SELECT `id`, `channel_id`, `user_id`, `item_id`, `item_type`, `reason_id`, `note`, `created`, `modified` FROM {$this->_dbprefix}miwovideos_{$this->_table} {$this->_where}";

        return array($query, $filename, $fields, $line);
    }

    public function backupFiles() {
        $filename = "miwovideos_files.sql";
        $fields = array('id', 'video_id', 'process_type', 'ext', 'file_size', 'source', 'channel_id',  'user_id', 'created', 'modified', 'published');
        $line = "INSERT IGNORE INTO {$this->_dbprefix}miwovideos_{$this->_table} (".implode(', ', $fields).")";
        $query = "SELECT `id`, `video_id`, `process_type`, `ext`, `file_size`, `source`, `channel_id`,  `user_id`, `created`, `modified`, `published` FROM {$this->_dbprefix}miwovideos_{$this->_table} {$this->_where}";

        return array($query, $filename, $fields, $line);
    }

    public function backupReasons() {
        $filename = "miwovideos_report_reasons.sql";
        $fields = array('id', 'parent', 'title', 'alias', 'description', 'access', 'language', 'association', 'published', 'created', 'modified');
        $line = "INSERT IGNORE INTO {$this->_dbprefix}miwovideos_{$this->_table} (".implode(', ', $fields).")";
        $query = "SELECT `id`, `parent`, `title`, `alias`, `description`, `access`, `language`, `association`, `published`, `created`, `modified` FROM {$this->_dbprefix}miwovideos_{$this->_table} {$this->_where}";

        return array($query, $filename, $fields, $line);
    }

    # MiwoVideos : Restore
    public function restoreCategories($line) {
        $preg = '/^INSERT IGNORE INTO `?(\w)+miwovideos_categories`?/';

        return array($preg, $line);
    }

    public function restoreChannels($line) {
        $preg = '/^INSERT IGNORE INTO `?(\w)+miwovideos_channels`?/';

        return array($preg, $line);
    }

    public function restorePlayists($line) {
        $preg = '/^INSERT IGNORE INTO `?(\w)+miwovideos_playlists`?/';

        return array($preg, $line);
    }

    public function restorePlayistvideos($line) {
        $preg = '/^INSERT IGNORE INTO `?(\w)+miwovideos_playlist_videos`?/';

        return array($preg, $line);
    }

    public function restoreVideos($line) {
        $preg = '/^INSERT IGNORE INTO `?(\w)+miwovideos_videos`?/';

        return array($preg, $line);
    }

    public function restoreVideocategories($line) {
        $preg = '/^INSERT IGNORE INTO `?(\w)+miwovideos_video_categories`?/';

        return array($preg, $line);
    }

    public function restoreSubscriptions($line) {
        $preg = '/^INSERT IGNORE INTO `?(\w)+miwovideos_channel_subscriptions`?/';

        return array($preg, $line);
    }

    public function restoreLikes($line) {
        $preg = '/^INSERT IGNORE INTO `?(\w)+miwovideos_likes`?/';

        return array($preg, $line);
    }

    public function restoreReports($line) {
        $preg = '/^INSERT IGNORE INTO `?(\w)+miwovideos_reports`?/';

        return array($preg, $line);
    }

    public function restoreFiles($line) {
        $preg = '/^INSERT IGNORE INTO `?(\w)+miwovideos_files`?/';

        return array($preg, $line);
    }

    public function restoreReasons($line) {
        $preg = '/^INSERT IGNORE INTO `?(\w)+miwovideos_report_reasons`?/';

        return array($preg, $line);
    }

    # Migration
    protected function _getUserChannelId($id){
        $db = MFactory::getDBO();
        static $cache = array();

        if (!isset($cache[$id])) {
            $db->setQuery("SELECT id, user_id FROM #__miwovideos_channels WHERE user_id = '{$id}' AND `default` = 1");
            $cache[$id] = $db->loadObject();
        }

        return $cache[$id];
    }

    protected function _getUserChannelName($name){
        $db = MFactory::getDBO();
        static $cache = array();
        $name = strtolower($name);

        if (!isset($cache[$name])) {
            $db->setQuery("SELECT c.id, c.user_id FROM #__miwovideos_channels AS c LEFT JOIN #__users AS u ON u.ID = c.user_id WHERE u.user_login = '{$name}'");
            $cache[$name] = $db->loadObject();
        }

        return $cache[$name];
    }

    # Wordpress Video Gallery Migration
    public function migrateWordpressVideoGalleryCats(){
        $db = MFactory::getDBO();

        $cat = "SELECT * FROM #__hdflvvideoshare_playlist ORDER BY `pid`";
        $db->setQuery($cat);
        $cats = $db->loadAssocList();

        if (empty($cats)) {
            return false;
        }

        foreach($cats as $cat) {
			$cat_name = ($this->_jstatus) ? $db->escape($cat['playlist_name']) : $db->getEscaped($cat['playlist_name']);

            $q = "INSERT IGNORE INTO `#__miwovideos_categories` (`id`, `parent`, `title`, `alias`, `introtext`, `published`, `access`, `ordering`, `language`) ".
                "VALUES ('".$cat['pid']."', '0', '".$cat_name."', '".$cat['playlist_slugname']."', '".$cat['playlist_desc']."', '".$cat['is_publish']."', '1', '".$cat['playlist_order']."', '*')";
            $db->setQuery($q);
            $db->query();
        }

        return true;
    }

    public function migrateWordpressVideoGalleryVideos(){
        $db = MFactory::getDBO();

        $vid = "SELECT * FROM #__hdflvvideoshare ORDER BY vid";
        $db->setQuery($vid);
        $vids = $db->loadAssocList();

        if (empty($vids)) {
            return false;
        }

        $alias = MApplication::stringURLSafe(htmlspecialchars_decode($vid['name'], ENT_QUOTES));

        foreach($vids as $vid) {
            $image_name = $vid_url = '';

            if(!empty($vid['file_type'])){
                switch($vid['file_type']){
                    case '2':
                        if(!empty($vid['image'])){
                            $image_name = $vid['image'];
                            $this->_copyImagesFiles(MPATH_MEDIA.'/videogallery/', 'videos/', $vid['vid'], $vid, 'WordpressVideoGallery', $image_name);
                        }
                        if(!empty($vid['file'])){
                            $vid_url = $vid['file'];
                            $this->_copyVideosFiles(MPATH_MEDIA.'/videogallery/', $vid['vid'], $vid_url, $vid, 'WordpressVideoGallery');
                        }
                        break;
                    case '1':
                    case '3':
                    case '4':
                        if(!empty($vid['image'])){
                            $image_name = $vid['image'];
                        }
                        if(!empty($vid['file'])){
                            $vid_url = $vid['file'];
                        }
                        break;
                    default:
                        if(!empty($vid['image'])){
                            $image_name = $vid['image'];
                        }
                        if(!empty($vid['file'])){
                            $vid_url = $vid['file'];
                        }
                        break;
                }
            }

            $chnl_id = self::_getUserChannelId($vid['member_id']);

            $vid_name = ($this->_jstatus) ? $db->escape($vid['name']) : $db->getEscaped($vid['name']);
            $vid_desc = ($this->_jstatus) ? $db->escape($vid['description']) : $db->getEscaped($vid['description']);

            $q = "INSERT IGNORE INTO `#__miwovideos_videos` (`id`, `user_id`, `channel_id`, `product_id`, `title`, `alias`, `introtext`, `source`, `published`, `created`, `modified`, ".
                "`thumb`, `duration`, `hits`, `featured`, `access`, `language`) ".
                "VALUES ('".$vid['vid']."', '".$vid['member_id']."', '".$chnl_id->id."', '0', '".$vid_name."', '".$alias."', '".$vid_desc."', '".$vid_url."', '".$vid['publish']."', '".$vid['post_date']."', '".$vid['post_date']
                ."', '".$image_name."', '".$vid['duration']."', '".$vid['hitcount']."', '".$vid['featured']."', '1', '*')";
            $db->setQuery($q);
            $db->query();
        }

        $cat = "SELECT `media_id`, `playlist_id` FROM #__hdflvvideoshare_med2play ORDER BY `rel_id`";
        $db->setQuery($cat);
        $cats = $db->loadAssocList();

        if (empty($cats)) {
            return false;
        }

        foreach($cats as $cat) {
            $q = "INSERT IGNORE INTO `#__miwovideos_video_categories` (`video_id`, `category_id`) VALUES ('".$cat['media_id']."', '".$cat['playlist_id']."')";
            $db->setQuery($q);
            $db->query();
        }

        return true;
    }

    # Cool Video Gallery Migration
    public function migrateCoolVideoGalleryCats(){
        $db = MFactory::getDBO();

        $cat = "SELECT * FROM #__cvg_gallery ORDER BY gid";
        $db->setQuery($cat);
        $cats = $db->loadAssocList();

        if (empty($cats)) {
            return false;
        }

        foreach($cats as $cat) {
            $alias = MApplication::stringURLSafe(htmlspecialchars_decode($cat['name'], ENT_QUOTES));
            $cat_name = ($this->_jstatus) ? $db->escape($cat['title']) : $db->getEscaped($cat['title']);

            $q = "INSERT IGNORE INTO `#__miwovideos_categories` (`id`, `parent`, `title`, `alias`, `published`, `ordering`, `access`, `introtext`, `language`) ".
                "VALUES ('".$cat['gid']."', '0', '".$cat_name."', '".$cat['name']."', '1', '1', '1', '".$cat['galdesc']."', '*')";
            $db->setQuery($q);
            $db->query();
        }

        return true;
    }

    public function migrateCoolVideoGalleryVideos(){
        $db = MFactory::getDBO();

        $vid = "SELECT * FROM #__cvg_videos ORDER BY pid";
        $db->setQuery($vid);
        $vids = $db->loadAssocList();

        if (empty($vids)) {
            return false;
        }

        foreach($vids as $vid) {
            $pos = strpos($vid['filename'], 'mp3');
            if ($pos === true) {
                continue;
            }

            $image_name = $vid_url = '';

            $alias = MApplication::stringURLSafe(htmlspecialchars_decode($vid['video_title'], ENT_QUOTES));

            $auth_id = self::_getCoolVidGalAuthorID($vid['galleryid']);
            $chnl_id = self::_getUserChannelId($auth_id->author);

            if(!empty($vid['video_type'])){
                switch($vid['video_type']){
                    case 'upload':
                        if(!empty($vid['thumb_filename'])){
                            $image_name = $vid['thumb_filename'];
                            if(!empty($image_name)){
                                $this->_copyImagesFiles(MPATH_MEDIA.'/video-gallery/'.$auth_id->name.'/thumbs/', 'videos/', $vid['pid'], $vid, 'CoolVideoGallery', $image_name);
                            }
                        }
                        if(!empty($vid['filename'])){
                            $vid_url = $vid['filename'];
                            $this->_copyVideosFiles(MPATH_MEDIA.'/video-gallery/'.$auth_id->name.'/', $vid['pid'], $vid_url, $vid, 'CoolVideoGallery');
                        }
                        break;
                    case 'Direct URL':
                    case 'Youtube Videos':
                    case 'Dailymotion Videos':
                    case 'Vimeo Videos':
                        if(!empty($vid['thumb_filename'])){
                            $image_name = $vid['thumb_filename'];
                        }
                        if(!empty($vid['filename'])){
                            $vid_url = $vid['filename'];
                        }
                        break;
                    default:
                        if(!empty($vid['thumb_filename'])){
                            $image_name = $vid['thumb_filename'];
                        }
                        if(!empty($vid['filename'])){
                            $vid_url = $vid['filename'];
                        }
                        break;
                }
            }

            $vid_name = ($this->_jstatus) ? $db->escape($vid['video_title']) : $db->getEscaped($vid['video_title']);
            $vid_desc = ($this->_jstatus) ? $db->escape($vid['description']) : $db->getEscaped($vid['description']);

            $q = "INSERT IGNORE INTO `#__miwovideos_videos` (`id`, `user_id`, `channel_id`, `product_id`, `title`, `alias`, `introtext`, `source`, `published`, ".
                "`thumb`, `created`, `modified`, `access`, `language`) ".
                "VALUES ('".$vid['pid']."', '".$chnl_id->user_id."', '".$chnl_id->id."', '0', '".$vid_name."', '".$alias."', '".$vid_desc."', '".$vid_url."', '1', '".
                $image_name."', '".$vid['videodate']."', '".$vid['videodate']."', '1', '*')";
            $db->setQuery($q);
            $db->query();

            $q = "INSERT IGNORE INTO `#__miwovideos_video_categories` (`video_id`, `category_id`) VALUES ('".$vid['pid']."', '".$vid['galleryid']."')";
            $db->setQuery($q);
            $db->query();
        }

        return true;
    }

    protected function _getCoolVidGalAuthorID($id){
        $db = MFactory::getDBO();
        static $cache = array();

        if (!isset($cache[$id])) {
            $db->setQuery("SELECT author, name FROM #__cvg_gallery WHERE gid = '{$id}'");
            $cache[$id] = $db->loadObject();
        }

        return $cache[$id];
    }

    # WPG Cool Gallery Migration
    public function migrateWPGCoolGalleryCats(){
        $db = MFactory::getDBO();

        $cat = "SELECT t.term_id AS id, t.name, t.slug, tt.parent, tt.description FROM #__terms AS t INNER JOIN #__term_taxonomy AS tt ON t.term_id = tt.term_id WHERE tt.taxonomy = 'niccoolcategory' ORDER BY t.term_id";
        $db->setQuery($cat);
        $cats = $db->loadAssocList();

        if (empty($cats)) {
            return false;
        }

        foreach($cats as $cat) {
            $cat_name = ($this->_jstatus) ? $db->escape($cat['name']) : $db->getEscaped($cat['name']);

            $q = "INSERT IGNORE INTO `#__miwovideos_categories` (`id`, `parent`, `title`, `alias`, `published`, `ordering`, `access`, `introtext`, `language`) ".
                "VALUES ('".$cat['id']."', '".$cat['parent']."', '".$cat_name."', '".$cat['slug']."', '1', '1', '1', '".$cat['description']."', '*')";
            $db->setQuery($q);
            $db->query();
        }

        return true;
    }

    public function migrateWPGCoolGalleryVideos(){
        $db = MFactory::getDBO();

        $vid = "SELECT m.meta_value AS video, p.ID, p.post_author, p.post_date, p.post_modified, p.post_title, p.post_status FROM #__posts AS p LEFT JOIN #__postmeta AS m ON p.id = m.post_id
             WHERE p.post_type='niccoolgallery' AND m.meta_key='wpg_meta_cool_video_link' AND p.post_status != 'auto-draft' AND p.post_status != 'inherit' ORDER BY ID";
        $db->setQuery($vid);
        $vids = $db->loadAssocList();

        if (empty($vids)) {
            return false;
        }

        foreach($vids as $vid) {
            $post_status = '1';
            $image_name = NULL;

            if(!empty($vid['post_status'])){
                switch($vid['post_status']){
                    case 'private':
                        $post_status = '3';
                        break;
                    case 'publish':
                        $post_status = '1';
                        break;
                }
            }

            $image_ids = "SELECT `meta_value` FROM #__postmeta WHERE meta_key = '_thumbnail_id' AND post_id = '".$vid['ID']."'";
            $db->setQuery($image_ids);
            $image_id = $db->loadResult();

            if(!empty($image_id)){
                $image_names = "SELECT `meta_value` FROM #__postmeta WHERE meta_key = '_wp_attached_file' AND post_id = '".$image_id."'";
                $db->setQuery($image_names);
                $image_name = $db->loadResult();
            }

            if(!empty($image_name)){
	            $image_name = MURL_MEDIA.'/'.$image_name;
            }

            $chnl_id = self::_getUserChannelId($vid['post_author']);
            //$cat_id = self::_getVideoURL($vid['category']);

            //$this->_copyVideosFiles(ABSPATH.'/media/com_hdwplayer/', $vid['ID'], $vid['video'], $vid, 'WPGCoolGallery');
            //$this->_copyVideosFiles(ABSPATH.'/media/com_hdwplayer/', $vid['ID'], $vid['hdvideo'], $vid);

            $alias = MApplication::stringURLSafe(htmlspecialchars_decode($vid['post_title'], ENT_QUOTES));
            $vid_name = ($this->_jstatus) ? $db->escape($vid['post_title']) : $db->getEscaped($vid['post_title']);

            $q = "INSERT IGNORE INTO `#__miwovideos_videos` (`id`, `user_id`, `channel_id`, `product_id`, `title`, `alias`, `created`, `modified`, `source`, `published`, ".
                "`thumb`, `access`, `language`) ".
                "VALUES ('".$vid['ID']."', '".$chnl_id->user_id."', '".$chnl_id->id."', '0', '".$vid_name."', '".$alias."', '".$vid['post_date']."', '".$vid['post_modified']."', '".$vid['video']."', '".$post_status
                ."', '".$image_name."', '1', '*')";
            $db->setQuery($q);
            $db->query();

            $cat = "SELECT `term_taxonomy_id` FROM #__term_relationships WHERE object_id='".$vid['ID']."' ORDER BY `object_id`";
            $db->setQuery($cat);
            $cats = $db->loadAssocList();

            foreach($cats as $cat) {
                $q = "INSERT IGNORE INTO `#__miwovideos_video_categories` (`video_id`, `category_id`) VALUES ('".$vid['ID']."', '".$cat['term_taxonomy_id']."')";
                $db->setQuery($q);
                $db->query();
            }
        }

        return true;
    }

    # All Video Gallery Migration
    public function migrateAllVideoGalleryCats(){
        $db = MFactory::getDBO();

        $cat = "SELECT * FROM #__allvideogallery_categories ORDER BY id";
        $db->setQuery($cat);
        $cats = $db->loadAssocList();

        if (empty($cats)) {
            return false;
        }

        foreach($cats as $cat) {
            $image_name = '';
            $cat_image = ($cat['thumb']) ? $cat['thumb'] : '';
            $images = explode('/', $cat_image);
            $image_name = array_pop($images);
            $cat_slug = array_pop($images);

            if(!empty($image_name)){
	            //@TODO This path should be wrong
                $this->_copyImagesFiles(ABSPATH.'media/com_allvideoshare/'.$cat_slug.'/', 'categories/', $cat, 'AllVideoGallery');
            }

            $cat_name = ($this->_jstatus) ? $db->escape($cat['name']) : $db->getEscaped($cat['name']);

            $q = "INSERT IGNORE INTO `#__miwovideos_categories` (`id`, `parent`, `title`, `alias`, `published`, `access`, `thumb`, `language`) ".
                "VALUES ('".$cat['id']."', '0', '".$cat_name."', '".$cat['slug']."', '".$cat['published']."', '1', '".$image_name."', '*')";
            $db->setQuery($q);
            $db->query();
        }

        return true;
    }

    public function migrateAllVideoGalleryVideos(){
        $db = MFactory::getDBO();

        $vid = "SELECT * FROM #__allvideogallery_videos ORDER BY id";
        $db->setQuery($vid);
        $vids = $db->loadAssocList();

        if (empty($vids)) {
            return false;
        }

        foreach($vids as $vid) {
            $image_name = '';
            $vid_url = $vid['video'];

            $chnl_id = self::_getUserChannelId('1');
            $cat_id = self::_getAllVideoGalleryCatID($vid['category']);

            if(!empty($vid['type'])){
                switch($vid['type']){
                    /*case 'upload':
                        if(!empty($vid['thumb'])){
                            $image_name = $vid['thumb'];

                            $images = explode('/', $image_name);
                            $image_name = array_pop($images);
                            $cat_slug = array_pop($images);

                            if(!empty($image_name)){
                                $this->_copyImagesFiles(ABSPATH.'/media/com_allvideoshare/'.$cat_slug.'/', 'videos/', $vid, 'AllVideoGallery');
                            }
                        }
                        if(!empty($vid['video'])){
                            //$vid_url = '/media/com_miwovideos/videos/'.$vid['id'].'/orig/'.$vid['video'];
                            $vid_url = $vid['video'];
                        }
                        break;*/
                    case 'url':
                    case 'rtmp':
                    case 'lighttpd':
                    case 'highwinds':
                    case 'bitgravity':
                    case 'youtube':
                        if(!empty($vid['preview'])){
                            $image_name = $vid['preview'];
                        }
                        if(!empty($vid['video'])){
                            $vid_url = $vid['video'];
                        }
                        break;
                    default:
                        if(!empty($vid['preview'])){
                            $image_name = $vid['preview'];
                        }
                        if(!empty($vid['video'])){
                            $vid_url = $vid['video'];
                        }
                        break;
                }
            }

            //$cat_alias = MApplication::stringURLSafe(htmlspecialchars_decode($vid['title'], ENT_QUOTES));

            //$this->_copyVideosFiles(ABSPATH.'/media/com_allvideoshare/'.$cat_id->slug.'/', $vid['id'], $vid['video'], $vid, 'AllVideoGallery', $cat_id->slug);
            //$this->_copyVideosFiles(ABSPATH.'/media/com_allvideoshare/'.$cat_id->slug.'/', $vid['id'], $vid['hdvideo'], $vid);

            $vid_name = ($this->_jstatus) ? $db->escape($vid['title']) : $db->getEscaped($vid['title']);
            $vid_desc = ($this->_jstatus) ? $db->escape($vid['description']) : $db->getEscaped($vid['description']);

            $q = "INSERT IGNORE INTO `#__miwovideos_videos` (`id`, `user_id`, `channel_id`, `product_id`, `title`, `alias`, `introtext`, `source`, `published`, ".
                "`thumb`, `hits`, `featured`, `access`, `language`) ".
                "VALUES ('".$vid['id']."', '".$chnl_id->user_id."', '".$chnl_id->id."', '0', '".$vid_name."', '".$vid['slug']."', '".$vid_desc."', '".$vid_url."', '".$vid['published']
                ."', '".$image_name."', '".$vid['hits']."', '".$vid['featured']."', '1', '*')";
            $db->setQuery($q);
            $db->query();

            $q = "INSERT IGNORE INTO `#__miwovideos_video_categories` (`video_id`, `category_id`) VALUES ('".$vid['id']."', '".$cat_id->id."')";
            $db->setQuery($q);
            $db->query();
        }

        return true;
    }

    protected function _getAllVideoGalleryCatID($name){
        $db = MFactory::getDBO();
        static $cache = array();

        if (!isset($cache[$name])) {
            $db->setQuery("SELECT id, slug FROM #__allvideogallery_categories WHERE name = '{$name}'");
            $cache[$name] = $db->loadObject();
        }

        return $cache[$name];
    }

    # HDW Player Migration
    public function migrateHDWPlayerCats(){
        $db = MFactory::getDBO();

        $cat = "SELECT `id`, `name` FROM #__hdwplayer_playlist ORDER BY `id`";
        $db->setQuery($cat);
        $cats = $db->loadAssocList();

        if (empty($cats)) {
            return false;
        }

        foreach($cats as $cat) {
            $alias = MApplication::stringURLSafe(htmlspecialchars_decode($cat['name'], ENT_QUOTES));
            $cat_name = ($this->_jstatus) ? $db->escape($cat['name']) : $db->getEscaped($cat['name']);

            $q = "INSERT IGNORE INTO `#__miwovideos_categories` (`id`, `parent`, `title`, `alias`, `published`, `ordering`, `access`, `language`) ".
                "VALUES ('".$cat['id']."', '0', '".$cat_name."', '".$alias."', '1', '1', '1', '*')";
            $db->setQuery($q);
            $db->query();
        }

        return true;
    }

    public function migrateHDWPlayerVideos(){
        $db = MFactory::getDBO();

        $vid = "SELECT * FROM #__hdwplayer_videos ORDER BY id";
        $db->setQuery($vid);
        $vids = $db->loadAssocList();

        if (empty($vids)) {
            return false;
        }

        foreach($vids as $vid) {
            $chnl_id = self::_getUserChannelId('1');

            $alias = MApplication::stringURLSafe(htmlspecialchars_decode($vid['title'], ENT_QUOTES));
            $vid_name = ($this->_jstatus) ? $db->escape($vid['title']) : $db->getEscaped($vid['title']);
            $vid_desc = ($this->_jstatus) ? $db->escape($vid['description']) : $db->getEscaped($vid['description']);

            $q = "INSERT IGNORE INTO `#__miwovideos_videos` (`id`, `user_id`, `channel_id`, `product_id`, `title`, `alias`, `introtext`, `source`, `published`, ".
                "`thumb`, `hits`, `access`, `language`) ".
                "VALUES ('".$vid['id']."', '".$chnl_id->user_id."', '".$chnl_id->id."', '0', '".$vid_name."', '".$alias."', '".$vid_desc."', '".$vid['video']."', '1', '".$vid['preview']."', '".$vid['hits']."', '1', '*')";
            $db->setQuery($q);
            $db->query();

            $q = "INSERT IGNORE INTO `#__miwovideos_video_categories` (`video_id`, `category_id`) VALUES ('".$vid['id']."', '".$vid['playlistid']."')";
            $db->setQuery($q);
            $db->query();
        }

        return true;
    }

    # Yendif Player Migration
    public function migrateYendifPlayerCats(){
        $db = MFactory::getDBO();

        $cat = "SELECT `id`, `name`, `published` FROM #__yendif_player_playlists ORDER BY `id`";
        $db->setQuery($cat);
        $cats = $db->loadAssocList();

        if (empty($cats)) {
            return false;
        }

        foreach($cats as $cat) {
            $alias = MApplication::stringURLSafe(htmlspecialchars_decode($cat['name'], ENT_QUOTES));
            $cat_name = ($this->_jstatus) ? $db->escape($cat['name']) : $db->getEscaped($cat['name']);

            $q = "INSERT IGNORE INTO `#__miwovideos_categories` (`id`, `parent`, `title`, `alias`, `published`, `ordering`, `access`, `language`) ".
                "VALUES ('".$cat['id']."', '0', '".$cat_name."', '".$alias."', '".$cat['published']."', '1', '1', '*')";
            $db->setQuery($q);
            $db->query();
        }

        return true;
    }

    public function migrateYendifPlayerVideos(){
        $db = MFactory::getDBO();

        $vid = "SELECT * FROM #__yendif_player_media ORDER BY id";
        $db->setQuery($vid);
        $vids = $db->loadAssocList();

        if (empty($vids)) {
            return false;
        }

        foreach($vids as $vid) {
            $image_name = $vid_url = '';

            $chnl_id = self::_getUserChannelId('1');

            if($vid['type'] == 'mp3') continue;

            if(!empty($vid['type'])){
                switch($vid['type']){
                    case 'video':
                        if(!empty($vid['poster'])){
                            $img_name = $vid['poster'];

                            $images = explode('/', $img_name);
                            $image_name = array_pop($images);
                            $last2 = array_pop($images);
                            $last3 = array_pop($images);
                            $this->_copyImagesFiles(MPATH_MEDIA.'/'.$last3.'/'.$last2.'/', 'videos/', $vid['id'], $vid, 'YendifPlayer', $image_name);
                        }
                        if(!empty($vid['mp4'])){
                            $vid_name = $vid['mp4'];

                            $videos = explode('/', $vid_name);
                            $vid_url = array_pop($videos);
                            $last2 = array_pop($videos);
                            $last3 = array_pop($videos);
                            $this->_copyVideosFiles(MPATH_MEDIA.'/'.$last3.'/'.$last2.'/', $vid['id'], $vid_url, $vid, 'YendifPlayer');
                        }
                        if(!empty($vid['webm'])){
                            $vid_name = $vid['webm'];

                            $videos = explode('/', $vid_name);
                            $webm_url = array_pop($videos);
                            $last2 = array_pop($videos);
                            $last3 = array_pop($videos);
                            $this->_copyVideosFiles(MPATH_MEDIA.'/'.$last3.'/'.$last2.'/', $vid['id'], $webm_url, $vid, 'YendifPlayer');
                        }
                        if(!empty($vid['ogg'])){
                            $vid_name = $vid['ogg'];

                            $videos = explode('/', $vid_name);
                            $ogg_url = array_pop($videos);
                            $last2 = array_pop($videos);
                            $last3 = array_pop($videos);
                            $this->_copyVideosFiles(MPATH_MEDIA.'/'.$last3.'/'.$last2.'/', $vid['id'], $ogg_url, $vid, 'YendifPlayer');
                        }
                        break;
                    case 'rtmp':
                    case 'youtube':
                        if(!empty($vid['poster'])){
                            $image_name = $vid['poster'];
                        }
                        if(!empty($vid['youtube'])){
                            $vid_url = $vid['youtube'];
                        }
                        break;
                }
            }

            $alias = MApplication::stringURLSafe(htmlspecialchars_decode($vid['title'], ENT_QUOTES));
            $vid_name = ($this->_jstatus) ? $db->escape($vid['title']) : $db->getEscaped($vid['title']);
            $vid_desc = ($this->_jstatus) ? $db->escape($vid['description']) : $db->getEscaped($vid['description']);

            $q = "INSERT IGNORE INTO `#__miwovideos_videos` (`id`, `user_id`, `channel_id`, `product_id`, `title`, `alias`, `introtext`, `source`, `created`, `duration`, `published`, ".
                "`thumb`, `hits`, `access`, `language`) ".
                "VALUES ('".$vid['id']."', '".$chnl_id->user_id."', '".$chnl_id->id."', '0', '".$vid_name."', '".$alias."', '".$vid_desc."', '".$vid_url."', '".$vid['createddate']."', '".$vid['duration']."', '1', '".
                $image_name."', '".$vid['hits']."', '1', '*')";
            $db->setQuery($q);
            $db->query();

            $vid_playls = explode(' ',$vid['playlists']);

            if(!empty($vid_playls)){
                foreach($vid_playls AS $vid_pls){
                    $q = "INSERT IGNORE INTO `#__miwovideos_video_categories` (`video_id`, `category_id`) VALUES ('".$vid['id']."', '".$vid_pls."')";
                    $db->setQuery($q);
                    $db->query();
                }
            }
            else {
                $q = "INSERT IGNORE INTO `#__miwovideos_video_categories` (`video_id`, `category_id`) VALUES ('".$vid['id']."', '".$vid['playlists']."')";
                $db->setQuery($q);
                $db->query();
            }

        }

        return true;
    }

    public function _copyImagesFiles($dir, $source = '', $id, $vid, $component = NULL, $thumb_name = NULL) {
        foreach (glob($dir . "*") as $filename) {
            if (MFolder::exists($filename)) {
                continue;
            }
			
            $media_path = MPATH_MEDIA.'/miwovideos/images/';

            if (MFile::exists($media_path. $source . $id .'/orig/'.$thumb_name)) {
                continue;
            }

            if (!MFolder::exists($media_path. $source . $id .'/orig/')) {
                MFolder::create($media_path. $source . $id .'/orig/', 0777);
            }

            if($component == 'CoolVideoGallery' || $component == 'WordpressVideoGallery' || $component == 'YendifPlayer'){
                if($thumb_name == basename($filename)){
                    if (!MFile::copy($filename, $media_path. $source . $id . '/orig/' . basename($filename))){
                        echo 'Failed to copy <i>' . $filename . '</i> to image directory.<br />';
                    }
                }
            }
            else {
                if (!MFile::copy($filename, $media_path. $source . $id . '/orig/' . basename($filename))){
                    echo 'Failed to copy <i>' . $filename . '</i> to image directory.<br />';
                }
            }
        }
    }

    public function _copyVideosFiles($dir, $id, $video_name, $vid, $component = NULL) {
        foreach (glob($dir . "*") as $filename) {
            if (MFolder::exists($filename)) {
                continue;
            }
			
            $media_path = MPATH_MEDIA.'/miwovideos/videos/';

            if (MFile::exists($media_path. $id .'/orig/'.$video_name)) {
                continue;
            }

            if (!MFolder::exists($media_path. $id .'/orig/')) {
                MFolder::create($media_path . $id .'/orig/', 0777);
            }

            if($component == 'CoolVideoGallery' || $component == 'WordpressVideoGallery' || $component == 'YendifPlayer'){
                if($video_name == basename($filename)){
                    if (!MFile::copy($filename, $media_path . $id . '/orig/' . basename($filename))){
                        echo 'Failed to copy <i>' . $filename . '</i> to image directory.<br />';
                    }
                }
            }
            else {
                if (!MFile::copy($filename, $media_path . $id . '/orig/' . basename($filename))){
                    echo 'Failed to copy <i>' . $filename . '</i> to image directory.<br />';
                }
            }
        }
    }
}
