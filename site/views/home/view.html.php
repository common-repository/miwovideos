<?php
/**
 * @package        MiwoVideos
 * @copyright      Copyright  ( C ) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license        GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die;

class MiwovideosViewHome extends MiwovideosView {

	public $widget_top    = '';
	public $widget_left   = '';
	public $widget_right  = '';
	public $widget_bottom = '';

	public function display($tpl = null) {
		$this->params  = $this->_mainframe->getParams();
		$this->widgets = array();
		add_filter('widget_display_callback', array($this, 'isInHome'));

		$modules  = MModuleHelper::getModules();
		$renderer = MFactory::getDocument()->loadRenderer('module');

		foreach ($modules as $module) {
			$params = new MRegistry();
			$params->loadString($module->params);
			ob_start();
			echo $renderer->render($module);
			switch ($params->get('position')) {
				case 1:
					$this->widget_top = ob_get_contents();
					break;
				case 2:
					$this->widget_left = ob_get_contents();
					break;
				case 3:
					$this->widget_right = ob_get_contents();
					break;
				case 4:
					$this->widget_bottom = ob_get_contents();
					break;
			}
			ob_end_clean();

		}

		parent::display($tpl);
	}

	public function isInHome($instance) {
		if (isset($instance['position']) and $instance['position'] != 0) {
			return false;
		}
	}
}
