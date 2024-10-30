<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die ;

class MiwovideosControllerPlaylists extends MiwoVideosController {
	
	public function __construct($config = array()) {
		parent::__construct('playlists');
		$this->utility = MiwoVideos::get('utility');
	}

    public function save() {
        $date = MFactory::getDate();
        $user_id = MFactory::getUser()->id;
        MRequest::setVar('cid', 0, 'post');
        MRequest::setVar('created', $date->format('Y-m-d H:i:s'), 'post');
        $json = array();
        if ($user_id) {
            $result = parent::save();
            $insertid = MiwoDB::insertid();
            MiwoVideos::get('utility')->trigger('onMiwovideosAfterSaveItem', array($insertid, 'playlists', 'created'));
            if (!$result) {
                $json['error'] = MText::_($result);
            } else {
                $json['success'] = 1;
                $json['id'] = $insertid;
            }
        } else {
            $json['redirect'] = $this->utility->redirectWithReturn();
        }
        echo json_encode($json);
        exit();
    }

    public function addVideoToPlaylist() {
        $user_id = MFactory::getUser()->id;
	    if (!$playlist_id = MRequest::getInt('playlist_id')) {
		    $playlist_id = $this->utility->getWatchlater()->id;
	    }
        $video_id = MRequest::getInt('video_id');
        $ordering = MRequest::getWord('ordering');
        if ($user_id) {
            $result = $this->utility->checkVideoInPlaylists($playlist_id, $video_id);
            if (!empty($result)) {
                $json['error'] = MText::_('COM_MIWOVIDEOS_ALREADY_ADDED');
                echo json_encode($json);
                exit();
            }
            $result = $this->_model->addVideoToPlaylist($playlist_id, $video_id, $ordering);
            if (!$result) {
                $json['error'] = 1;
            } else {
                $json['success'] = MText::_('COM_MIWOVIDEOS_ADDED_TO_PLAYLIST');
            }
        } else {
            $json['redirect'] = $this->utility->redirectWithReturn();
        }
        echo json_encode($json);
        exit();
    }

    public function removeVideoFromPlaylist() {
        $user_id = MFactory::getUser()->id;
	    if (!$playlist_id = MRequest::getInt('playlist_id')) {
		    $playlist_id = $this->utility->getWatchlater()->id;
	    }
        $video_id = MRequest::getInt('video_id');
        if ($user_id) {
            $result = $this->utility->checkVideoInPlaylists($playlist_id, $video_id);
            if (empty($result)) {
                $json['error'] = MText::_('COM_MIWOVIDEOS_ALREADY_REMOVED');
                echo json_encode($json);
                exit();
            }
            $result = $this->_model->removeVideoToPlaylist($playlist_id, $video_id);
            if (!$result) {
                $json['error'] = 1;
            } else {
                $json['success'] = MText::_('COM_MIWOVIDEOS_REMOVED_FROM_PLAYLIST');
            }
        } else {
            $json['redirect'] = $this->utility->redirectWithReturn();
        }
        echo json_encode($json);
        exit();
    }
}