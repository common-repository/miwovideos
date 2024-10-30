<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die ('Restricted access');

require_once(MPATH_WP_PLG.'/miwovideos/admin/library/remote.php');

class plgMiwovideosVideofoxnews extends MiwovideosRemote {

    public function __construct(&$subject, $config) {
        parent::__construct($subject, $config);
    }

    public function getPlayer(&$output, $pluginParams, $item) {
        $this->width  = $this->params->get('width');
        $this->height = $this->params->get('height');

        $id = $this->parse($item->source);

        ob_start();
        ?>
        <script type="text/javascript" src="http://video.foxnews.com/v/embed.js?id=<?php echo $id; ?>&w=<?php echo $this->width; ?>&h=<?php echo $this->height; ?>"></script>
		<noscript>Watch the latest video at <a href="http://video.foxnews.com">video.foxnews.com</a></noscript>
        <?php
        $output = ob_get_contents();
        ob_end_clean();
    }

    public function getDuration() {
        // TODO: return null
        $duration = false;

        preg_match('/<strong itemprop="duration" content="([^"]+)/', $this->buffer, $match);
        if (!empty($match[1])) {
            $duration = substr($match[1], 1, 4);
            $duration = str_replace("M", ":", $duration);
            echo $duration;
            exit;
        }

		$duration = (int) $duration;
		
		if ((int)$duration > 0) {
			return $duration;
		} else {
             return false;
        }
    }

	protected function parse($url) {
		$pos_u = strpos($url, "v/");
		$code = array();

		if ($pos_u === false) {
			return null;
		}
		else if ($pos_u) {
			$pos_u_start = $pos_u + 2;
			$pos_u_end = $pos_u_start + 13;

			$length = $pos_u_end - $pos_u_start;
			$code = substr($url, $pos_u_start, $length);
			$code = strip_tags($code);
			$code = preg_replace("/[^a-zA-Z0-9s]/", "", $code);
		}

		return $code;
	}
}