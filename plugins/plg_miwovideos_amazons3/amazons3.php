<?php
/**
 * @package        MiwoVideos
 * @copyright      Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license        GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die('Restricted access');

require_once(MPATH_WP_PLG.'/miwovideos/admin/library/cdn.php');

class plgMiwovideosAmazons3 extends MiwovideosCdn {

	public $utility;
	public $config;
	public $storage;
	public $bucket;
	public $s3;
	public $bucketName;
	public $endpoint;

	public function __construct(&$subject, $config) {
		parent::__construct($subject, $config);

		if (MRequest::getString('option') != 'com_miwovideos' or MRequest::getString('task') != 'cdn') {
			return;
		}

		MLoader::register('S3', MPATH_MIWI.'/plugins/plg_miwovideos_amazons3/assets/S3.php');
		MTable::addIncludePath(MPATH_WP_PLG.'/miwovideos/admin/tables');
		$this->utility = MiwoVideos::get('utility');
		$this->config  = MiwoVideos::getConfig();

		// AWS access info
		$this->bucketName  = $this->params->get('awsBucket', '');
		$awsAccessKey      = $this->params->get('awsAccessKey', '');
		$awsSecretKey      = $this->params->get('awsSecretKey', '');
		$reducedRedundancy = $this->params->get('awsRrs', 0);
		$location          = $this->params->get('awsRegion', 'us-west-1');
		$this->endpoint    = 's3' . $location . '.amazonaws.com';

		// Windows curl extension has trouble with SSL connections, so we won't use it
		if (substr(PHP_OS, 0, 3) == "WIN") {
			$useSSL = 0;
		}
		else {
			$useSSL = 1;
		}

		//marpada-E

		// Check for CURL
		if (!extension_loaded('curl')) {
			exit("ERROR: CURL extension not loaded");
		}

		// Pointless without your keys!
		if ($awsAccessKey == '' || $awsSecretKey == '') {
			exit("ERROR: AWS access information required");
		}

		// Instantiate the class
		$this->s3 = new S3($awsAccessKey, $awsSecretKey, $useSSL, $this->endpoint);

		if ($reducedRedundancy) {
			$this->storage = S3::STORAGE_CLASS_RRS;
		}
		else {
			$this->storage = S3::STORAGE_CLASS_STANDARD;
		}

		//Check if bucket exists and if it belongs to the default region
		$bucketlocation = $this->s3->getBucketLocation($this->bucketName);
		if (($bucketlocation) && ($bucketlocation <> $location)) {
			echo "Bucket already exist in ".$bucketlocation." region";
			$location = $bucketlocation;
			switch ($location) {
				case "us-west-1":
					$this->s3->setEndpoint('s3-us-west-1.amazonaws.com');
					break;
				case "EU":
					$this->s3->setEndpoint('s3-eu-west-1.amazonaws.com');
					break;
				case "ap-southeast-1":
					$this->s3->setEndpoint('s3-ap-southeast-1.amazonaws.com');
					break;
				case "ap-northeast-1":
					$this->s3->setEndpoint('s3-ap-northeast-1.amazonaws.com');
					break;
				default:
					$this->s3->setEndpoint('s3.amazonaws.com');
			}
		}

		// Create a bucket with public read access
		$this->s3->putBucket($this->bucketName, S3::ACL_PUBLIC_READ, $location);

		// Get the contents of our bucket
		$this->bucket = $this->s3->getBucket($this->bucketName);
	}

	public function maintenance() {
		if(empty($this->s3)) {
			return;
		}
		// Get local queue
		$queued = $this->getLocalQueue();
		echo "About to process ".count($queued)." video items<br />".PHP_EOL;
		foreach ($queued as $video) {
			$errors = false;
			$db     = MFactory::getDBO();

			// Select queued processes from the table.
			$query = 'SELECT COUNT(*) FROM #__miwovideos_processes WHERE video_id = '.$video->id.' AND (status = 0) AND (published = 1)';

			$db->setQuery($query);
			$queuedProcesses = $db->loadResult();
			if ($queuedProcesses > 0) {
				echo "[ID:".$video->id."] Video has queued processes which need to be completed or deleted before the transfer of this video item</strong><br />".PHP_EOL;
				$errors = true;
				continue;
			}

			// Get files for local media
			$files = MiwoVideos::get('files')->getVideoFiles($video->id);
			if (count($files) == 0) {
				echo "[ID:".$video->id."] Video has no files</strong><br />".PHP_EOL;
				$errors = true;
				continue;
			}

			foreach ($files as $file) {
				if ($file->process_type == 100) {
					if ($file->ext == 'jpg') {
						$size = MiwoVideos::get('utility')->getThumbSize($this->config->get('thumb_size'));
					}
					else {
						$size = $this->utility->getVideoSize($video->id, $video->source);
					}
				}
				else {
					$size = MiwoVideos::get('processes')->getTypeSize($file->process_type);
				}

				if ($file->process_type == 200) {
					$relativePath = $this->utility->getVideoFilePath($video->id, 'orig', $video->source);
				}
				else {
					if ($file->ext == 'jpg' or $file->ext == 'thumb') {
						$relativePath = $this->utility->getThumbPath($file->video_id, 'videos', $file->source, $size, 'default');
					}
					else {
						$relativePath = $this->utility->getVideoFilePath($file->video_id, $size, $file->source);
					}
				}

				if (!$this->transferFiles($video->id, ltrim($relativePath, '/'))) {
					$errors = true;
					continue;
				}

				if (!$errors) {
					MRequest::setVar('cid', $file->id, 'post');
					$source = "http://".$this->bucketName.".".$this->endpoint.$relativePath;
					if (!MiwoVideos::get('controller')->updateField('files', 'source', $source, null)) {
						MError::raiseWarning(500, 'Error when updating file source');
					}
				}
			}

			# Put frame files
			$frame_files = MFolder::files(MIWOVIDEOS_UPLOAD_DIR.'/images/videos/'.$video->id.'/frames');
			foreach ($frame_files as $frame_file) {
				if (!$this->transferFiles($video->id, 'wp-content/uploads/miwovideos/images/videos/'.$video->id.'/frames/'.$frame_file)) {
					$errors = true;
					continue;
				}
			}

			// If no errors, and all local files exist on CDN then modify database so media
			// if switched to CDN, delete all local files
			if (!$errors) {
				echo "[ID:".$video->id."] Updating database </br>".PHP_EOL;
				$file_path  = $this->utility->getVideoFilePath($video->id, 'orig', $video->source);
				$thumb_size = $this->utility->getThumbSize($this->config->get('thumb_size'));
				$thumb_path = '/media/com_miwovideos/images/videos/'.$video->id.'/'.$thumb_size.'/'.$video->thumb;

				MRequest::setVar('cid', $video->id, 'post');
				$source = "http://".$this->bucketName.".".$this->endpoint.$file_path;
				$thumb  = "http://".$this->bucketName.".".$this->endpoint.$thumb_path;

				if (!MiwoVideos::get('controller')->updateField('videos', 'source', $source, null)) {
					MError::raiseWarning(500, 'Error when updating video source');
				}

				if (!MiwoVideos::get('controller')->updateField('videos', 'thumb', $thumb, null)) {
					MError::raiseError(500, 'Error when updating video thumbnail');
				}

				echo "[ID:".$video->id."] Database updated </br>".PHP_EOL;

				if (!MFolder::delete(MIWOVIDEOS_UPLOAD_DIR.'/videos/'.$video->id) or !MFolder::delete(MIWOVIDEOS_UPLOAD_DIR.'/images/videos/'.$video->id)) {
					MError::raiseWarning(500, "File delete error");
				}
				else {
					echo "[ID:".$video->id."] Delete local file </br>".PHP_EOL;
				}

			}
		}
	}

	private function transferFiles($id, $relativePath) {
		$path = MPATH_ROOT.'/'.$relativePath;
		// Check local file exists
		if (!file_exists($path)) {
			echo "[ID:".$id."] Source File [<strong>$path</strong>] does not exist so skipping</strong><br />".PHP_EOL;
			return false;
		}

		// If more than 500mb, we might struggle to transfer this in the timeout...
		if (filesize($path) > 524288000) {
			echo "[ID:".$id."] Source File [<strong>$path</strong>] is larger than 0.5GB so skipping</strong><br />".PHP_EOL;
			return false;
		}

		if (isset($this->bucket[ $relativePath ])) {
			if ($this->bucket[ $relativePath ]['size'] == filesize($path)) {
				echo "[ID:".$id."] File [<strong>$relativePath</strong>] already exists in <strong>{$this->bucketName}</strong><br />".PHP_EOL;
				return true;
			}
			else {
				echo "[ID:".$id."] File [<strong>$relativePath</strong>] must be updated <strong>{$this->bucketName}</strong><br />".PHP_EOL;
				if (@$this->s3->putObject($this->s3->inputFile($path, false), $this->bucketName, $relativePath, S3::ACL_PUBLIC_READ, array(), array(), $this->storage)) {
					echo "[ID:".$id."] S3::putObject(): File copied to {$this->bucketName}/".$relativePath."</br>".PHP_EOL;
					return true;
				}
				else {
					echo "[ID:".$id."] S3::putObject(): Failed to copy file </br>".PHP_EOL;
					return false;
				}
			}
		}
		else {
			// Put our file (also with public read access)
			if ($this->s3->putObject($this->s3->inputFile($path, false), $this->bucketName, $relativePath, S3::ACL_PUBLIC_READ, array(), array(), $this->storage)) {
				echo "[ID:".$id."] S3::putObject(): File copied to {$this->bucketName}/".$relativePath."</br>".PHP_EOL;
				return true;
			}
			else {
				echo "[ID:".$id."] S3::putObject(): Failed to copy file </br>".PHP_EOL;
				return false;
			}
		}


	}
}