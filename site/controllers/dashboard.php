<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die ;

class MiwovideosControllerDashboard extends MiwovideosController {
	
	public function __construct($config = array()) {
        $_lang = MFactory::getLanguage();
        $_lang->load('com_miwovideos', MPATH_ADMINISTRATOR, 'en-GB', true);
        $_lang->load('com_miwovideos', MPATH_ADMINISTRATOR, $_lang->getDefault(), true);
        $_lang->load('com_miwovideos', MPATH_ADMINISTRATOR, null, true);

        $view = MiwoVideos::getInput()->getCmd('view');

        if (file_exists((MPATH_MIWOVIDEOS_ADMIN.'/controllers/'.$view.'.php'))) {
            require_once(MPATH_MIWOVIDEOS_ADMIN.'/controllers/'.$view.'.php');

            $class_name = 'MiwovideosController'.ucfirst($view);

            $this->_admin_controller = new $class_name();

            $model_file = MPATH_MIWOVIDEOS_ADMIN.'/models/'.$view.'.php';
            if (file_exists($model_file)) {
                require_once($model_file);

                $model_class_name = 'MiwovideosModel'.ucfirst($view);

                $this->_admin_controller->_model = new $model_class_name();
            }
        }

        if ($view == 'dashboard') {
            $view = 'miwovideos';
        }

		parent::__construct($view);

        if (MFactory::getUser()->get('id') == 0) {
            $this->_mainframe->redirect(MiwoVideos::get('utility')->redirectWithReturn());
        }
    }

    public function display($cachable = false, $urlparams = false) {
	    if ($this->_task == 'back') {
		    $post = MRequest::get('post', MREQUEST_ALLOWRAW);
		    $cid  = MRequest::getVar('cid', array(), 'post');

		    if (!empty($cid[0])) {
			    $post['id'] = $cid[0];
		    }

		    $post['Itemid'] = MiwoVideos::getInput()->getInt('Itemid', 0);
		    $link           = MRoute::_('index.php?option='.$this->_option.'&view=dashboard&dashboard=1&Itemid='.$post['Itemid']);
		    $link           = str_replace('&amp;', '&', $link);

		    parent::setRedirect($link);
	    }
        $view = $this->getDashboardView();
        if (!is_object($view)) {
            return;
        }

        /*$layout = MiwoVideos::getInput()->getCmd('layout', '');

        if (!empty($layout)) {
            $view->setLayout($layout);
        }*/

        $view->display();
    }

    public function edit() {
        MRequest::setVar('hidemainmenu', 1);

        $view = $this->getDashboardView();
        if (!is_object($view)) {
            return;
        }

	    $this->_admin_controller->edit();
        //$view->display('edit');
    }

    public function support() {
        $view = $this->getDashboardView();
        if (!is_object($view)) {
            return;
        }

        $view->setLayout('support');

        $view->display();
    }

    public function translators() {
        $view = $this->getDashboardView();
        if (!is_object($view)) {
            return;
        }

        $view->setLayout('translators');

        $view->display();
    }

    public function defaultChannel() {
        MRequest::checkToken() or mexit('Invalid Token');

        MiwoVideos::get('channels')->updateDefaultChannel(1);

        parent::route();
    }

    public function notDefaultChannel() {
        MRequest::checkToken() or mexit('Invalid Token');

        MiwoVideos::get('channels')->updateDefaultChannel(0);

        parent::route();
    }

    public function upload() {
        $this->frontUpload('upload');
    }

    public function uberUpload() {
        $this->frontUpload('uberUpload');
    }

    public function remoteLink() {
        $this->frontUpload('remoteLink');
    }

    public function serverImport() {
        $this->frontUpload('serverImport');
    }

	public function embedCode() {
		$this->frontUpload('embedCode');
	}

    public function frontUpload($function = 'upload') {
        $upload = $this->_admin_controller->$function();

        if ($function == 'upload') {
            return;
        }

        $dashboard = '';
        if (MiwoVideos::isDashboard()) {
            $dashboard = '&dashboard=1';
        }

        if ($upload->_count > 1) {
            $this->setRedirect('index.php?option=com_miwovideos&view=videos'.$dashboard);
        }
        else {
            $this->setRedirect('index.php?option=com_miwovideos&view=videos&task=edit&cid[]=' . $upload->_id.$dashboard);
        }
    }

    public function convertToHtml5() {
        $this->_admin_controller->convertToHtml5();
    }

    public function link_upload() {
        $this->_admin_controller->link_upload();
    }

    public function set_progress() {
        $this->_admin_controller->set_progress();
    }

    public function get_progress() {
        $this->_admin_controller->get_progress();
    }

    public function save() {
        $msg = $this->_admin_controller->save();

        parent::routeDashboard($msg);
    }

    public function copy() {
        $msg = $this->_admin_controller->copy();

        parent::routeDashboard($msg);
    }

    public function delete() {
        $msg = $this->_admin_controller->delete();

        parent::routeDashboard($msg);
    }
}