<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die('Restricted Access');

# Imports
mimport('framework.filesystem.file');

class MiwovideosModelRestoreMigrate extends MiwovideosModel {

	public function __construct() {
		parent::__construct('restoremigrate');
	}

    public function backup() {
		list($query, $filename, $fields, $line) = $this->_getBackupVars();
		
		$ret = MiwoVideos::get('utility')->backupDB($query, $filename, $fields, $line);

		return $ret;
    }

    public function restore() {
		# Get the uploaded file
		if (!$file = $this->_getUploadedFile()) {
			return false;
		}

		# Load SQL
		$lines = file($file);

		$result = true;
		for ($i = 0, $n = count($lines); $i < $n; $i++) {
			# Trim line
			$line = trim($lines[$i]);
			
			list($preg, $line) = $this->_getRestorePregLine($line);
			
			# Ignore empty lines
			if (strlen($line) == 0 || empty($line) || $line == '') {
				continue;
			}

			# If the query continues at the next line.
			while (substr($line, -1) != ';' && $i + 1 < count($lines)) {
				$i++;
				$newLine = trim($lines[$i]);
				
				if (strlen($newLine) == 0) {
					continue;
				}
				
				$line .= ' '.$lines[$i];
			}

			if (preg_match($preg, $line) > 0) {
				$this->_db->setQuery($line);
				if (!$this->_db->query()) {
					MError::raiseWarning( 100, MText::_('Error importing line').': '.$line.'<br />'.$this->_db->getErrorMsg());
					$result = false;
				}
			} else {
				MError::raiseWarning(100, MText::_('Ignoring line').': '.$line);
			}
		}

		MFile::delete($file);
		
		return $result;
    }

    public function migrate() {
        $ret = false;

        Miwovideos::get('utility')->import('library.backuprestore');

        $items = array('WordpressVideoGalleryCats', 'WordpressVideoGalleryVideos',
                        'CoolVideoGalleryCats', 'CoolVideoGalleryVideos',
                        'WPGCoolGalleryCats', 'WPGCoolGalleryVideos',
                        'AllVideoGalleryCats', 'AllVideoGalleryVideos',
                        'HDWPlayerCats', 'HDWPlayerVideos',
                        'YendifPlayerCats', 'YendifPlayerVideos');

        foreach ($items as $item) {
            if (MiwoVideos::getInput()->getCmd('migrate_'.$item, 0, 'post')) {
                $class = new MiwovideosBackupRestore(array('_table' => $item, '_where' => ''));
                $function = 'migrate' . ucfirst($item);

                $ret = $class->$function();

                return $ret;
            }
        }

        return $ret;
    }

    public function _getBackupVars() {
        Miwovideos::get('utility')->import('library.backuprestore');

        $items = array('categories', 'channels', 'playlists', 'playlistvideos', 'videos', 'videocategories', 'subscriptions', 'likes', 'reports', 'files', 'reasons');

        foreach ($items as $item) {
            if (MiwoVideos::getInput()->getCmd('backup_'.$item, 0, 'post')) {
                $class = new MiwovideosBackupRestore(array('_table' => $item, '_where' => ''));
                $function = 'backup' . ucfirst($item);

                list($query, $filename, $fields, $line) = $class->$function();

                return array($query, $filename, $fields, $line);
            }
        }
    }

    public function _getRestorePregLine($line) {
		Miwovideos::get('utility')->import('library.backuprestore');
		
		$items = array('categories', 'channels', 'playlists', 'playlistvideos', 'videos',  'videocategories', 'subscriptions', 'likes', 'reports', 'files', 'reasons');

		foreach ($items as $item) {
			if (MiwoVideos::getInput()->getCmd('restore_'.$item, 0, 'post')) {
				$class = new MiwovideosBackupRestore();
				$function = 'restore' . ucfirst($item);
				
				list($preg, $line) = $class->$function($line);
				
				return array($preg, $line);
			}
		}
	}

    public function _getUploadedFile () {
		$userfile = MiwoVideos::getInput()->getVar('file_restore', null, 'files', 'array');

		# Make sure that file uploads are enabled in php
		if (!(bool) ini_get('file_uploads')) {
			MError::raiseWarning(100, MText::_('WARNINSTALLFILE'));
			return false;
		}

		# Make sure that zlib is loaded so that the package can be unpacked
		if (!extension_loaded('zlib')) {
			MError::raiseWarning(100, MText::_('WARNINSTALLZLIB'));
			return false;
		}

		# If there is no uploaded file, we have a problem...
		if (!is_array($userfile) ) {
			MError::raiseWarning(100, MText::_('No file selected'));
			return false;
		}

		# Check if there was a problem uploading the file.
		if ( $userfile['error'] || $userfile['size'] < 1 ) {
			MError::raiseWarning(100, MText::_('WARNINSTALLUPLOADERROR'));
			return false;
		}

		# Build the appropriate paths
		$config = MFactory::getConfig();
		$tmp_dest = $config->get('tmp_path').'/'.$userfile['name'];
		$tmp_src  = $userfile['tmp_name'];

		# Move uploaded file
		mimport('framework.filesystem.file');
		$uploaded = MFile::upload($tmp_src, $tmp_dest);
		
		if (!$uploaded) {
			MError::raiseWarning('SOME_ERROR_CODE', '<br /><br />' . MText::_('File not uploaded, please, make sure the "Global Configuration => Server => Path to Temp-folder" is valid.') . '<br /><br /><br />');
			return false;
		}
		
		return $tmp_dest;
	}
}
