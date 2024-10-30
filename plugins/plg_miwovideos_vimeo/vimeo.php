<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die('Restricted access');

require_once(MPATH_WP_PLG.'/miwovideos/admin/library/remote.php');

class plgMiwovideosVimeo extends MiwovideosRemote {

    public function __construct(&$subject, $config) {
        parent::__construct($subject, $config);
    }

    public function getPlayer(&$output, $pluginParams, $item) {
        if (MRequest::getString('view') == 'video' and MRequest::getInt('playlist_id', 0) > 0) {
            $document = MFactory::getDocument();
            $document->addStyleSheet(MURL_MIWOVIDEOS.'/site/assets/css/playlist_vimeo.css');
        }

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
        <iframe
            src="<?php echo MUri::getInstance()->getScheme(); ?>://player.vimeo.com/video/<?php echo $id; ?>?title=0&amp;autoplay=<?php echo $autoplay; ?>&amp;byline=0&amp;portrait=0"
            width="<?php echo $this->width; ?>" height="<?php echo $this->height; ?>" frameborder="0"
            webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
        <?php
        $output = ob_get_contents();
        ob_end_clean();
    }

    public function getDuration() {
        $duration = false;
        $code    = $this->parse($this->url);
        $api_url = "http://vimeo.com/api/v2/video/$code.xml";

        $buffer = $this->getBuffer($api_url);

        if (!empty($buffer)) {
            $data = new SimpleXmlElement($buffer, LIBXML_NOCDATA);

            $duration = $data->video->duration->__toString();
        }

        if ($duration > 0) {
            return $duration;
        }
    }

    public function getThumbnail() {
        $thumbnail = false;
        $code    = $this->parse($this->url);
        $api_url = "http://vimeo.com/api/v2/video/$code.xml";

        $buffer = $this->getBuffer($api_url);

        if (!empty($buffer)) {
            $pos_thumb_search = strpos($buffer, "thumbnail_medium");

            if ($pos_thumb_search === false) {
                return null;
            } else {
                $pos_thumb_start = strpos($buffer, "http", $pos_thumb_search);
                $pos_thumb_end   = strpos($buffer, '.jpg', $pos_thumb_start);
                if ($pos_thumb_end === false) {
                    return null;
                } else {
                    $length    = $pos_thumb_end + 4 - $pos_thumb_start;
                    $thumbnail = substr($buffer, $pos_thumb_start, $length);
                    $thumbnail = strip_tags($thumbnail);
                }
            }
        }

        $thumbnail = trim(strip_tags($thumbnail));
        $isValid   = preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $thumbnail);

        if ($isValid) {

            return $thumbnail;
        }

    }

    protected function parse($url) {
        $url = str_replace("https", "http", $url);

        if (preg_match('~^http://(?:www\.)?vimeo\.com/(?:clip:)?(\d+)~', $url, $match)) {
            if (!empty($match[1])) return $match[1];
        }

        return null;
    }
}