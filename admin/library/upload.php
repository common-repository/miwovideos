<?php
/**
 * @package        MiwoVideos
 * @copyright      Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license        GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die('Restricted Access');

class MiwovideosUpload extends MObject {

	public function __construct() {
		$this->config    = MiwoVideos::getConfig();
		$this->_id       = null;
		$this->_title    = null;
		$this->_filename = null;
		$this->_count    = 0;
	}

	public function process() {
		$user = MFactory::getUser();
		$date = MFactory::getDate();
		$acl  = MiwoVideos::get('acl');
		$row  = MiwoVideos::getTable('MiwovideosVideos');

		//@TODO : Delete if not needed
		mimport('framework.filesystem.file');

		//Retrieve file details from uploaded file, sent from upload form
		$file = MRequest::getVar('Filedata', null, 'files', 'array');

		// For Dropzone upload
		if (empty($file)) {
			$file = MRequest::getVar('file', null, 'files', 'array');
		}
		$ext = strtolower(MFile::getExt($file['name']));

		$item_id = MiwoVideos::getInput()->getInt('item_id', null);

		//@TODO Check if we need to replace an existing media item

		if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
			$this->setError(MText::_('COM_MIWOVIDEOS_ERROR_UBER_PHP_UPLOAD'));
			return false;
		}
		else {

			//$duration = MiwoVideos::get('videos')->getDuration();

			$post         = array();
			$this->_title = strrchr($file['name'], '.');
			if ($ext !== false) {
				$this->_title = substr($file['name'], 0, -strlen(".".$ext));
			}


			$model = MiwoVideos::get('controller')->getModel('videos');
			if (!empty($item_id) and $item_id > 0 and $acl->canEdit()) {
				$row->load($item_id);
				if (strpos($row->source, 'http://') === false) {
					$files       = MiwoVideos::get('files')->getVideoFiles($item_id);
					$files_model = MiwoVideos::get('controller')->getModel('files');
					foreach ($files as $dfile) {
						$ids[] = $dfile->id;
					}
					$files_model->delete($ids);
				}
				MRequest::setVar('cid', $item_id, 'post');
				$row->id = $item_id;
				MiwoVideos::get('controller')->updateField('videos', 'modified', $date->format('Y-m-d H:i:s'), $model);
			}
			else {
				$channel_id    = MiwoVideos::getInput()->getInt('channel_id', null);
				$channel_table = MiwoVideos::getTable('MiwovideosChannels');
				$channel_table->load($channel_id);

				if (!$channel_table->share_others) {
					$channel_id = MiwoVideos::get('channels')->getDefaultChannel()->id;
				}

				if (empty($channel_id)) {
					$channel_id = MiwoVideos::get('channels')->getDefaultChannel()->id;
				}
				$post['user_id']     = $user->id;
				$post['channel_id']  = $channel_id;
				$post['product_id']  = '';
				$post['title']       = $this->_title;
				$post['alias']       = MFilterOutput::stringURLSafe($post['title']);
				$post['introtext']   = '';
				$post['fulltext']    = '';
				$post['source']      = '';
				$post['duration']    = '';
				$post['likes']       = '';
				$post['dislikes']    = '';
				$post['hits']        = '';
				$post['access']      = 1;
				$post['price']       = '';
				$post['created']     = $date->format('Y-m-d H:i:s');
				$post['modified']    = $date->format('Y-m-d H:i:s');
				$post['published']   = MiwoVideos::get('acl')->canAutoPublish() ? 1 : 0;
				$post['featured']    = 0;
				$post['fields']      = '';
				$post['thumb']       = '';
				$post['licence']     = '';
				$post['meta_desc']   = '';
				$post['meta_key']    = '';
				$post['meta_author'] = '';
				$post['params']      = '';
				$post['ordering']    = '';
				$post['language']    = '*';
				// Bind it to the table
				if (!$row->bind($post)) {
					$this->setError($row->getError());
					return false;
				}

				// Store it in the db
				if (!$row->store()) {
					$this->setError($row->getError());
					return false;
				}
			}
		}

		$src = $file['tmp_name'];

		// Hash with file extension
		$filename = hash('haval256,5', $file['name']).".".$ext;

		$this->_id       = $row->id;
		$this->_filename = $filename;

		if (MFile::upload($src, MIWOVIDEOS_UPLOAD_DIR."/videos/".$row->id."/orig/".$filename)) {
			MiwoVideos::get('files')->add($row, $ext, $filename, 'orig', 200);
		}
		else {
			$this->setError(MText::_('COM_MIWOVIDEOS_ERROR_FILE_COULD_NOT_BE_COPIED_TO_UPLOAD_DIRECTORY'));
			return false;
		}

		// Update source field
		MRequest::setVar('cid', $row->id, 'post');
		MiwoVideos::get('controller')->updateField('videos', 'source', $filename, $model);
		$row->source = $filename;

		$this->addProcesses($row);

		if ($this->config->get('upload_script') == 'fancy' and !empty($file)) {
			if (!MiwoVideos::get('utility')->backgroundTask($row->id, $filename)) {
				$this->setError(MText::_('COM_MIWOVIDEOS_ERROR_PHP_UPLOAD'));
				return false;
			}
		}

		MiwoVideos::get('utility')->trigger('onFinderAfterSave', array('com_miwovideos.videos', $row->id, null), 'finder');

		return true;
	}

	public function addProcesses($item) {
		if (!MiwoVideos::get('utility')->getFfmpegVersion()) {
			$this->setError('FFmpeg is not installed');
			return false;
		}

		$config = $this->config;

		$location = MIWOVIDEOS_UPLOAD_DIR.'/videos/'.$item->id.'/orig/'.$item->source;

		if (!file_exists($location)) {
			return false;
		}
		if (substr(PHP_OS, 0, 3) == "WIN") {
			$command = "\"".$config->get('ffmpeg_path', '/usr/bin/ffmpeg')."\" -i $location 2>&1";
		}
		else {
			$command = $config->get('ffmpeg_path', '/usr/bin/ffmpeg')." -i $location 2>&1";
		}

		MiwoVideos::exec($command, $output);
		MiwoVideos::log('FFmpeg : '.$command);
		MiwoVideos::log($output);

		$flatoutput = is_array($output) ? implode("\n", $output) : $output;
		if (empty($flatoutput)) {
			return false;
		}
		else {
			$pos = strpos($flatoutput, "No such file or directory");
			if ($pos !== false) {
				$this->setError('No such file or directory');
				return false;
			}

			$pos = strpos($flatoutput, "not found");
			if ($pos !== false) {
				$this->setError('Not found');
				return false;
			}

			$pos = strpos($flatoutput, "Permission denied");
			if ($pos !== false) {
				$this->setError('Permission denied');
				return false;
			}
		}
		$input_height = 0;

		// Get original size
		if (preg_match('/Stream.*Video:.* (\d+)x(\d+).* (\d+\.\d+|\d+) tbr/', implode("\n", $output), $matches)) {
			$input_height = $matches[2];
		}
		elseif (preg_match('/Stream.*Video:.* (\d+)x(\d+).* (\d+\.\d+|\d+) tb/', implode("\n", $output), $matches)) {
			$input_height = $matches[2];
		}

		// Check we are meant to be processing
		if ($this->config->get('process_videos') == 0) {
			return true;
		}

		$processes = array();

		$process_lib = MiwoVideos::get('processes');

		$video_size = MiwoVideos::get('utility')->getVideoSize($item->id, $item->source);
		$thumb_size = MiwoVideos::get('utility')->getThumbSize($this->config->get('thumb_size'));

		$input_height >= 75 && $thumb_size != 75 ? $processes[] = $process_lib->add($item, '1', $this->config->get('jpeg_75')) : null;
		$input_height >= 100 && $thumb_size != 100 ? $processes[] = $process_lib->add($item, '2', $this->config->get('jpeg_100')) : null;
		$input_height >= 240 && $thumb_size != 240 ? $processes[] = $process_lib->add($item, '3', $this->config->get('jpeg_240')) : null;
		$input_height >= 500 && $thumb_size != 500 ? $processes[] = $process_lib->add($item, '4', $this->config->get('jpeg_500')) : null;
		$input_height >= 640 && $thumb_size != 640 ? $processes[] = $process_lib->add($item, '5', $this->config->get('jpeg_640')) : null;
		$input_height >= 1024 && $thumb_size != 1024 ? $processes[] = $process_lib->add($item, '6', $this->config->get('jpeg_1024')) : null;
		$input_height >= 240 && $video_size != 240 ? $processes[] = $process_lib->add($item, '7', $this->config->get('mp4_240p')) : null;
		$input_height >= 360 && $video_size != 360 ? $processes[] = $process_lib->add($item, '8', $this->config->get('mp4_360p')) : null;
		$input_height >= 480 && $video_size != 480 ? $processes[] = $process_lib->add($item, '9', $this->config->get('mp4_480p')) : null;
		$input_height >= 720 && $video_size != 720 ? $processes[] = $process_lib->add($item, '10', $this->config->get('mp4_720p')) : null;
		$input_height >= 1080 && $video_size != 1080 ? $processes[] = $process_lib->add($item, '11', $this->config->get('mp4_1080p')) : null;
		$input_height >= 240 && $video_size != 240 ? $processes[] = $process_lib->add($item, '12', $this->config->get('webm_240p')) : null;
		$input_height >= 360 && $video_size != 360 ? $processes[] = $process_lib->add($item, '13', $this->config->get('webm_360p')) : null;
		$input_height >= 480 && $video_size != 480 ? $processes[] = $process_lib->add($item, '14', $this->config->get('webm_480p')) : null;
		$input_height >= 720 && $video_size != 720 ? $processes[] = $process_lib->add($item, '15', $this->config->get('webm_720p')) : null;
		$input_height >= 1080 && $video_size != 1080 ? $processes[] = $process_lib->add($item, '16', $this->config->get('webm_1080p')) : null;
		$input_height >= 240 && $video_size != 240 ? $processes[] = $process_lib->add($item, '17', $this->config->get('ogg_240p')) : null;
		$input_height >= 360 && $video_size != 360 ? $processes[] = $process_lib->add($item, '18', $this->config->get('ogg_360p')) : null;
		$input_height >= 480 && $video_size != 480 ? $processes[] = $process_lib->add($item, '19', $this->config->get('ogg_480p')) : null;
		$input_height >= 720 && $video_size != 720 ? $processes[] = $process_lib->add($item, '20', $this->config->get('ogg_720p')) : null;
		$input_height >= 1080 && $video_size != 1080 ? $processes[] = $process_lib->add($item, '21', $this->config->get('ogg_1080p')) : null;
		$input_height >= 240 ? $processes[] = $process_lib->add($item, '26', $this->config->get('flv_240p')) : null;
		$input_height >= 360 ? $processes[] = $process_lib->add($item, '27', $this->config->get('flv_360p')) : null;
		$input_height >= 480 ? $processes[] = $process_lib->add($item, '28', $this->config->get('flv_480p')) : null;
		$input_height >= 720 ? $processes[] = $process_lib->add($item, '29', $this->config->get('flv_720p')) : null;
		$input_height >= 1080 ? $processes[] = $process_lib->add($item, '30', $this->config->get('flv_1080p')) : null;

		// Inject Metadata
		$processes[] = $process_lib->add($item, '22', $this->config->get('flv_240p') or $this->config->get('flv_360p') or $this->config->get('flv_360p') or $this->config->get('flv_480p') or $this->config->get('flv_720p') or $this->config->get('flv_1080p'));

		//$processes[] = $process_lib->add($item, '23', 1); // Move moov atom
		//$processes[] = $process_lib->add($item, '24', 1); // Get duration
		//$processes[] = $process_lib->add($item, '25', 1); //Get title

		$process_lib->add($item, '100', 1); // Convert videos to HTML5.

		if (!$this->config->get('auto_process_videos')) {
			return false;
		}


		$cli    = MPATH_MIWI.'/cli/miwovideoscli.php';
		$output = '';
		if (substr(PHP_OS, 0, 3) != "WIN") {
			// @TODO Log if throw an error
			$command = "env -i ".$this->config->get('php_path', '/usr/bin/php')." $cli process ".implode(" ", $processes)." > /dev/null 2>&1 &";
		}
		else {
			MiwoVideos::exec('where php.exe', $php_path);
			// @TODO Log if throw an error
			$command = $config->get('php_path', $php_path)." $cli process ".implode(" ", $processes)." NUL";
		}

		MiwoVideos::exec($command, $output, $error);
		MiwoVideos::log('CLI : '.$command);
		MiwoVideos::log($output);
		MiwoVideos::log($error);

	}

	public function uber() {
		$user   = & MFactory::getUser();
		$date   =& MFactory::getDate();
		$config = $this->config;

//******************************************************************************************************
//   ATTENTION: THIS FILE HEADER MUST REMAIN INTACT. DO NOT DELETE OR MODIFY THIS FILE HEADER.
//
//   Name: ubr_finished.php
//   Revision: 1.3
//   Date: 2/18/2008 5:36:57 PM
//   Link: http://uber-uploader.sourceforge.net
//   Initial Developer: Peter Schmandra  http://www.webdice.org
//   Description: Show successful file uploads.
//
//   Licence:
//   The contents of this file are subject to the Mozilla Public
//   License Version 1.1 (the "License"); you may not use this file
//   except in compliance with the License. You may obtain a copy of
//   the License at http://www.mozilla.org/MPL/
//
//   Software distributed under the License is distributed on an "AS
//   IS" basis, WITHOUT WARRANTY OF ANY KIND, either express or
//   implied. See the License for the specific language governing
//   rights and limitations under the License.
//
//***************************************************************************************************************

//***************************************************************************************************************
// The following possible query string formats are assumed
//
// 1. ?upload_id=upload_id
// 2. ?about=1
//****************************************************************************************************************

		$THIS_VERSION = "1.3"; // Version of this file
		$UPLOAD_ID    = ''; // Initialize upload id

		require_once(MPATH_MIWOVIDEOS_ADMIN.'/library/uber/ubr_ini.php');
		require_once(MPATH_MIWOVIDEOS_ADMIN.'/library/uber/ubr_lib.php');
		require_once(MPATH_MIWOVIDEOS_ADMIN.'/library/uber/ubr_finished_lib.php');

		if ($PHP_ERROR_REPORTING) {
			error_reporting(E_ALL);
		}

		if (preg_match("/^[a-zA-Z0-9]{32}$/", $_GET['upload_id'])) {
			$UPLOAD_ID = $_GET['upload_id'];
		}
		elseif (isset($_GET['about']) && $_GET['about'] == 1) {
			kak("<u><b>UBER UPLOADER FINISHED PAGE</b></u><br>UBER UPLOADER VERSION =  <b>".$UBER_VERSION."</b><br>UBR_FINISHED = <b>".$THIS_VERSION."<b><br>\n", 1, __LINE__);
		}
		else {
			kak("ERROR: Invalid parameters passed<br>", 1, __LINE__);
		}

//Declare local values
		$_XML_DATA        = array(); // Array of xml data read from the upload_id.redirect file
		$_CONFIG_DATA     = array(); // Array of config data read from the $_XML_DATA array
		$_POST_DATA       = array(); // Array of posted data read from the $_XML_DATA array
		$_FILE_DATA       = array(); // Array of 'FileInfo' objects read from the $_XML_DATA array
		$_FILE_DATA_TABLE = ''; // String used to store file info results nested between <tr> tags
		$_FILE_DATA_EMAIL = ''; // String used to store file info results

		$xml_parser = new XML_Parser; // XML parser
		$xml_parser->setXMLFile($TEMP_DIR, $_REQUEST['upload_id']); // Set upload_id.redirect file
		$xml_parser->setXMLFileDelete($DELETE_REDIRECT_FILE); // Delete upload_id.redirect file when finished parsing
		$xml_parser->parseFeed(); // Parse upload_id.redirect file

// Display message if the XML parser encountered an error
		if ($xml_parser->getError()) {
			kak($xml_parser->getErrorMsg(), 1, __LINE__);
		}

		$_XML_DATA    = $xml_parser->getXMLData(); // Get xml data from the xml parser
		$_CONFIG_DATA = getConfigData($_XML_DATA); // Get config data from the xml data
		$_POST_DATA   = getPostData($_XML_DATA); // Get post data from the xml data
		$_FILE_DATA   = getFileData($_XML_DATA); // Get file data from the xml data

// Output XML DATA, CONFIG DATA, POST DATA, FILE DATA to screen and exit if DEBUG_ENABLED.
		if ($DEBUG_FINISHED) {
			debug("<br><u>XML DATA</u>", $_XML_DATA);
			debug("<u>CONFIG DATA</u>", $_CONFIG_DATA);
			debug("<u>POST DATA</u>", $_POST_DATA);
			debug("<u>FILE DATA</u><br>", $_FILE_DATA);
			exit;
		}

//Create file upload table
		$_FILE_DATA_TABLE = getFileDataTable($_FILE_DATA, $_CONFIG_DATA);

// Create and send email
		if ($_CONFIG_DATA['send_email_on_upload']) {
			emailUploadResults($_FILE_DATA, $_CONFIG_DATA, $_POST_DATA);
		}

/////////////////////////////////////////////////////////////////////////////////////////////////
// NOTE: You can now access all XML values below this comment. eg.
//   $_XML_DATA['upload_dir']; or $_XML_DATA['link_to_upload'] etc
/////////////////////////////////////////////////////////////////////////////////////////////////
// NOTE: You can now access all config values below this comment. eg.
//   $_CONFIG_DATA['upload_dir']; or $_CONFIG_DATA['link_to_upload'] etc
/////////////////////////////////////////////////////////////////////////////////////////////////
// NOTE: You can now access all post values below this comment. eg.
//   $_POST_DATA['client_id']; or $_POST_DATA['check_box_1_'] etc
/////////////////////////////////////////////////////////////////////////////////////////////////
// NOTE: You can now access all file (slot, name, size, type) info below this comment. eg.
//   $_FILE_DATA[0]->name  or  $_FILE_DATA[0]->getFileInfo('name')
/////////////////////////////////////////////////////////////////////////////////////////////////

		// Get associations from ubr_upload and assign them to the mform array
		$data = array();
		if (isset($_POST_DATA['item_id'])) {
			$item_id = intval($_POST_DATA['item_id']);
		}
		else {
			$item_id = null;
		}
		MRequest::setVar('mform', $data);
		$acl = MiwoVideos::get('acl');

		foreach ($_FILE_DATA as $arrayKey => $slot) {

			$tmp_name = MIWOVIDEOS_UPLOAD_DIR.'/videos/'.$slot->name;
			if (!isset($slot->name) || !file_exists($tmp_name)) {
				$this->setError(MText::_('COM_MIWOVIDEOS_UPLOAD_ERROR'));

				return false;
			}

			$error = false;


			// Retrieve file details from uploaded file, sent from upload form
			mimport('framework.filesystem.file');
			$ext = strtolower(MFile::getExt($slot->name));

			if ($error === false) {
				$row = MiwoVideos::getTable('MiwovideosVideos');

				$post = array();

				$title = strrchr($_POST_DATA[ $slot->slot ], '.');
				if ($ext !== false) {
					$title = substr($_POST_DATA[ $slot->slot ], 0, -strlen(".".$ext));
				}

				$model = MiwoVideos::get('controller')->getModel('videos');
				if (!empty($item_id) and $item_id > 0 and $acl->canEdit()) {
					$row->load($item_id);
					if (strpos($row->source, 'http://') === false) {
						$files       = MiwoVideos::get('files')->getVideoFiles($item_id);
						$files_model = MiwoVideos::get('controller')->getModel('files');
						foreach ($files as $dfile) {
							$ids[] = $dfile->id;
						}
						$files_model->delete($ids);
					}
					MRequest::setVar('cid', $item_id, 'post');
					$row->id = $item_id;
					MiwoVideos::get('controller')->updateField('videos', 'modified', $date->format('Y-m-d H:i:s'), $model);
				}
				else {
					if (isset($_POST_DATA['channel_id'])) {
						$channel_id = intval($_POST_DATA['channel_id']);
					}
					else {
						$channel_id = null;
					}
					$channel_table = MiwoVideos::getTable('MiwovideosChannels');
					$channel_table->load($channel_id);

					if (!$channel_table->share_others) {
						$channel_id = MiwoVideos::get('channels')->getDefaultChannel()->id;
					}

					if (empty($channel_id)) {
						$channel_id = MiwoVideos::get('channels')->getDefaultChannel()->id;
					}
					$post['user_id']     = $user->id;
					$post['channel_id']  = $channel_id;
					$post['product_id']  = '';
					$post['title']       = $title;
					$post['alias']       = MFilterOutput::stringURLSafe($title);
					$post['introtext']   = '';
					$post['fulltext']    = '';
					$post['source']      = '';
					$post['duration']    = '';
					$post['likes']       = '';
					$post['dislikes']    = '';
					$post['hits']        = 0;
					$post['access']      = 1;
					$post['price']       = '';
					$post['created']     = $date->format('Y-m-d H:i:s');
					$post['modified']    = $date->format('Y-m-d H:i:s');
					$post['published']   = MiwoVideos::get('acl')->canAutoPublish() ? 1 : 0;
					$post['featured']    = 0;
					$post['fields']      = '';
					$post['thumb']       = '';
					$post['licence']     = '';
					$post['meta_desc']   = '';
					$post['meta_key']    = '';
					$post['meta_author'] = '';
					$post['params']      = '';
					$post['ordering']    = '';
					$post['language']    = '*';

					// Bind it to the table
					if (!$row->bind($post)) {
						$this->setError($row->getError());

						return false;
					}

					// Store it in the db
					if (!$row->store()) {
						$this->setError($row->getError());

						return false;
					}
				}
			}


			// Hash with file extension
			$filename = hash('haval256,5', $slot->name).".".$ext;
			$src      = $tmp_name;

			$types = explode('|', $config->get('allow_file_types'));

			if (in_array($ext, $types)) {
				MFolder::create(MIWOVIDEOS_UPLOAD_DIR."/videos/".$row->id."/orig");
				if (MFile::move($src, MIWOVIDEOS_UPLOAD_DIR."/videos/".$row->id."/orig/".$filename)) {
					//Redirect to a page of your choice
				}
				else {
					$this->setError(MText::_('COM_MIWOVIDEOS_ERROR_FILE_COULD_NOT_BE_COPIED_TO_UPLOAD_DIRECTORY'));

					return false;
				}
			}
			else {
				$this->setError(MText::_('COM_MIWOVIDEOS_ERROR_EXTENSION_NOT_ALLOWED'));

				return false;
			}
			$this->_id       = $row->id;
			$this->_title    = $row->title;
			$this->_filename = $filename;

			//@TODO Add the file to files database

			// Update source field
			$model = MiwoVideos::get('controller')->getModel('videos');
			MRequest::setVar('cid', $row->id, 'post');
			MiwoVideos::get('controller')->updateField('videos', 'source', $filename, $model);
			$row->source = $filename;

			MiwoVideos::get('utility')->backgroundTask($row->id, $filename);

			$this->addProcesses($row);
		}

		return true;
	}

	public function remoteLink() {
		$user    = MFactory::getUser();
		$channel = MiwoVideos::get('channels')->getDefaultChannel();
		$date    = MFactory::getDate();
		$utility = MiwoVideos::get('utility');

		$data = MiwoVideos::getInput()->getString('remote_links', array(), 'post', 'array');
		$urls = explode("\n", $data);

		if (empty($urls)) {
			return false;
		}

		foreach ($urls as $url) {
			$host = null;
			$host = $utility->getHost($url);

			if (empty($host)) {
				$this->setError(MText::_('COM_MIWOVIDEOS_WRONG_LINK'));
				return false;
			}

			$plugin = MiwoVideos::getPlugin($host);

			if (!empty($plugin)) {
				$url         = $plugin->getCleanUrl($url);
				$plugin->url = $url;
				$plugin->getBuffer($url);
			}
			else {
				$this->setError(MText::sprintf('COM_MIWOVIDEOS_X_PLUGIN_DISABLED_NOT_INSTALLED', ucfirst($host)));
				return false;
			}

			MTable::addIncludePath(MPATH_WP_PLG.'/miwovideos/admin/tables');
			$row = MTable::getInstance('MiwovideosVideos', 'Table');

			$post = array();
			$acl  = MiwoVideos::get('acl');

			$item_id = MiwoVideos::getInput()->getInt('item_id', null);

			if (!empty($item_id) and $item_id > 0 and $acl->canEdit()) {
				$row->load($item_id);

				if (strpos($row->source, 'http://') === false) {
					$files       = MiwoVideos::get('files')->getVideoFiles($item_id);
					$files_model = MiwoVideos::get('controller')->getModel('files');

					foreach ($files as $dfile) {
						$ids[] = $dfile->id;
					}

					$files_model->delete($ids);
				}

				$post['user_id']    = $user->id;
				$post['channel_id'] = $channel->id;
				$post['source']     = $plugin->getSource();
				$post['duration']   = $plugin->getDuration();
				$post['thumb']      = $plugin->getThumbnail();
				$post['modified']   = $date->format('Y-m-d H:i:s');
			}
			else {
				$channel_id    = MiwoVideos::getInput()->getInt('channel_id', null);
				$channel_table = MiwoVideos::getTable('MiwovideosChannels');
				$channel_table->load($channel_id);

				if (!$channel_table->share_others) {
					$channel_id = MiwoVideos::get('channels')->getDefaultChannel()->id;
				}

				if (empty($channel_id)) {
					$channel_id = MiwoVideos::get('channels')->getDefaultChannel()->id;
				}

				$post['user_id']    = $user->id;
				$post['channel_id'] = $channel_id;
				$post['title']      = $plugin->getContent('title');
				$post['alias']      = MFilterOutput::stringURLSafe($post['title']);
				$post['introtext']  = $plugin->getContent('description');
				$post['source']     = $plugin->getSource();
				$post['duration']   = $plugin->getDuration();
				$post['thumb']      = $plugin->getThumbnail();
				$post['published']  = MiwoVideos::get('acl')->canAutoPublish() ? 1 : 0;
				$post['featured']   = 0;
				$post['access']     = 1;
				$post['created']    = $date->format('Y-m-d H:i:s');
				$post['modified']   = $date->format('Y-m-d H:i:s');
				$post['hits']       = 0;
				$post['language']   = '*';
			}

			// Bind it to the table
			if (!$row->bind($post)) {
				$this->setError($row->getError());
				return false;
			}

			// Store it in the db
			if (!$row->store()) {
				$this->setError($row->getError());
				return false;
			}

			$this->_id    = $row->id;
			$this->_title = $row->title;

			$this->_count++;
		}

		return true;
	}

	
	}
