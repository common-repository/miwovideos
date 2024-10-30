<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die ;

class MiwovideosControllerPlaylist extends MiwoVideosController {
	
	public function __construct($config = array()) {
		parent::__construct('playlist');
	}

    public function display($cachable = false, $urlparams = false) {
    $layout = MRequest::getCmd('layout');

    $function = 'display'.ucfirst($layout);

    $view = $this->getView(ucfirst($this->_context), 'html');
	$playlist_model = $this->getModel('playlist');
    $view->setModel($playlist_model, true);
    $video_model = $this->getModel('video');
    $view->setModel($video_model);

    if (!empty($layout)) {
        $view->setLayout($layout);
    }

    $view->$function();
}
}