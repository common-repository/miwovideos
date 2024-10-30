<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die ('Restricted access');

require_once(MPATH_WP_PLG.'/miwovideos/admin/library/remote.php');

class plgMiwovideosVine extends MiwovideosRemote {

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
        <iframe class="vine-embed" src="https://vine.co/v/<?php echo $id ?>/embed/simple?audio=<?php echo $autoplay; ?>"
                width="<?php echo $this->width; ?>" height="<?php echo $this->height; ?>" frameborder="0"></iframe>
        <?php

        $output = ob_get_contents();
        ob_end_clean();
    }

    protected function parse($url) {
        $pos_u = strpos($url, "v/");
        $code  = array();

        if ($pos_u === false) {
            return null;
        } else if ($pos_u) {
            $pos_u_start = $pos_u + 2;
            $pos_u_end   = $pos_u_start + 11;

            $length = $pos_u_end - $pos_u_start;
            $code   = substr($url, $pos_u_start, $length);
            $code   = strip_tags($code);
            $code   = preg_replace("/[^a-zA-Z0-9s]/", "", $code);
        }

        return $code;
    }
}