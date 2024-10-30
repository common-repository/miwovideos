<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die('Restricted Access');

# Imports
mimport('framework.installer.installer');
mimport('framework.installer.helper');
mimport('framework.filesystem.file');
mimport('framework.filesystem.folder');

# Model Class
class MiwovideosModelUpgrade extends MiwovideosModel {

	# Main constructer
	public function __construct() {
        parent::__construct('upgrade');
    }
    
	# Upgrade
    public function upgrade() {
        $utility = MiwoVideos::get('utility');

		# Get package
		$type = MiwoVideos::getInput()->getCmd('type');
		if ($type == 'upload') {
			$userfile = MRequest::getVar('install_package', null, 'files', 'array');
			$package = $utility->getPackageFromUpload($userfile);
		}
        else if ($type == 'server') {
			$package = $utility->getPackageFromServer('index.php?option=com_mijoextensions&view=download&model=miwovideos&pid='.$utility->getConfig()->get('pid'));
		}

		# Was the package unpacked?
		if (!$package or empty($package['dir'])) {
			MError::raiseWarning('SOME_ERROR_CODE', MText::_('Unable to find install package.'));
			return false;
		}

        
		# Miwi Framework
	    $src = $package['dir'].'/miwi';
        $dest = MPATH_WP_CNT.'/miwi';
        if (!MFolder::exists($dest)) {
            MFolder::copy($src, $dest);
            MFolder::delete($src);
        }
        elseif (MFolder::exists($src) and MFolder::exists($dest)) {
            require_once(MPATH_WP_PLG.'/miwovideos/miwovideos.php');
            $src_version  = MVideos::getMiwiVersion($src.'/versions.xml');
            $dest_version = MVideos::getMiwiVersion($dest.'/versions.xml');
            if (version_compare($src_version, $dest_version, 'gt')) {
                MFolder::copy($src, $dest, '', true);
                MFolder::delete($src);
            }
            else {
                MFolder::delete($src);
            }
        }
		
		if (!MFolder::exists(ABSPATH.'cgi-bin')) {
			MFolder::create(ABSPATH.'cgi-bin');
		}

		MFile::move(MPath::clean($package['dir'].'/admin/ubr_upload.pl'), MPath::clean(ABSPATH.'cgi-bin/ubr_upload.pl'));

		if (!MFolder::exists(MPATH_MIWI.'/cli')) {
			MFolder::create(MPATH_MIWI.'/cli');
		}

		MFile::move(MPath::clean($package['dir'].'/miwovideoscli.php'), MPath::clean(MPATH_MIWI.'/cli/miwovideoscli.php'));

	    if (MFolder::copy($package['dir'].'/languages', MPath::clean(MPATH_MIWI . '/languages'), null, true)) {
		    MFolder::delete($package['dir'].'/languages');
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
	    if (MFolder::copy($package['dir'].'/media', MPath::clean(MPATH_MEDIA.'/miwovideos'), null, true)) {
		    MFolder::delete($package['dir'].'/media');
	    }
	    if (MFolder::copy($package['dir'].'/modules', MPath::clean(MPATH_MIWI . '/modules'), null, true)) {
		    MFolder::delete($package['dir'].'/modules');
	    }
	    if (MFolder::copy($package['dir'].'/plugins', MPath::clean(MPATH_MIWI . '/plugins'), null, true)) {
		    MFolder::delete($package['dir'].'/plugins');
	    }

	    # MiwoVideos Plugin
	    MFolder::copy($package['dir'], MPath::clean(MPATH_WP_PLG.'/miwovideos'), null, true);
		MFolder::delete($package['dir']);

	    $script_file = MPATH_WP_PLG.'/miwovideos/script.php';
	    if (MFile::exists($script_file)) {
		    require_once($script_file);

		    $installer_class = 'com_MiwovideosInstallerScript';

		    $installer = new $installer_class();

		    if (method_exists($installer, 'preflight')) {
			    $installer->preflight(null, null);
		    }

		    if (method_exists($installer, 'postflight')) {
			    $installer->postflight(null, null);
		    }
	    }
		
		
		return true;
    }
}