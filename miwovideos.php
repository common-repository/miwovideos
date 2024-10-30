<?php
/*
Plugin Name: MiwoVideos
Plugin URI: http://miwisoft.com
Description: MiwoVideos allows you to turn your site into a professional looking video-sharing website with features similar to YouTube.
Author: Miwisoft
Version: 1.3.0
Author URI: http://miwisoft.com/wordpress-plugins/miwovideos-share-your-videos
*/

defined('ABSPATH') or die('MIWI');

if (!class_exists('MWordpress')) {
    require_once(dirname(__FILE__) . '/wordpress.php');
}

final class MVideos extends MWordpress {

    public function __construct() {
	    if (!defined('MURL_MIWOVIDEOS')) {
		    define('MURL_MIWOVIDEOS', plugins_url('', __FILE__));
	    }

	    if (!defined('MIWOVIDEOS_PACK')) {
		    define('MIWOVIDEOS_PACK', 'lite');
	    }

        parent::__construct('miwovideos', '33.0002');
    }

	public function initialise() {
		$miwi = MPATH_WP_CNT.'/miwi/initialise.php';

		if (!file_exists($miwi)) {
			return false;
		}

		require_once($miwi);

		$this->app = MFactory::getApplication();

		$this->app->initialise();

		# auto upgrade
		mimport('joomla.application.component.helper');
		$config = MComponentHelper::getParams('com_'.$this->context);

		if(!empty($config) and file_exists(MPATH_WP_CNT.'/miwi/autoupdate.php')) {
			$pid = $config->get('pid');
			if(!empty($pid)) {
				$path = 'http://miwisoft.com/index.php?option=com_mijoextensions&view=download&pack=upgrade&model=' . $this->context.'&pid=' . $pid;
				require_once(MPATH_WP_CNT.'/miwi/autoupdate.php');
				new MiwisoftAutoUpdate($path, $this->context);
			}
		}

		require_once(MPATH_WP_PLG.'/miwovideos/admin/library/miwovideos.php');
		$user_plg = MiwoVideos::getPlugin('miwovideos', 'user');

		add_action('profile_update', array($user_plg, 'OnUserAfterSave'), 10, 2);
		add_action('user_register', array($user_plg, 'OnUserAfterSave'));
		add_action('deleted_user', array($user_plg, 'OnUserAfterDelete'), 10, 2);
	}
}

$mvideos = new MVideos();

register_activation_hook(__FILE__, array($mvideos, 'activate'));
register_deactivation_hook(__FILE__, array($mvideos, 'deactivate'));