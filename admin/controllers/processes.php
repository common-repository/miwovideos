<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die ;

class MiwovideosControllerProcesses extends MiwoVideosController {
	
	public function __construct($config = array()) {
		parent::__construct('processes');
	}
	
	public function process() {
		# Check token
		MRequest::checkToken() or mexit('Invalid Token');
		
		$cid = MRequest::getVar('cid', array(), 'post');
		$ret = false;

		foreach ($cid as $id) {
			$exists = $this->_model->getSuccessful($id);
			if ($exists) {
				$ids[] = $id;
				continue;
			}
            $ret = MiwoVideos::get('processes')->run($id);
		}

		if (isset($ids) and count($ids) > 0) {
			MError::raiseNotice(100, MText::sprintf('COM_MIWOVIDEOS_ALREADY_PROCESSED', implode(',', $ids)));
		}

        if ($ret) {
            $this->_mainframe->enqueueMessage(MText::_('COM_MIWOVIDEOS_RECORD_PROCESSED'));
        } else {
            MError::raiseError(100, MText::_('COM_MIWOVIDEOS_PROCESS_FAILED'));
        }

        $this->setRedirect('index.php?option='.$this->_option.'&view='.$this->_context);
		
	}
	
	public function processAll() {
		$this->process();
	}

	public function processForce() {
		$processes = MRequest::getVar('cid', array(), 'post');
		$this->_model->changeProcessStatus($processes, 3); // Change status to Processing
		$cli    = MPATH_MIWI.'/cli/miwovideoscli.php';
		$output = '';
		if (substr(PHP_OS, 0, 3) != "WIN") {
			// @TODO Log if throw an error
			$command = "env -i ".MiwoVideos::getConfig()->get('php_path', '/usr/bin/php')." $cli process ".implode(" ", $processes)." > /dev/null 2>&1 &";
		}
		else {
			MiwoVideos::exec('where php.exe', $php_path);
			// @TODO Log if throw an error
			$command = MiwoVideos::getConfig()->get('php_path', $php_path)." $cli process ".implode(" ", $processes)." NUL";
		}

		MiwoVideos::exec($command, $output, $error);
		MiwoVideos::log('CLI : '.$command);
		MiwoVideos::log($output);
		MiwoVideos::log($error);

		if ($error == false) {
			$this->_mainframe->enqueueMessage(MText::_('COM_MIWOVIDEOS_START_BACKGROUND_PROCESS'));
		} else {
			MError::raiseError(100, MText::_('COM_MIWOVIDEOS_PROCESS_FAILED'));
		}

		$this->setRedirect('index.php?option='.$this->_option.'&view='.$this->_context);
	}
}