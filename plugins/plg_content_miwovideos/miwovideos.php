<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die('Restricted access');

mimport('framework.plugin.plugin');

class plgContentMiwovideos extends MPlugin {

	public function __construct(&$subject, $params) {
		parent::__construct($subject, $params);
	}

	public function onContentPrepare($context, &$article, &$params, $limitstart) {
		if (MFactory::getApplication()->isAdmin()) {
			return true;
		}

        if (strpos($article->text, '{miwovideos id=') === false) {
            return true;
        }
		
		$regex = "#{miwovideos id=(\d+)}#s";
		
		$article->text = preg_replace_callback($regex, array(&$this, '_processMatches'), $article->text);
		
		return true;
	}

	public function _processMatches(&$matches) {
        require_once(MPATH_WP_PLG.'/miwovideos/admin/library/miwovideos.php');

        $old_option = MRequest::getCmd('option');
        $old_view = MRequest::getCmd('view');

		MRequest::setVar('option', 'com_miwovideos');
		MRequest::setVar('view', 'video');
		MRequest::setVar('video_id', $matches[1]);

		ob_start();

        require_once(MPATH_MIWOVIDEOS.'/controllers/video.php');
        require_once(MPATH_MIWOVIDEOS.'/models/video.php');
        require_once(MPATH_MIWOVIDEOS.'/models/playlists.php');
        require_once(MPATH_MIWOVIDEOS.'/views/video/view.html.php');

		$controller = new MiwovideosControllerVideo();
        $controller->_model = new MiwovideosModelVideo();
        $controller->_playlist_model = new MiwovideosModelPlaylists();

        $options['name'] = 'video';
        $options['layout'] = 'default';
        $options['base_path'] = MPATH_MIWOVIDEOS;
        $view = new MiwovideosViewVideo($options);

        $view->setModel($controller->_model, true);
        $view->setModel($controller->_playlist_model);

        //$view->setLayout('common');
        $view->display();

		$output = ob_get_contents();
		ob_end_clean();

        MRequest::setVar('option', $old_option);
        MRequest::setVar('view', $old_view);
		
		return $output;
	}
}