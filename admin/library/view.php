<?php
/**
 * @package        MiwoVideos
 * @copyright      Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license        GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die('Restricted Access');

# Imports
mimport('framework.application.component.view');

if (!class_exists('MiwisoftView')) {
	if (interface_exists('MView')) {
		abstract class MiwisoftView extends MViewLegacy {}
	}
	else {
		class MiwisoftView extends MView {}
	}
}

class MiwovideosView extends MiwisoftView {

	public $toolbar;
	public $document;

	public function __construct($config = array()) {
		parent::__construct($config);

		$this->_mainframe = MFactory::getApplication();
		if ($this->_mainframe->isAdmin()) {
			$this->_option = MiwoVideos::get('utility')->findOption();
		}
		else {
			$this->_option = MiwoVideos::getInput()->getCmd('option');
		}

		$this->_view = MiwoVideos::getInput()->getCmd('view');

		# Get toolbar object
		if ($this->_mainframe->isAdmin()) {
			$this->toolbar = MToolBar::getInstance();
		}

		$this->document = MFactory::getDocument();

		$tmpl = $this->_mainframe->getTemplate();

		# Template CSS
		if (file_exists(MPATH_WP_CNT.'/themes/'.$tmpl.'/html/com_miwovideos/assets/css/stylesheet.css') and !MiwoVideos::isDashboard()) {
			$this->document->addStyleSheet(MURL_WP_CNT.'/themes/'.$tmpl.'/html/com_miwovideos/assets/css/stylesheet.css');
			if (MiwoVideos::is30()) {
				MHtml::_('jquery.framework');
			}
			$color  = MiwoVideos::getConfig()->get('override_color');
			$color1 = '#'.dechex(hexdec($color) - 1379346);
			$color2 = '#'.dechex(hexdec($color) + 654582);
			$color3 = '#'.dechex(hexdec($color) + 1967870);
			$css    = '.subscribe {
                        background-color:'.$color.' !important;
                        background-image: -webkit-gradient(linear, 0 0, 0 100%, from('.$color1.'), to('.$color2.'));
                        background-image: -webkit-linear-gradient(to top, '.$color1.' 0%, '.$color2.' 100%);
                        background-image: -moz-linear-gradient(to top, '.$color1.' 0%, '.$color2.' 100%);
                        background-image: -o-linear-gradient(to top, '.$color1.' 0%, '.$color2.' 100%);
                        background-image: linear-gradient(to top, '.$color1.' 0%, '.$color2.' 100%);
                    }
                    .subscribe:hover{
                        background         : '.$color2.' !important; // Dailymotion
                        background-image: -webkit-gradient(linear, 0 0, 0 100%, from('.$color.'), to('.$color3.'));
                        background-image: -webkit-linear-gradient(to top, '.$color.' 0%, '.$color3.' 100%);
                        background-image: -moz-linear-gradient(to top, '.$color.' 0%, '.$color3.' 100%);
                        background-image: -o-linear-gradient(to top, '.$color.' 0%, '.$color3.' 100%);
                        background-image: linear-gradient(to top, '.$color.' 0%, '.$color3.' 100%);
                    }
                    .follow_button { // Dailymotion
                        border-color: #52882F;
                        background: #6CB23E;
                    }
                    .play_all_pp {
                        /*background : #6CB23E !important;*/
                    }
                    .miwovideos_flow_select_cp .toggled span {

                    }
                    .miwovideos_tabs li.active {
                        border-bottom:3px solid '.$color.';
                    }
                    .miwovideos_tabs li:hover{
                        border-bottom:3px solid '.$color.';
                    }
                    .miwovideos_box .nav-tabs li.active{
                        border-bottom:3px solid '.$color.';
                    }
                    .vjs-default-skin .vjs-volume-level {
                        background: '.$color.' !important;
                    }

                    .vjs-default-skin .vjs-play-progress {
                        background: '.$color.' !important;
                    }';
			$this->document->addStyleDeclaration($css);

		}
		# Component CSS
		else {
			if ($this->_mainframe->isAdmin()) {
				$this->document->addStyleSheet(MURL_MIWOVIDEOS.'/admin/assets/css/miwovideos.css');
			}
			else {
				$this->document->addStyleSheet(MURL_MIWOVIDEOS.'/site/assets/css/miwovideos.css');
			}
		}

		if (MiwoVideos::is30()) {
			if ($this->_mainframe->isAdmin()) {
				$this->document->addStyleSheet(MURL_MIWOVIDEOS.'/admin/assets/css/j3.css');
				MHtml::_('formbehavior.chosen', 'select');
			}
			else {
				$this->document->addStyleSheet(MURL_MIWOVIDEOS.'/site/assets/css/j3.css');
			}
		}
		else {
			if ($this->_mainframe->isAdmin()) {
				$this->document->addStyleSheet(MURL_MIWOVIDEOS.'/admin/assets/css/j2.css');
				$this->document->addStyleSheet(MURL_MIWOVIDEOS.'/admin/assets/css/table.css');
			}
			else {
				$this->document->addStyleSheet(MURL_MIWOVIDEOS.'/site/assets/css/j2.css');
			}
		}

		if (MiwoVideos::isDashboard()) {
			$this->document->addScript(MURL_MIWOVIDEOS.'/site/assets/js/adminform.js');
		}

		if ($this->_mainframe->isSite()) {
			// Load first jQuery lib
			if (MiwoVideos::is30()) {
				MHtml::_('jquery.framework');
				MHtml::_('behavior.framework');
			}
			$this->document->addScript(MURL_MIWOVIDEOS.'/site/assets/js/thumbnail.js');
			if (file_exists(MPATH_WP_CNT.'/themes/'.$tmpl.'/html/com_miwovideos/assets/js/watchlater.js') and !MiwoVideos::isDashboard()) {
				$this->document->addScript(MURL_WP_CNT.'/themes/'.$tmpl.'/html/com_miwovideos/assets/js/watchlater.js');
			}
			else {
				$this->document->addScript(MURL_MIWOVIDEOS.'/site/assets/js/watchlater.js');
			}

			$this->document->setBase(MUri::root());
		}

		$this->acl    = MiwoVideos::get('acl');
		$this->config = MiwoVideos::getConfig();
		$this->Itemid = MiwoVideos::getInput()->getInt('Itemid', 0);
	}

	public function getIcon($i, $task, $img, $check_acl = false) {
		if ($check_acl and !$this->acl->canEditState()) {
			$html = '<img src="'.MURL_MIWOVIDEOS.'/admin/assets/images/'.$img.'" border="0" />';
		}
		else {
			$html = '<a href="javascript:void(0);" onclick="return listItemTask(\'cb'.$i.'\',\''.$task.'\')">';
			$html .= '<img src="'.MURL_MIWOVIDEOS.'/admin/assets/images/'.$img.'" border="0" />';
			$html .= '</a>';
		}

		return $html;
	}

	public function loadForeignTemplate($view, $layout = 'default', $function = 'display') {
		$type = 'html';

		$task  = MiwoVideos::getInput()->getCmd('task', '');
		$tasks = array('add', 'edit', 'apply', 'save2new');
		if (in_array($task, $tasks) /* and ($view != 'upload') and ($view != 'files')*/) {
			//$type = 'edit';
		}

		$location = MPATH_MIWOVIDEOS_ADMIN;
		if ($this->_mainframe->isSite()) {
			$location = MPATH_MIWOVIDEOS;
		}

		$path = $location.'/views/'.$view.'/view.'.$type.'.php';

		if (file_exists($path)) {
			require_once $path;
		}
		else {
			return null;
		}

		$view_name  = 'MiwovideosView'.ucfirst($view);
		$model_name = 'MiwovideosModel'.ucfirst($view);

		$options['name']      = $view;
		$options['layout']    = $layout;
		$options['base_path'] = $location;

		$_view = new $view_name($options);

		$model_file = $location.'/models/'.$view.'.php';
		if (file_exists($model_file)) {
			require_once($model_file);

			$model = new $model_name();

			$_view->setModel($model, true);
		}

		if (MiwoVideos::is30()) {
			MHtml::_('formbehavior.chosen', 'select');
		}

		$tpl = null;
		if ($layout != 'default') {
			$tpl = $layout;
		}

		ob_start();

		$_view->$function($tpl);

		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}

	public function display($tpl = null) {
		$is_dashboard = MiwoVideos::isDashboard();

		if ($is_dashboard) {
			$result = $this->loadDashboardTemplate($tpl);

			if ($result instanceof Exception) {
				return $result;
			}

			echo $result;
		}
		else {
			parent::display($tpl);
		}
	}

	public function loadDashboardTemplate($tpl = null) {
		$this->_output = null;

		$view = $this->_view;
		if ($view == 'dashboard') {
			$view = 'miwovideos';
		}

		$layout = $this->getLayout();

		// Create the template file name based on the layout
		$file = isset($tpl) ? $layout.'_'.$tpl : $layout;
		$file = preg_replace('/[^A-Z0-9_\.-]/i', '', $file);

		$template_path = MPATH_MIWOVIDEOS_ADMIN.'/views/'.$view.'/tmpl/'.$file.'.php';

		if (file_exists($template_path)) {
			ob_start();

			include($template_path);

			$this->_output = ob_get_contents();
			ob_end_clean();

			return $this->_output;
		}
		else {
			throw new Exception(MText::sprintf('MLIB_APPLICATION_ERROR_LAYOUTFILE_NOT_FOUND', $file), 500);
		}
	}
}