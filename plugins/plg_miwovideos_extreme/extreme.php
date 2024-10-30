<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die ('Restricted access');

require_once(MPATH_WP_PLG.'/miwovideos/admin/library/remote.php');

class plgMiwovideosExtreme extends MiwovideosRemote {

    public function __construct(&$subject, $config) {
        parent::__construct($subject, $config);
    }

    public function getPlayer(&$output, $pluginParams, $item) {
        $this->width  = $this->params->get('width');
        $this->height = $this->params->get('height');

        $id = $this->parse($item->source, '');

        ob_start();
        ?>
        <div id="fcplayer_container" style="width:<?php echo $this->width; ?>px;margin:0 auto;"></div><script type="text/javascript" src="http://player.extreme.com/embed/<?php echo $id; ?>.js?width=<?php echo $this->width; ?>&amp;height=<?php echo $this->height; ?>"></script>
        <?php
        $output = ob_get_contents();
        ob_end_clean();
    }

    //TODO : Check getDuration method
    /*public function getDuration() {
        $duration = false;

        $code = $this->parse($this->url);
        $api_url = "http://media.freecaster.com/img/poster/video/140x79/1032/$code";
        $buffer = $this->getBuffer($api_url);

        if (!empty($buffer)) {
            preg_match('/" /></a><div class="video_duration">(.*)</', $this->buffer, $match);
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
    }*/

	public function parse($url)	{
		$buffer = $this->getBuffer($url);
		preg_match('/video_id":"([^"]+)/', $buffer, $match);
		if (!empty($match[1])) {
			return $match[1];
		}
		
		return false;
	}
}