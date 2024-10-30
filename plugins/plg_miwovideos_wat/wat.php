<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
// No direct access to this file
defined('MIWI') or die('Restricted access');

require_once(MPATH_WP_PLG.'/miwovideos/admin/library/remote.php');

class plgMiwovideosWat extends MiwovideosRemote {

    public function __construct(&$subject, $config) {
        parent::__construct($subject, $config);
    }

    public function getPlayer(&$output, $pluginParams, $item) {
        $this->width  = $this->params->get('width');
        $this->height = $this->params->get('height');

        $id = $this->parse($item->source, '');

        ob_start();
        ?>
		<iframe src="<?php echo $id; ?>" frameborder="0" style="width:<?php echo $this->width; ?>px; height: <?php echo $this->height; ?>px;"></iframe>
        <?php
        $output = ob_get_contents();
        ob_end_clean();
    }

    public function parse($url) {
        preg_match('#http://www.wat.tv/video/([A-Za-z0-9]+)#s', $url, $match);
        if (!empty($match[1])) {
            return $match[1];
        }

        return false;
    }
}