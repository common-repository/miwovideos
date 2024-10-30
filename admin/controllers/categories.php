<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die ;

class MiwovideosControllerCategories extends MiwoVideosController {

	public function __construct($config = array()) {
		parent::__construct('categories');
	}

	public function save() {
		$this->trigger();
		parent::save();
	}

	public function publish() {
		$this->trigger(1);
		parent::publish();
	}

	public function unpublish() {
		$this->trigger(0);
		parent::unpublish();
	}

	protected function trigger($status = null) {
		if (is_null($status)) {
			$post = MRequest::get('post', MREQUEST_ALLOWRAW);
			$status = $post['published'];
		}
		$cid = MRequest::getVar('cid', array(), 'post');
		MiwoVideos::get('utility')->trigger('onFinderChangeState', array('com_miwovideos.categories', $cid[0], $status), 'finder');
	}
}