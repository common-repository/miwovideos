<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die ('Restricted access');

require_once(MPATH_WP_PLG.'/miwovideos/admin/library/remote.php');

class plgMiwovideosVeoh extends MiwovideosRemote {

    public function __construct(&$subject, $config) {
        parent::__construct($subject, $config);
    }

    public function getPlayer(&$output, $pluginParams, $item) {
        $this->width  = $this->params->get('width');
        $this->height = $this->params->get('height');

        $id = $this->parse($item->source);

        ob_start();
        ?>
		<object width="<?php echo $this->width; ?>" height="<?php echo $this->height; ?>" id="veohFlashPlayer" name="veohFlashPlayer">
			<param name="movie" value="http://www.veoh.com/swf/webplayer/WebPlayer.swf?version=AFrontend.5.7.0.1390&permalinkId=v20520636wmzkjdnx&player=videodetailsembedded&videoAutoPlay=0&id=anonymous"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param>
			<embed src="http://www.veoh.com/swf/webplayer/WebPlayer.swf?version=AFrontend.5.7.0.1390&permalinkId=<?php echo $id; ?>&player=videodetailsembedded&videoAutoPlay=0&id=anonymous" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="<?php echo $this->width; ?>" height="<?php echo $this->height; ?>" id="veohFlashPlayerEmbed" name="veohFlashPlayerEmbed"></embed>
		</object>
		<?php
        $output = ob_get_contents();
        ob_end_clean();
    }

    public function getDuration() {
        $duration = false;

        preg_match('/<meta name="item-duration" content="([^"]+)/', $this->buffer, $match);
        if (!empty($match[1])) {
            $duration = (int) $match[1];
        }

        if ($duration > 0) {
            return $duration;
        } else {
            return false;
        }
    }

    public function getThumbnail() {
        $thumbnail = false;
        $noHtmlFilter = MFilterInput::getInstance();

        if (!empty($this->buffer)) {
            preg_match('/<meta name="og:image" content="([^"]+)/', $this->buffer, $match);

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
		$pos_u = strpos($url, "watch/");
		$code = array();

		if ($pos_u === false) {
			return null;
		}
		else if ($pos_u) {
			$pos_u_start = $pos_u + 6;
			$pos_u_end = $pos_u_start + 17;
			
			$length = $pos_u_end - $pos_u_start;
			$code = substr($url, $pos_u_start, $length);                       
			$code = strip_tags($code);
			$code = preg_replace("/[^a-zA-Z0-9s]/", "", $code);
		}
		
		return $code; 
	}
}