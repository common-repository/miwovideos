<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
// No direct access to this file
defined('MIWI') or die('Restricted access');

require_once(MPATH_WP_PLG.'/miwovideos/admin/library/remote.php');

class plgMiwovideosBlip extends MiwovideosRemote {

    public function __construct(&$subject, $config) {
        parent::__construct($subject, $config);
    }

    public function getPlayer(&$output, $pluginParams, $item) {
        $this->width  = $this->params->get('width');
        $this->height = $this->params->get('height');

        $id = null;
		$code = $this->parse($item->source);
        $api_url = "http://blip.tv/rss/view/$code";
		$buffer = $this->getBuffer($api_url, '');
		
		if (!empty($buffer)){
			preg_match("/<blip:embedLookup>(.*)<\/blip:embedLookup>/siU", $buffer, $match);                        
			if (!empty($match[1])){
				$id = $match[1];
				$id = preg_replace("/[^a-zA-Z0-9-_+]/", "", $id);
			}
		}

        ob_start();
        ?>
        <iframe src="http://blip.tv/play/<?php echo $id; ?>.html?p=1" width="<?php echo $this->width; ?>" height="<?php echo $this->height; ?>" frameborder="0" allowfullscreen></iframe><embed type="application/x-shockwave-flash" src="http://a.blip.tv/api.swf#<?php echo $id; ?>" style="display:none"></embed>
        <?php
        $output = ob_get_contents();
        ob_end_clean();
    }

	public function getThumbnail() {
        $thumbnail = false;
        $noHtmlFilter = MFilterInput::getInstance();

        $code = $this->parse($this->url);
        $api_url = "http://blip.tv/rss/view/$code";
        $buffer = $this->getBuffer($api_url);

        if (!empty($buffer)) {
            preg_match("/<blip:picture>(.*)<\/blip:picture>/siU", $buffer, $match);

            if (!empty($match[1])) {
                $thumbnail = $match[1];
                $thumbnail = str_replace("<![CDATA[", "", $thumbnail);
                $thumbnail = str_replace("]]>", "", $thumbnail);
                $thumbnail = (string)str_replace(array("\r", "\r\n", "\n"), '', $thumbnail);
                $thumbnail = $noHtmlFilter->clean($thumbnail);
                $thumbnail = MHtmlString::truncate($thumbnail, 255);
                $thumbnail = trim($thumbnail);
            }
        }
		
		$thumbnail = trim(strip_tags($thumbnail));
        $isValid = preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $thumbnail);
		
		if ($isValid) {
			return $thumbnail;
		} else {
            return false;
        }
    }

	protected function parse($url) {
		$code = substr($url, -7);
		$code = preg_replace("/[^0-9]/", "", $code);

		if (!empty($code)) return $code;

		return null;
	}
}