<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die ('Restricted access');

require_once(MPATH_WP_PLG.'/miwovideos/admin/library/remote.php');

class plgMiwovideosYouku extends MiwovideosRemote {

    public function __construct(&$subject, $config) {
        parent::__construct($subject, $config);
    }

    public function getPlayer(&$output, $pluginParams, $item) {
        $this->width  = $this->params->get('width');
        $this->height = $this->params->get('height');

        $id = $this->parse($item->source, '');

        ob_start();
        ?>
        <embed src="http://player.youku.com/player.php/sid/<?php echo $id; ?>/v.swf" quality="high" width="<?php echo $this->width; ?>" height="<?php echo $this->height; ?>" align="middle" allowScriptAccess="sameDomain" allowFullscreen="true" type="application/x-shockwave-flash"></embed>
        <?php
        $output = ob_get_contents();
        ob_end_clean();
    }

    public function getDuration() {
        $duration = false;

        $code = $this->parse($this->url);
        $api_url = "http://api.youku.com/api_ptvideoinfo/pid/XMTI5Mg==/id/$code/rt/xml";
        $buffer = $this->getBuffer($api_url);

        if (!empty($buffer)) {
            preg_match("/<duration>(.*)<\/duration>/siU", $buffer, $match);
            if (!empty($match[1])) {
                $ts = $match[1];
                if (count(explode(':', $ts)) == 1) {
                    list($secs) = explode(':', $ts);
                    $duration = $secs;
                }
                else if (count(explode(':', $ts)) == 2)	{
                    list($mins, $secs) = explode(':', $ts);
                    $duration = ($mins * 60) + $secs;
                }
                else if (count(explode(':', $ts)) == 3)	{
                    list($hours, $mins, $secs) = explode(':', $ts);
                    $duration = ($hours * 3600) + ($mins * 60) + $secs;
                }
            }
        }

        if ((int)$duration > 0) {
            return $duration;
        } else {
            return false;
        }
    }

	public function getThumbnail() {
        $thumbnail = false;
        $noHtmlFilter = MFilterInput::getInstance();

        $code = $this->parse($this->url);
        $api_url = "http://api.youku.com/api_ptvideoinfo/pid/XMTI5Mg==/id/$code/rt/xml";
        $buffer = $this->getBuffer($api_url);

        if (!empty($buffer)) {
            preg_match("/<imagelink_large>(.*)<\/imagelink_large>/siU", $buffer, $match);

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
		$pos_u = strpos($url, "id_");
		$code = array();

		if ($pos_u === false) {
			return null;
		}
		else if ($pos_u) {
			$pos_u_start = $pos_u + 3;
			$pos_u_end = $pos_u_start + 13;

			$length = $pos_u_end - $pos_u_start;
			$code = substr($url, $pos_u_start, $length);                       
			$code = strip_tags($code);
			$code = preg_replace("/[^a-zA-Z0-9s]/", "", $code);
		}

		return $code; 
	}
}