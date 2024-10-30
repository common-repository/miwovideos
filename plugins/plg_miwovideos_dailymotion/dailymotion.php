<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die ('Restricted access');

require_once(MPATH_WP_PLG.'/miwovideos/admin/library/remote.php');

class plgMiwovideosDailymotion extends MiwovideosRemote {

    public function __construct(&$subject, $config) {
        parent::__construct($subject, $config);
    }

    public function getPlayer(&$output, $pluginParams, $item) {
        $config = MiwoVideos::getConfig();
        $autoplay = $this->params->get('autoplay');
        if ($autoplay == "global") {
            $autoplay = $config->get('autoplay');
        }

        $this->width  = $this->params->get('width');
        $this->height = $this->params->get('height');

        $id = $this->parse($item->source, '');

        ob_start();
        ?>
        <iframe frameborder="0" width="<?php echo $this->width; ?>" height="<?php echo $this->height; ?>"
                src="http://www.dailymotion.com/embed/video/<?php echo $id; ?>?logo=0&amp;autoPlay=<?php echo $autoplay; ?>"></iframe>
        <?php
        $output = ob_get_contents();
        ob_end_clean();
    }

    public function getThumbnail() {
        $code = $this->parse($this->url);
        $thumbnail = 'http://www.dailymotion.com/thumbnail/video/' . $code;

        return $thumbnail;

    }

    protected function parse($url) {
        preg_match('#http://www.dailymotion.com/video/([A-Za-z0-9]+)#s', $url, $matches);
        if (isset($matches[1])) {
            return $matches[1];
        }

        return false;
    }
}