<?php
/**
 * @package        MiwoVideos
 * @copyright      Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license        GNU General Public License version 2 or later
 */

define('MCLI', true);
define('MPATH_BASE', dirname(dirname(__FILE__)));

require_once MPATH_BASE.'/framework/environment/request.php';
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/wp-load.php';
require_once MPATH_BASE . '/initialise.php';
define('MPATH_COMPONENT', MPATH_WP_PLG . '/miwovideos/admin');
define('MPATH_COMPONENT_SITE', MPATH_WP_PLG.'/miwovideos/site');
define('MPATH_COMPONENT_ADMINISTRATOR', MPATH_WP_PLG.'/miwovideos/admin');
mimport('framework.environment.request');
MRequest::setVar('option', 'com_miwovideos', 'GET');
$_SERVER['REQUEST_METHOD'] = 'GET';

// Instantiate the application.
$app = MFactory::getApplication('administrator');

mimport('framework.application.cli');
mimport('framework.database.database');
mimport('framework.database.table');
mimport('framework.database.table.extension');
mimport('framework.filesystem.file');
mimport('framework.filesystem.folder');

class MiwovideosCli {
	public function __construct() {
		MLoader::register('MApplication', MPATH_MIWI.'/framework/application/application.php');
		MLoader::register('MApplicationHelper', MPATH_MIWI.'/framework/application/helper.php');
		MLoader::register('MControllerLegacy', MPATH_MIWI.'/framework/application/component/controller.php');
		MLoader::register('MControllerLegacy', MPATH_MIWI.'/framework/application/component/model.php');
		MLoader::register('MControllerLegacy', MPATH_MIWI.'/framework/application/component/view.php');
		MLoader::register('MComponentHelper', MPATH_MIWI.'/framework/application/component/helper.php');
		MFactory::getApplication('administrator');
		MLoader::register('MiwoVideos', MPATH_WP_PLG.'/miwovideos/admin/library/miwovideos.php');	}

	public function process() {

		// Load process object
		$process_lib = MiwoVideos::get('processes');

		$args      = $GLOBALS['argv'];
		$processes = array_slice($args, 1);
		if (count($processes) > 1) {
			foreach ($processes as $process) {
				$process = (int)$process;
				if ($process > 0) {
					$process_lib->run($process);
				}

			}
		}
		else {
			for ($i = 1; $i <= 50; $i++) {
				$process_lib->run();
			}
		}
	}

	public function cdn() {
		$config      = MiwoVideos::getConfig();
		$cdn = MiwoVideos::getPlugin($config->get('cdn', 'amazons3'));
		if (empty($cdn)) {
			return false;
		}

		return $cdn->maintenance();

	}

	public function convertToHtml5() {
		$args   = $GLOBALS['argv'];
		$upload = MiwoVideos::get('videos');
		$upload->convertToHtml5($args[2], $args[3]);
	}

	public function test() {
		$filename = MPATH_SITE . '/tmp/com_miwovideos.background';
		$buffer   = '';
		MFile::write($filename, $buffer);
	}

	public function execute() {
		$args = $GLOBALS['argv'];
		if ($args[1] == 'test') {
			$this->test();
		}
		else if ($args[1] == 'cdn') {
			$this->cdn();
		}
		else if ($args[1] == 'process') {
			$this->process();
		}
		else if ($args[1] == 'convertToHtml5') {
			$this->convertToHtml5();
		}
	}
}

$cli = new MiwovideosCli();
$cli->execute();