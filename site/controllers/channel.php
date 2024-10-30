<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die ;

class MiwovideosControllerChannel extends MiwoVideosController {
	
	public function __construct($config = array()) {
		parent::__construct('channel');
	}	

    public function display($cachable = false, $urlparams = false) {
        $layout = MRequest::getCmd('layout');

        $function = 'display'.ucfirst($layout);

        $view = $this->getView(ucfirst($this->_context), 'html');
	    $channel_model = $this->getModel('channel');
	    $view->setModel($channel_model, true);
	    $playlists_model = $this->getModel('playlists');
	    $view->setModel($playlists_model);

        if (!empty($layout)) {
            $view->setLayout($layout);
        }

        $view->$function();
    }
}