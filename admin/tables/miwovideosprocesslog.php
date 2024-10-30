<?php
/**
 * @package        MiwoVideos
 * @copyright      Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license        GNU General Public License version 2 or later
 */
// No direct access to this file
defined('MIWI') or die('Restricted Access');

class TableMiwovideosProcessLog extends MTable {

	public $id              = 0;
	public $process_id      = 0;
	public $video_id        = 0;
	public $input           = null;
	public $output          = null;
	public $created         = null;
	public $created_user_id = 0;

	public function __construct($db) {
		parent::__construct('#__miwovideos_process_log', 'id', $db);
	}
}
