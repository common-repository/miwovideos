<?php
/**
 * @package        MiwoVideos
 * @copyright      Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license        GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die;


class MiwovideosViewUpload extends MiwovideosView {

	function display($tpl = null) {
		if ($this->_mainframe->isAdmin()) {
			$this->addToolbar();
		}

		$this->config = MiwoVideos::getConfig();

		if ($this->config->get('upload_script') == 'fancy') {
			$this->document->addScript(MURL_MIWOVIDEOS.'/site/assets/js/Swiff.Uploader.js');
			$this->document->addScript(MURL_MIWOVIDEOS.'/site/assets/js/Fx.ProgressBar.js');
			$this->document->addScript(MURL_MIWOVIDEOS.'/site/assets/js/FancyUpload2.js');
			$this->document->addStyleSheet(MURL_MIWOVIDEOS.'/site/assets/css/fancyupload.css');
		}

		if ($this->config->get('upload_script') == 'dropzone') {
			
			$this->document->addStyleSheet(MURL_MIWOVIDEOS.'/site/assets/css/dropzone.css');
		}

		if ($this->_mainframe->isAdmin()) {
					}

		parent::display($tpl);
	}

	protected function addToolbar() {
		if ($this->_view == 'videos') {
			return;
		}

		MToolBarHelper::title(MText::_('COM_MIWOVIDEOS_UPLOAD_NEW_VIDEO'), 'miwovideos');

		$this->toolbar->appendButton('Popup', 'help1', MText::_('Help'), 'http://miwisoft.com/support/docs/miwovideos/how-to/how-to-upload-video-files?tmpl=component', 650, 500);
	}

	protected function getRecursiveFolders($folder) {
		$this->folders_id = null;
		$txt              = null;
		if (isset($folder['children']) && count($folder['children'])) {
			$tmp        = $this->tree;
			$this->tree = $folder;
			$txt        = $this->loadTemplate('server');
			$this->tree = $tmp;
		}
		return $txt;
	}
}