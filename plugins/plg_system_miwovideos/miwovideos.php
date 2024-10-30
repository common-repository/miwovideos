<?php
/**
 * @package        MiwoVideos
 * @copyright      Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license        GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die('Restricted access');

class plgSystemMiwovideos extends MPlugin {

	public function onAfterRoute() {
		$db       = MFactory::getDBO();
		$app      = MFactory::getApplication();
		$document = MFactory::getDocument();

		
            $ui_folder = MURL_WP_CNT.'/miwi/media/jui';

            if ($app->isAdmin() or MiwoVideos::isDashboard()) {
                $document->addScript($ui_folder.'/js/jquery-ui-1.10.4.custom.min.js');
                $document->addStyleSheet($ui_folder.'/css/jquery-ui-1.10.4.custom.min.css');

				

				MRequest::setVar('task', MRequest::getCmd('task', MRequest::getCmd('task'), 'post'));
            }
		if ($app->isSite()) {
			$this->_checkRedirection();

			return true;
		}
	}

	public function _loadJquery() {
		$document = MFactory::getDocument();
		$option   = MRequest::getCmd('option');

		if ($option != 'com_miwovideos') {
			return false;
		}

		if (version_compare(MVERSION, '3.0.0', 'ge')) {
			return false;
		}

		if ($document->getType() != 'html') {
			return false;
		}

		if (!file_exists(MPATH_WP_PLG.'/miwovideos/admin/library/miwovideos.php')) {
			return false;
		}

		return true;
	}

	public function _checkRedirection() {
		$app = MFactory::getApplication();

		$miwovideos = MPATH_WP_PLG.'/miwovideos/admin/library/miwovideos.php';
		if (!file_exists($miwovideos)) {
			return;
		}

		require_once($miwovideos);

		$plugin = MPluginHelper::getPlugin('system', 'miwovideos');
		$params = new MRegistry($plugin->params);

		$option = MRequest::getCmd('option');

		$link = '';

		if (!empty($option)) {
			switch ($option) {
				case 'com_jvideos':
					if ($params->get('redirect_jvideos', '0') == '1') {
						$link = self::_getJvideosLink();
					}
					break;
				default:
					return true;
			}
		}

		if (empty($link)) {
			return true;
		}

		$Itemid = MRequest::getInt('Itemid', '');
		$lang   = MRequest::getWord('lang', '');

		if (!empty($Itemid)) {
			$Itemid = '&Itemid='.$Itemid;
		}

		if (!empty($lang)) {
			$lang = '&lang='.$lang;
		}

		$url = MRoute::_('index.php?option=com_miwovideos&'.$link.$Itemid.$lang);

		$app->redirect($url, '', 'message', true);
	}
}
