<?php
/**
 * @package        MiwoVideos
 * @copyright      Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license        GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die;

class MiwovideosControllerVideo extends MiwoVideosController {

	public function __construct($config = array()) {
		parent::__construct('video');

		$id       = MiwoVideos::getInput()->getInt('id');
		$video_id = MiwoVideos::getInput()->getInt('video_id');

		if (empty($video_id) and !empty($id)) {
			$this->_mainframe->redirect(MRoute::_('index.php?option=com_miwovideos&view=video&video_id='.$id));
		}
	}

	public function display($cachable = false, $urlparams = false) {
		$layout = MRequest::getCmd('layout');

		$function = 'display'.ucfirst($layout);

		$view        = $this->getView(ucfirst($this->_context), 'html');
		$video_model = $this->getModel('video');
		$view->setModel($video_model, true);
		$playlists_model = $this->getModel('playlists');
		$view->setModel($playlists_model);

		if (!empty($layout)) {
			$view->setLayout($layout);
		}

		$view->$function();
	}

	public function submitReport() {
		$post = MRequest::get('post', MREQUEST_ALLOWRAW);
		$user = MFactory::getUser();
		$json = array();
		if ($user->id != 0) {
			if ($this->_model->submitReport($post)) {
				$html[]          = '<div class="miwovideos_report_success">'.MText::_('COM_MIWOVIDEOS_SUCCESS_REPORT').'</div>';
				$html[]          = '<div class="miwovideos_report_text">';
				$html[]          = '     <div style="font-weight: bold">'.MText::_('COM_MIWOVIDEOS_ISSUE_REPORTED').'</div>';
				$html[]          = '     <p id="miwovideos_reasons"></p>';
				$html[]          = '     <div style="font-weight: bold">'.MText::_('COM_MIWOVIDEOS_ADDITIONAL_DETAILS').'</div>';
				$html[]          = '     <p>'.$post['miwovideos_report'].'</p>';
				$html[]          = '</div>';
				$json['success'] = implode("\n", $html);
			}
			else {
				$json['error'] = true;
			}
		}
		else {
			$json['redirect'] = MiwoVideos::get('utility')->redirectWithReturn();
		}
		echo json_encode($json);
		exit();

	}

	public function getVideoFile() {
		MiwoVideos::get('videos')->getVideoFile();
		return;
	}
}