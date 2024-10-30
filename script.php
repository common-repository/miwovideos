<?php
/**
 * @package        MiwoVideos
 * @copyright      Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license        GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die('Restricted access');

// Import Libraries
mimport('framework.application.helper');
mimport('framework.filesystem.file');
mimport('framework.filesystem.folder');
mimport('framework.installer.installer');

class com_MiwovideosInstallerScript {

	private $_current_version     = null;
	private $_is_new_installation = true;

	public function preflight($type, $parent) {
		$db = MFactory::getDBO();
		$db->setQuery('SELECT option_value FROM #__options WHERE option_name = "miwovideos"');
		$config = $db->loadResult();

		if (!empty($config)) {
			$this->_is_new_installation = false;

			$miwovideos_xml = MPath::clean(MPATH_WP_PLG.'/miwovideos/miwovideos.xml');

			if (MFile::exists($miwovideos_xml)) {
				$xml                    = simplexml_load_file($miwovideos_xml, 'SimpleXMLElement');
				$this->_current_version = (string)$xml->version;
			}
		}
	}

	public function postflight($type, $parent) {
		require_once(MPATH_WP_PLG.'/miwovideos/admin/library/miwovideos.php');

		if (!MFolder::exists(ABSPATH.'cgi-bin')) {
			MFolder::create(ABSPATH.'cgi-bin');
		}

		MFile::move(MPath::clean(MPATH_WP_PLG.'/miwovideos/admin/ubr_upload.pl'), MPath::clean(ABSPATH.'cgi-bin/ubr_upload.pl'));

		if (!MFolder::exists(MPATH_MIWI.'/cli')) {
			MFolder::create(MPATH_MIWI.'/cli');
		}

		MFile::move(MPath::clean(MPATH_WP_PLG.'/miwovideos/miwovideoscli.php'), MPath::clean(MPATH_MIWI.'/cli/miwovideoscli.php'));

		if (MFolder::exists(MPath::clean(MPATH_WP_PLG.'/miwovideos/languages'))) {
			MFolder::copy(MPath::clean(MPATH_WP_PLG.'/miwovideos/languages'), MPath::clean(MPATH_MIWI.'/languages'), null, true);
			MFolder::delete(MPath::clean(MPATH_WP_PLG.'/miwovideos/languages'));
		}
		if (!MFolder::exists(MPath::clean(MPATH_MEDIA))) {
			MFolder::create(MPath::clean(MPATH_MEDIA));
		}
		if (!MFolder::exists(MPath::clean(MPATH_MEDIA.'/miwovideos'))) {
			MFolder::create(MPath::clean(MPATH_MEDIA.'/miwovideos'));
		}
		if (!MFolder::exists(MPath::clean(MPATH_MEDIA.'/miwovideos/videos'))) {
			MFolder::create(MPath::clean(MPATH_MEDIA.'/miwovideos/videos'));
		}
		if (MFolder::exists(MPath::clean(MPATH_WP_PLG.'/miwovideos/media'))) {
			MFolder::copy(MPath::clean(MPATH_WP_PLG.'/miwovideos/media'), MPath::clean(MPATH_MEDIA.'/miwovideos'), null, true);
			MFolder::delete(MPath::clean(MPATH_WP_PLG.'/miwovideos/media'));
		}
		if (MFolder::exists(MPath::clean(MPATH_WP_PLG.'/miwovideos/modules'))) {
			MFolder::copy(MPath::clean(MPATH_WP_PLG.'/miwovideos/modules'), MPath::clean(MPATH_MIWI.'/modules'), null, true);
			MFolder::delete(MPath::clean(MPATH_WP_PLG.'/miwovideos/modules'));
		}
		if (MFolder::exists(MPath::clean(MPATH_WP_PLG.'/miwovideos/plugins'))) {
			MFolder::copy(MPath::clean(MPATH_WP_PLG.'/miwovideos/plugins'), MPath::clean(MPATH_MIWI.'/plugins'), null, true);
			MFolder::delete(MPath::clean(MPATH_WP_PLG.'/miwovideos/plugins'));
		}

		########

		if ($this->_is_new_installation == true) {
			$this->_installMiwovideos();
		}
		else {
			$this->_updateMiwovideos();
		}
	}

	protected function _installMiwovideos() {
		$db = MFactory::getDbo();

		$config = new stdClass();
		# General
		$config->pid                        = '';
		$config->version_checker            = '1';
		$config->show_db_errors             = '0';
		$config->log                        = '0';
		$config->jusersync                  = '0';
		$config->categories                 = '1';
		$config->playlists                  = '1';
		$config->tags                       = '1';
		$config->subscriptions              = '1';
		$config->likes_dislikes             = '1';
		$config->custom_fields              = '1';
		$config->reports                    = '1';
		$config->comments                   = '0';
		$config->cdn                        = '0';
		$config->protect_files              = '0';
		$config->protected_link_expire_time = '1800';
		# Front-end
		$config->button_class       = MiwoVideos::is30() ? 'btn button-primary' : 'miwovideos_button';
		$config->override_color     = '#dc2f2c';
		$config->videos_per_page    = '6';
		$config->load_plugins       = '0';
		$config->show_empty_cat     = '1';
		$config->show_number_videos = '1';
		$config->order_videos       = 'v.ordering';
		$config->listing_style      = 'grid';
		$config->title_truncation   = '20';
		$config->desc_truncation    = '150';
		$config->thumb_size         = '3'; // Small Image
		$config->thumb_aspect       = '43';
		$config->items_per_column   = '3';
		# Player
		$config->video_player  = 'videojs';
		$config->fallback      = '0';
		$config->video_quality = '480';
		$config->autoplay      = '1';
		# Upload
		$config->normal_upload           = '1';
		$config->perl_upload             = '1';
		$config->remote_video_link       = '1';
		$config->remote_video_embed_code = '1';
		$config->upload_script           = 'fancy';
		$config->allow_file_types        = 'mov|mpeg|divx|flv|mpg|avi|mp4|mkv';
		$config->upload_max_filesize     = '128';
		# Processing
		$config->process_videos        = '1';
		$config->auto_process_videos   = '1';
		$config->frames                = '1';
		$config->watermark             = '1';
		$config->watermark_position    = '4';
		$config->watermark_path        = 'wp-admin/images/wordpress-logo.png';
		$config->metadata_injector     = 'yamdi';
		$config->max_video_bitrate240  = '300';
		$config->audio_bitrate240      = '0';
		$config->max_video_bitrate360  = '500';
		$config->audio_bitrate360      = '0';
		$config->max_video_bitrate480  = '700';
		$config->audio_bitrate480      = '0';
		$config->max_video_bitrate720  = '900';
		$config->audio_bitrate720      = '0';
		$config->max_video_bitrate1080 = '1200';
		$config->audio_bitrate1080     = '0';
		$config->jpeg_75               = '1';
		$config->jpeg_100              = '1';
		$config->jpeg_240              = '1';
		$config->jpeg_500              = '1';
		$config->jpeg_640              = '1';
		$config->jpeg_1024             = '1';
		$config->mp4_240p              = '1';
		$config->mp4_360p              = '1';
		$config->mp4_480p              = '1';
		$config->mp4_720p              = '1';
		$config->mp4_1080p             = '1';
		$config->webm_240p             = '1';
		$config->webm_360p             = '1';
		$config->webm_480p             = '1';
		$config->webm_720p             = '1';
		$config->webm_1080p            = '1';
		$config->ogg_240p              = '1';
		$config->ogg_360p              = '1';
		$config->ogg_480p              = '1';
		$config->ogg_720p              = '1';
		$config->ogg_1080p             = '1';
		$config->flv_240p              = '1';
		$config->flv_360p              = '1';
		$config->flv_480p              = '1';
		$config->flv_720p              = '1';
		$config->flv_1080p             = '1';
		# Server
		$config->php_path             = '/usr/bin/php';
		$config->ffmpeg_path          = '/usr/local/bin/ffmpeg';
		$config->qt_faststart_path    = '/usr/local/bin/qt-faststart';
		$config->flvtool2_path        = '/usr/bin/flvtool2';
		$config->yamdi_path           = '/usr/bin/yamdi';
		$config->uber_upload_perl_url = '';
		$config->uber_upload_tmp_path = '/tmp/ubr_temp/';
		$reg                          = new MRegistry($config);
		$config                       = $reg->toString();

		$user_id = MFactory::getUser()->id;

		$db->setQuery('INSERT INTO `#__options` (option_name, option_value) VALUES ("miwovideos", '.$db->Quote($config).')');
		$db->query();

		# SAMPLE DATA
		
		# Playlist
		$db->setQuery("INSERT IGNORE  INTO `#__miwovideos_playlists` (`id`, `user_id`, `channel_id`, `type`, `title`, `alias`, `introtext`, `fulltext` ,`thumb` ,`fields` ,`likes` ,`dislikes` ,`hits` ,`subscriptions` ,`params` ,`ordering` ,`access` ,`language` ,`created` ,`modified` ,`meta_desc` ,`meta_key` ,`meta_author` ,`share_others` ,`featured` ,`published`) VALUES
			(1, ".$user_id.", 0, 0, 'My Playlist', 'my-playlist', 'My Playlist', NULL , 'http://i1.ytimg.com/vi/KAi2QIq1SUs/mqdefault.jpg', NULL , '1445', '23', '45147', '0', NULL , 1, 1, '*', NOW(), NOW(), NULL, NULL, NULL, 0, 0, 1);");
		$db->query();

		$db->setQuery("INSERT IGNORE  INTO `#__miwovideos_playlist_videos` (`id` , `playlist_id`, `video_id`, `ordering`) VALUES
			(1, 1, 1, 1),
			(2, 1, 2, 1);");
		$db->query();

		# Videos
		$db->setQuery("INSERT IGNORE  INTO `#__miwovideos_videos` (`id`, `user_id`, `channel_id`, `product_id`, `title`, `alias`, `introtext`, `fulltext`, `source`, `duration`, `likes`, `dislikes`, `hits`, `access`, `price`, `created`, `modified`, `featured`, `published`, `fields`, `thumb`, `meta_desc`, `meta_key`, `meta_author`, `params`, `language`) VALUES
			(1, ".$user_id.", 0, 0, 'EOS 600D Sample Video', 'eos-600d-sample-video', 'Your EOS adventure starts here with the new Canon EOS 600D, empowering enthusiast photographers to capture outstanding', 'stills and Full HD video. Be the first to see some of its features in this sample video shot entirely on the camera itself', 'http://www.youtube.com/watch?v=KAi2QIq1SUs', '90', '585', '14', '45689', '1', NULL, NOW(), NOW(), '0', '1', '{\"miwi_license\":\"Standard YouTube License\"}', 'http://i1.ytimg.com/vi/KAi2QIq1SUs/mqdefault.jpg', NULL, NULL, NULL, NULL, '*');");
		$db->query();

		$db->setQuery("INSERT IGNORE  INTO `#__miwovideos_videos` (`id`, `user_id`, `channel_id`, `product_id`, `title`, `alias`, `introtext`, `fulltext`, `source`, `duration`, `likes`, `dislikes`, `hits`, `access`, `price`, `created`, `modified`, `featured`, `published`, `fields`, `thumb`, `meta_desc`, `meta_key`, `meta_author`, `params`, `language`) VALUES
			(2, ".$user_id.", 0, 0, 'Canon Eos 550D sample video', 'canon-eos-550d-sample-video', 'Sample video from the new Canon EOS 550D shot at 1920x1080 resolution (30fps) and optimised for YouTube. Offering Full HD', 'movie recording with manual control and selectable frame rates, the EOS 550D allows you to capture everything in stunning detail.', 'http://www.youtube.com/watch?v=3f7l-Z4NF70', '193', '585', '14', '45689', '1', NULL, NOW(), NOW(), '0', '1', '{\"miwi_license\":\"Standard YouTube License\"}', 'http://i1.ytimg.com/vi/3f7l-Z4NF70/mqdefault.jpg', NULL, NULL, NULL, NULL, '*');");
		$db->query();

		$db->setQuery("INSERT IGNORE  INTO `#__miwovideos_videos` (`id`, `user_id`, `channel_id`, `product_id`, `title`, `alias`, `introtext`, `fulltext`, `source`, `duration`, `likes`, `dislikes`, `hits`, `access`, `price`, `created`, `modified`, `featured`, `published`, `fields`, `thumb`, `meta_desc`, `meta_key`, `meta_author`, `params`, `language`) VALUES
			(3, ".$user_id.", 0, 0, 'Robocop - The Future Of American Mustice', 'robocop-the-future-of-american-justice', 'In 2028 Detroit, when Alex Murphy (Moel Kinnaman) - a lovaing husband, father and good cop - is critically injured in the line of duty, the multinational con...', '', 'http://www.youtube.com/watch?v=7VPbtuevHls', '57', '132', '9', '21325', '1', NULL, NOW(), NOW(), '0', '1', '{\"miwi_license\":\"Standard YouTube License\"}', 'http://i1.ytimg.com/vi/7VPbtuevHls/hqdefault.jpg', NULL, NULL, NULL, NULL, '*');");
		$db->query();

		$db->setQuery("INSERT IGNORE  INTO `#__miwovideos_videos` (`id`, `user_id`, `channel_id`, `product_id`, `title`, `alias`, `introtext`, `fulltext`, `source`, `duration`, `likes`, `dislikes`, `hits`, `access`, `price`, `created`, `modified`, `featured`, `published`, `fields`, `thumb`, `meta_desc`, `meta_key`, `meta_author`, `params`, `language`) VALUES
			(4, ".$user_id.", 0, 0, 'Eminem - The Monster (Explicit) ft. Rihanna', 'eminem-the-monster-explicit-ft-rihanna', 'Download Eminem\'s \'MMLP2\' Album on iTunes now:http://smarturl.it/MMLP2 Music video by Eminem ft. Rihanna \"The Monster\" 2013 Interscope', '', 'http://www.youtube.com/watch?v=EHkozMIXZ8w', '319', '721612', '24072', '87379128', '1', NULL, NOW(), NOW(), '1', '1', '{\"miwi_license\":\"Standard YouTube License\"}', 'http://i1.ytimg.com/vi/EHkozMIXZ8w/hqdefault.jpg', NULL, NULL, NULL, NULL, '*');");
		$db->query();

		$db->setQuery("INSERT IGNORE  INTO `#__miwovideos_videos` (`id`, `user_id`, `channel_id`, `product_id`, `title`, `alias`, `introtext`, `fulltext`, `source`, `duration`, `likes`, `dislikes`, `hits`, `access`, `price`, `created`, `modified`, `featured`, `published`, `fields`, `thumb`, `meta_desc`, `meta_key`, `meta_author`, `params`, `language`) VALUES
			(5, ".$user_id.", 0, 0, 'Top 10 Dunks in the All-Star Game', 'top-10-dunks-in-the-all-star-game', 'Check out the Top 10 dunks in the 62 year history of the All-Star Game. Visit nba.com/video for more highlights. About the NBA: The NBA is the premier profes...', '', 'http://www.youtube.com/watch?v=aXgkgai-OI0', '182', '1997', '972', '114552', '1', NULL, NOW(), NOW(), '1', '1', '{\"miwi_license\":\"Standard YouTube License\"}', 'http://i1.ytimg.com/vi/aXgkgai-OI0/hqdefault.jpg', NULL, NULL, NULL, NULL, '*');");
		$db->query();

		$db->setQuery("INSERT IGNORE  INTO `#__miwovideos_videos` (`id`, `user_id`, `channel_id`, `product_id`, `title`, `alias`, `introtext`, `fulltext`, `source`, `duration`, `likes`, `dislikes`, `hits`, `access`, `price`, `created`, `modified`, `featured`, `published`, `fields`, `thumb`, `meta_desc`, `meta_key`, `meta_author`, `params`, `language`) VALUES
			(6, ".$user_id.", 0, 0, 'League of Legends Cinematic: A Twist of Fate', 'league-of-legends-cinematic-a-twist-of-fate', 'Get up close and personal with your favorite champions in the League of Legends Cinematic: A Twist of Fate.', '', 'http://www.youtube.com/watch?v=tEnsqpThaFg', '275', '277296', '4365', '26381509', '1', NULL, NOW(), NOW(), '0', '1', '{\"miwi_license\":\"Standard YouTube License\"}', 'http://i1.ytimg.com/vi/tEnsqpThaFg/hqdefault.jpg', NULL, NULL, NULL, NULL, '*');");
		$db->query();

		$db->setQuery("INSERT IGNORE  INTO `#__miwovideos_videos` (`id`, `user_id`, `channel_id`, `product_id`, `title`, `alias`, `introtext`, `fulltext`, `source`, `duration`, `likes`, `dislikes`, `hits`, `access`, `price`, `created`, `modified`, `featured`, `published`, `fields`, `thumb`, `meta_desc`, `meta_key`, `meta_author`, `params`, `language`) VALUES
			(7, ".$user_id.", 0, 0, 'Fast & Furious 6 - Official Trailer [HD]', 'fast-furious-6-official-trailer-hd', 'Since Dom (Diesel) and Brians (Walker) Rio heist toppled a kingpins empire and left their crew with $100 million, our heroes have scattered across the globe....', '', 'http://www.youtube.com/watch?v=PP7pH4pqC5A', '155', '7396', '302', '2546526', '1', NULL, NOW(), NOW(), '1', '1', '{\"miwi_license\":\"Standard YouTube License\"}', 'http://i1.ytimg.com/vi/PP7pH4pqC5A/hqdefault.jpg', NULL, NULL, NULL, NULL, '*');");
		$db->query();

		$db->setQuery("INSERT IGNORE  INTO `#__miwovideos_videos` (`id`, `user_id`, `channel_id`, `product_id`, `title`, `alias`, `introtext`, `fulltext`, `source`, `duration`, `likes`, `dislikes`, `hits`, `access`, `price`, `created`, `modified`, `featured`, `published`, `fields`, `thumb`, `meta_desc`, `meta_key`, `meta_author`, `params`, `language`) VALUES
			(8, ".$user_id.", 0, 0, 'The Hobbit : Desolation Of Smaug - I Found', 'the-hobbit-desolation-of-smaug-i-found-something-in-the-goblin-tunnels', 'The Dwarves, Bilbo and Gandalf have successfully escaped the Misty Mountains, and Bilbo has gained the One Ring. They all continue their journey to get their...', '', 'http://www.youtube.com/watch?v=UgjeDk_NWCU', '48', '89', '33', '18477', '1', NULL, NOW(), NOW(), '0', '1', '{\"miwi_license\":\"Standard YouTube License\"}', 'http://i1.ytimg.com/vi/UgjeDk_NWCU/hqdefault.jpg', NULL, NULL, NULL, NULL, '*');");
		$db->query();

		$db->setQuery("INSERT IGNORE  INTO `#__miwovideos_videos` (`id`, `user_id`, `channel_id`, `product_id`, `title`, `alias`, `introtext`, `fulltext`, `source`, `duration`, `likes`, `dislikes`, `hits`, `access`, `price`, `created`, `modified`, `featured`, `published`, `fields`, `thumb`, `meta_desc`, `meta_key`, `meta_author`, `params`, `language`) VALUES
			(9, ".$user_id.", 0, 0, 'Queen - We Will Rock You', 'queen-we-will-rock-you', 'Music video by Queen performing We Will Rock You. (C) 1977 Queen Productions Ltd. under exclusive licence to Universal International Music BV', '', 'http://www.youtube.com/watch?v=XMLiqEqMQyQ', '129', '61573', '2102', '11135550', '1', NULL, NOW(), NOW(), '1', '1', '{\"miwi_license\":\"Standard YouTube License\"}', 'http://i1.ytimg.com/vi/XMLiqEqMQyQ/hqdefault.jpg', NULL, NULL, NULL, NULL, '*');");
		$db->query();

		$db->setQuery("INSERT IGNORE  INTO `#__miwovideos_videos` (`id`, `user_id`, `channel_id`, `product_id`, `title`, `alias`, `introtext`, `fulltext`, `source`, `duration`, `likes`, `dislikes`, `hits`, `access`, `price`, `created`, `modified`, `featured`, `published`, `fields`, `thumb`, `meta_desc`, `meta_key`, `meta_author`, `params`, `language`) VALUES
			(10, ".$user_id.", 0, 0, 'Introducing MiwoVideos', 'introducing-miwovideos', 'Introducing, MiwoVideos: The revolutionary video sharing component for Moomla. MiwoVideos allows you to turn your site into a professional looking video-sharing website with user-interface and features similar to YouTube. The interface has been thought in detail and designed specifically for the users.', '', 'http://www.youtube.com/watch?v=QTxA6XnAQas', '126', '3487', '0', '135657', '1', NULL, NOW(), NOW(), '1', '1', '{\"miwi_license\":\"Standard YouTube License\"}', 'http://i1.ytimg.com/vi/QTxA6XnAQas/maxresdefault.jpg', NULL, NULL, NULL, NULL, '*');");
		$db->query();

		$db->setQuery("INSERT IGNORE  INTO `#__miwovideos_video_categories` (`id` , `video_id`, `category_id`) VALUES
			(1, 1, 2),
			(2, 2, 2),
			(3, 3, 3),
			(4, 4, 4),
			(5, 5, 5),
			(6, 6, 6),
			(7, 7, 3),
			(8, 8, 4),
			(9, 9, 3),
			(10, 10, 2);");
		$db->query();

		$this->addPage();
	}

	protected function _updateMiwovideos() {
		if (empty($this->_current_version)) {
			return;
		}

		if ($this->_current_version < '1.1.0') {
			$config = MiwoVideos::getConfig();

			$config->set('flv_240p', 1);
			$config->set('flv_360p', 1);
			$config->set('flv_480p', 1);
			$config->set('flv_720p', 1);
			$config->set('flv_1080p', 1);
			$config->set('metadata_injector', 'yamdi');
			$config->set('flvtool2_path', '/usr/bin/flvtool2');
			$config->set('yamdi_path', '/usr/bin/yamdi');
			$config->set('fallback', 0);

			MiwoVideos::get('utility')->storeConfig($config);

			MiwoDB::query("INSERT IGNORE INTO `#__miwovideos_process_type` (`id`, `title`, `alias`, `filetype`, `size`, `ordering`, `published`)
							VALUES
								(26, 'Flv (240p)', 'flv-240p', 'flv', '240', '26', '1'),
								(27, 'Flv (360p)', 'flv-360p', 'flv', '360', '27', '1'),
								(28, 'Flv (480p)', 'flv-480p', 'flv', '480', '28', '1'),
								(29, 'Flv (720p)', 'flv-720p', 'flv', '720', '29', '1'),
								(30, 'Flv (1080p)', 'flv-1080p', 'flv', '1080', '30', '1');");
		}

		if ($this->_current_version < '1.1.2') {
			MiwoDB::query("ALTER TABLE `#__miwovideos_videos` ADD ordering INT(0) DEFAULT '0' AFTER published");
		}

		if ($this->_current_version < '1.2.0') {
			MiwoDB::query("ALTER TABLE `#__miwovideos_videos` CHANGE `source` `source` TEXT NOT NULL ;");
		}
		
		if (version_compare($this->_current_version, '1.2.1', 'lt')) {
			MiwoDB::query("CREATE TABLE IF NOT EXISTS `#__miwovideos_process_log` (
							  `id`              INT(11)          NOT NULL AUTO_INCREMENT,
							  `process_id`      INT(11) UNSIGNED NOT NULL DEFAULT '0',
							  `video_id`        INT(11) UNSIGNED NOT NULL DEFAULT '0',
							  `input`           VARCHAR(5120)    NOT NULL DEFAULT '',
							  `output`          LONGTEXT    	 NOT NULL,
							  `created`         DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00',
							  `created_user_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
							  PRIMARY KEY (`id`)
							)
							  DEFAULT CHARSET = utf8;");
		}
	}

	public function uninstall($parent) {
		$db  = MFactory::getDBO();
		$src = __FILE__;
	}

	public function addPage() {
		$page_content = "<!-- MiwoVideos Shortcode. Please do not remove to video plugin work properly. -->[miwovideos]<!-- MiwoVideos Shortcode End. -->";
		add_option("miwovideos_page_id", '', '', 'yes');

		$miwovideos_post = array();
		$_tmp_page       = null;

		$id = get_option("miwovideos_page_id");

		if (!empty($id) && $id > 0) {
			$_tmp_page = get_post($id);
		}

		if ($_tmp_page != null) {
			$miwovideos_post['ID']          = $id;
			$miwovideos_post['post_status'] = 'publish';

			wp_update_post($miwovideos_post);
		}
		else {
			$miwovideos_post['post_title']     = 'Videos';
			$miwovideos_post['post_content']   = $page_content;
			$miwovideos_post['post_status']    = 'publish';
			$miwovideos_post['post_author']    = 1;
			$miwovideos_post['post_type']      = 'page';
			$miwovideos_post['comment_status'] = 'closed';

			$id = wp_insert_post($miwovideos_post);
			update_option('miwovideos_page_id', $id);
		}
	}
}
