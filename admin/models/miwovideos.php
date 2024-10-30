<?php
/**
 * @package        MiwoVideos
 * @copyright      Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license        GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die('Restricted access');

class MiwovideosModelMiwovideos extends MiwovideosModel {

	public function __construct() {
		parent::__construct('miwovideos');
	}

	public function savePersonalID() {
		$pid = trim(MRequest::getVar('pid', '', 'post', 'string'));

		if (!empty($pid)) {
			$config = MiwoVideos::getConfig();
			$config->set('pid', $pid);

			MiwoVideos::get('utility')->storeConfig($config);
		}
	}

	public function jusersync() {

		$users = MiwoDB::loadObjectList('SELECT ID, user_login name FROM `#__users`');

		if (!empty($users)) {
			foreach ($users as $user) {
				$user->name = str_replace("'", "\\'", $user->name);
				MiwoDB::query("INSERT INTO `#__miwovideos_channels` (`user_id`, `title`, `alias`, `introtext`, `fulltext`, `thumb`, `banner`, `fields`,`likes`, `dislikes`, `hits`, `params`, `ordering`, `access`, `language`, `created`, `modified`, `meta_desc`, `meta_key`, `meta_author`, `share_others`, `featured`, `published`, `default`) VALUES
                ({$user->ID}, '{$user->name}', '{$user->name}', '{$user->name}', '', '', '', '', 0, 0, 0, '', '', 1, '*', NOW(), NOW(), '', '', '', 0, 0, 1, 1)");
				$channel_id = MiwoDB::insertid();

				# Watch Later
				MiwoDB::query("INSERT INTO `#__miwovideos_playlists` (`channel_id`, `user_id`, `type`, `title`, `alias`, `introtext`, `fulltext`, `thumb`, `fields`,`likes`, `dislikes`, `hits`, `subscriptions`, `params`, `ordering`, `access`, `language`, `created`, `modified`, `meta_desc`, `meta_key`, `meta_author`, `share_others`, `featured`, `published`) VALUES
                ({$channel_id}, {$user->ID}, 1, 'Watch Later', '', '', '', '', '', '', 0, 0, 0, '', '', 1, '*', NOW(), NOW(), '', '', '', 0, 0, 1)");

				# Favorite Videos
				MiwoDB::query("INSERT INTO `#__miwovideos_playlists` (`channel_id`, `user_id`,  `type`, `title`, `alias`, `introtext`, `fulltext`, `thumb`, `fields`,`likes`, `dislikes`, `hits`, `subscriptions`, `params`, `ordering`, `access`, `language`, `created`, `modified`, `meta_desc`, `meta_key`, `meta_author`, `share_others`, `featured`, `published`) VALUES
                ({$channel_id}, {$user->ID}, 2, 'Favorite Videos', '', '', '', '', '', '', 0, 0, 0, '', '', 1, '*', NOW(), NOW(), '', '', '', 0, 0, 1)");
			}

			$channel_id = MiwoVideos::get('channels')->getDefaultChannel()->id;
			MiwoDB::query('UPDATE `#__miwovideos_videos` SET `channel_id` = '.$channel_id.' WHERE `channel_id` = 0');
			MiwoDB::query('UPDATE `#__miwovideos_playlists` SET `channel_id` = '.$channel_id.' WHERE `channel_id` = 0');

			$config = MiwoVideos::getConfig();
			$config->set('jusersync', 1);

			MiwoVideos::get('utility')->storeConfig($config);
		}
	}

	# Check info
	public function getInfo() {
		static $info;

		if (!isset($info)) {
			if (empty($info)) {
				$info = array();

				if ($this->config->get('version_checker') == 1) {
					$utility                   = MiwoVideos::get('utility');
					$info['version_installed'] = $utility->getMiwovideosVersion();
					$info['version_latest']    = $utility->getLatestMiwovideosVersion();

					# Set the version status
					$info['version_status']  = version_compare($info['version_installed'], $info['version_latest']);
					$info['version_enabled'] = 1;
				}
				else {
					$info['version_status']  = 0;
					$info['version_enabled'] = 0;
				}

				$info['pid'] = $this->config->get('pid');

				$cache = MFactory::getCache('com_miwovideos', 'output');
				$cache->setCaching(1);

				if (!$server = $cache->get('server_info', 'com_miwovideos')) {
					$server   = array();
					$version = MiwoVideos::get('utility')->getFfmpegVersion();
					$server[] = array(
						'name'  => 'FFmpeg',
						'value' => $version ? $version : MText::_('MNO')
					);
					$server[] = array(
						'name'  => 'allow_fileuploads',
						'value' => ini_get('file_uploads') ? MText::_('MYES') : MText::_('MNO')
					);
					$server[] = array('name' => 'upload_max_filesize', 'value' => ini_get('upload_max_filesize'));
					$server[] = array('name' => 'max_input_time', 'value' => ini_get('max_input_time'));
					$server[] = array('name' => 'memory_limit', 'value' => ini_get('memory_limit'));
					$server[] = array('name' => 'max_execution_time', 'value' => ini_get('max_execution_time'));
					$server[] = array('name' => 'post_max_size', 'value' => ini_get('post_max_size'));
					$server[] = array(
						'name'  => 'upload_folder_permission',
						'value' => (is_writable(MIWOVIDEOS_UPLOAD_DIR.'/')) ? MText::_('MYES') : MText::_('MNO')
					);
					$server[] = array(
						'name'  => 'curl',
						'value' => (extension_loaded('curl')) ? MText::_('MYES') : MText::_('MNO')
					);
					$server[] = array(
						'name'  => 'exec',
						'value' => (function_exists('exec')) ? MText::_('MYES') : MText::_('MNO')
					);
					$server[] = array(
						'name'  => 'flvtool2',
						'value' => $this->checkFlvtool2()
					);
					$server[] = array(
						'name'  => 'yamdi',
						'value' => $this->checkYamdi()
					);
					if ($this->config->get('perl_upload')) {
						$server[] = array(
							'name'  => 'ubr_upload script',
							'value' => ($this->isUrlExist($this->config->get('uber_upload_perl_url'))) ? MText::_('MYES') : MText::_('MNO')
						);
					}
					$cache->store($server, 'server_info', 'com_miwovideos');
				}

				$info['server'] = $server;
			}
		}

		return $info;
	}

	public function checkFlvtool2() {
		if (substr(PHP_OS, 0, 3) != "WIN") {
			MiwoVideos::exec("which flvtool2 2>&1", $output, $error);
		}
		else {
			MiwoVideos::exec("where flvtool2.exe", $output, $error);
		}

		MiwoVideos::log($output);
		MiwoVideos::log($error);

		if ($error === false) {
			return $output[0];
		}
		else {
			return MText::_('MNO');
		}
	}

	public function checkYamdi() {
		if (substr(PHP_OS, 0, 3) != "WIN") {
			MiwoVideos::exec("which yamdi 2>&1", $output, $error);
		}
		else {
			MiwoVideos::exec("where yamdi", $output, $error);
		}

		MiwoVideos::log($output);
		MiwoVideos::log($error);

		if ($error === false) {
			return $output[0];
		}
		else {
			return MText::_('MNO');
		}
	}

	public function isUrlExist($url) {
		$status = false;
		if (extension_loaded('curl')) {
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_NOBODY, true);
			curl_exec($ch);
			$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

			if ($code == 200) {
				$status = true;
			}
			else {
				$status = false;
			}
			curl_close($ch);
		}

		return $status;
	}

	public function getStats() {
		$count = array();

		$where = '';
		if (!MiwoVideos::get('acl')->canManage()) {
			$user_id = MFactory::getUser()->get('id');

			$where = ' WHERE user_id = '.$user_id;
		}

		$count['categories']    = MiwoDB::loadResult("SELECT COUNT(*) FROM #__miwovideos_categories");
		$count['channels']      = MiwoDB::loadResult("SELECT COUNT(*) FROM #__miwovideos_channels {$where}");
		$count['playlists']     = MiwoDB::loadResult("SELECT COUNT(*) FROM #__miwovideos_playlists {$where}");
		$count['videos']        = MiwoDB::loadResult("SELECT COUNT(*) FROM #__miwovideos_videos {$where}");
		$count['subscriptions'] = MiwoDB::loadResult("SELECT COUNT(*) FROM #__miwovideos_subscriptions {$where}");

		return $count;
	}
}