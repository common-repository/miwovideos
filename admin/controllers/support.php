<?php
/*
* @package		MiwoVideos
* @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
* @license		GNU General Public License version 2 or later
*/
# No Permission
defined('MIWI') or die ('Restricted access');

# Controller Class
class MiwovideosControllerSupport extends MiwovideosController {

	# Main constructer
    public function __construct() {
        parent::__construct('support');
    }
	
	# Support page
    public function support() {
        $view = $this->getView(ucfirst($this->_context), 'html');
        $view->setLayout('support');
        $view->display();
    }
    
	# Translators page
    public function translators() {
        $view = $this->getView(ucfirst($this->_context), 'html');
        $view->setLayout('translators');
        $view->display();
    }
}