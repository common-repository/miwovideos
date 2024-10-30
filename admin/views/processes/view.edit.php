<?php
/**
 * @package        MiwoVideos
 * @copyright      Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license        GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die('Restricted Access');

class MiwovideosViewProcesses extends MiwovideosView {

	public function display($tpl = null) {
		$this->logs   = $this->get('logs');
		$this->item   = $this->getModel()->getProcess();

		if ($this->_mainframe->isAdmin()) {
			$this->addToolBar();
		}

		parent::display($tpl);
	}

	protected function addToolbar() {
		MToolBarHelper::title(MText::_('COM_MIWOVIDEOS_CPANEL_PROCESS_LOG') , 'miwovideos' );
		MToolBarHelper::cancel();
	}
}