<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die ('Restricted access');

require_once(MPATH_WP_PLG.'/miwovideos/admin/library/remote.php');

class plgMiwovideosVevo extends MiwovideosRemote {

    public function __construct(&$subject, $config) {
        parent::__construct($subject, $config);
    }

    public function getPlayer(&$output, $pluginParams, $item) {
        $this->width  = $this->params->get('width');
        $this->height = $this->params->get('height');

        $id = $this->parse($item->source);

        ob_start();
        ?>
        <object width="<?php echo $this->width; ?>" height="<?php echo $this->height; ?>">
			<param name="movie" value="http://videoplayer.vevo.com/embed/Embedded?videoId=GB1101200977&playlist=false&autoplay=0&playerId=62FF0A5C-0D9E-4AC1-AF04-1D9E97EE3961&playerType=embedded&env=0&cultureName=en-US&cultureIsRTL=False"></param>
			<param name="wmode" value="transparent"></param><param name="bgcolor" value="#000000"></param><param name="allowFullScreen" value="true"></param><param name="allowScriptAccess" value="always"></param>
			<embed src="http://videoplayer.vevo.com/embed/Embedded?videoId=<?php echo $id; ?>&playlist=false&autoplay=0&playerId=62FF0A5C-0D9E-4AC1-AF04-1D9E97EE3961 &playerType=embedded&env=0&cultureName=en-US&cultureIsRTL=False" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="<?php echo $this->width; ?>" height="<?php echo $this->height; ?>" bgcolor="#000000" wmode="transparent"></embed>
		</object>
        <?php
        $output = ob_get_contents();
        ob_end_clean();
    }

	protected function parse($url) {
		$pos_u = strpos($url, "watch/");
		$code = array();

		if ($pos_u === false) {
			return null;
		}
		else if ($pos_u) {
			$pos_start = $pos_u + 6;
			$length = strlen($url) - strlen($pos_start);
			$temp = substr($url, $pos_start, $length);

			$pos_v = strpos($temp, "/");
			$pos_start = $pos_v + 1;
			$length = strlen($temp) - strlen($pos_start);
			$temp2 = substr($temp, $pos_start, $length);

			$pos_w = strpos($temp2, "/");
			$pos_start = $pos_w + 1;
			$length = strlen($temp2) - strlen($pos_start);
			$code = substr($temp2, $pos_start, $length);

			$code = strip_tags($code);
			$code = preg_replace("/[^a-zA-Z0-9s]/", "", $code);
		}
		return $code;
	}
}