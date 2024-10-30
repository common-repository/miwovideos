<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die ('Restricted access');

require_once(MPATH_WP_PLG.'/miwovideos/admin/library/remote.php');

class plgMiwovideosYoutube extends MiwovideosRemote {

    public function __construct(&$subject, $config) {
        parent::__construct($subject, $config);
    }

    public function getPlayer(&$output, $pluginParams, $item) {

        $params = $this->params;

        $config = MiwoVideos::getConfig();
        $autoplay = $this->params->get('autoplay');
        if ($autoplay == "global") {
            $autoplay = $config->get('autoplay');
        }

        $this->width = '100%';
        $this->height = '100%';
        if ($config->get('video_height')) {
            $this->height = $config->get('video_height');
        } else {
            $this->height = 390; //$this->width*$config->get('video_aspect',0.75);
        }

        $id = $this->parse($item->source, '');

        // Pull parameters from the original Youtube url and transfer these to the iframe tag where appropriate
        $url = parse_url($item->source);
        parse_str($url['query'], $ytvars);
        if (isset($ytvars['cc_load_policy'])) $params->set('cc_load_policy', $ytvars['cc_load_policy']);
        if (isset($ytvars['cc_lang_pref'])) $params->set('cc_lang_pref', $ytvars['cc_lang_pref']);
        if (isset($ytvars['hl'])) $params->set('hl', $ytvars['hl']);

	    $plugin = MiwoVideos::getPlugin($config->get('video_player'));
	    if (empty($plugin)) {
		    $params->set('play_local', 0);
	    }

        if ($config->get('video_player') != 'flowplayer' and $params->get('play_local') == 1) {
            $pluginParams = new MRegistry();
            $pluginParams->loadString($plugin->params);
            $pluginParams->set('id', $id);

            $ret = $plugin->getPlayer($output, $pluginParams, $item);
	        if ($config->get('video_player') == 'videojs') {
		        MFactory::getDocument()->addScript(MURL_WP_CNT.'/miwi/plugins/plg_miwovideos_videojs/video-js/media.youtube.js');
	        }
            return $ret;
        } elseif (MRequest::getString('view') == 'video' and MRequest::getInt('playlist_id', 0) > 0) {
            $document = MFactory::getDocument();
            $document->addStyleSheet(MURL_MIWOVIDEOS.'/site/assets/css/playlist_youtube.css');
        }

        ob_start();
        ?>
        <iframe class="miwovideos_iframe_youtube" style="width: <?php echo $this->width; ?>; border: none;" height="<?php echo $this->height; ?>"
                src="<?php echo MUri::getInstance()->getScheme(); ?>://www.youtube.com/embed/<?php echo $id; ?>?wmode=opaque&amp;autoplay=<?php echo $autoplay; ?>&amp;autohide=<?php echo $params->get('autohide', 2); ?>&amp;border=<?php echo $params->get('border', 0); ?>&amp;cc_load_policy=<?php echo $params->get('cc_load_policy', 1); ?>&amp;cc_lang_pref=<?php echo $params->get('cc_lang_pref', 'en'); ?>&amp;hl=<?php echo $params->get('hl', 'en'); ?>&amp;color=<?php echo $params->get('color', 'red'); ?>&amp;color1=<?php echo $params->get('color1'); ?>&amp;color2=<?php echo $params->get('color2'); ?>&amp;controls=<?php echo $params->get('controls', 1); ?>&amp;fs=<?php echo $params->get('fs', 1); ?>&amp;hd=<?php echo $params->get('hd', 0); ?>&amp;iv_load_policy=<?php echo $params->get('iv_load_policy', 1); ?>&amp;modestbranding=<?php echo $params->get('modestbranding', 1); ?>&amp;rel=<?php echo $params->get('rel', 1); ?>&amp;theme=<?php echo $params->get('theme', 'dark'); ?>"
                allowfullscreen></iframe>
        <?php
        $output = ob_get_contents();
        ob_end_clean();
    }

    protected function parse($url, $return = 'embed', $width = '', $height = '', $rel = 0) {
        $urls = parse_url($url);

        // Url is http://youtu.be/xxxx
        if ($urls['host'] == 'youtu.be') {
            $id = ltrim($urls['path'], '/');
        } // Url is http://www.youtube.com/embed/xxxx
        else if (strpos($urls['path'], 'embed') == 1) {
            $id = end(explode('/', $urls['path']));
        } // Url is xxxx only
        else if (strpos($url, '/') === false) {
            $id = $url;
        } // http://www.youtube.com/watch?feature=player_embedded&v=m-t4pcO99gI
        // Url is http://www.youtube.com/watch?v=xxxx
        else {
            parse_str($urls['query'], $arrayVars);
            $id = $arrayVars['v'];
        }

        // Return embed iframe
        if ($return == 'embed') {
            return '<iframe width="'.($width ? $width : 560).'" height="'.($height ? $height : 349).'" src="http://www.youtube.com/embed/'.$id.'?rel='.$rel.'" frameborder="0" allowfullscreen></iframe>';
        } // Return normal thumb
        else if ($return == 'thumb') {
            return 'http://i1.ytimg.com/vi/'.$id.'/default.jpg';
        } // Return hqthumb
        else if ($return == 'hqthumb') {
            return 'http://i1.ytimg.com/vi/'.$id.'/hqdefault.jpg';
        } // Return id
        else {
            return $id;
        }
    }

    public function getDuration() {
        $duration = '';

        preg_match("/\"length_seconds\"\s*:\s*\"([0-9]*)\",/siU", $this->buffer, $match);

        if (!empty($match[1])) {
            $duration = (int)$match[1];
        }

        return $duration;
    }

    public function getThumbnail() {
        $thumbnail = $this->getContent('image');
        return $thumbnail;
    }

    public function getOgVideoTag($item) {
        // Youtube ID
        $id = $this->parse($item->source, '');

        return 'http://www.youtube.com/v/' . $id . '?version=3&amp;autohide=1';
    }



    public function getCleanUrl($url) {
        $pattern = '`.*?((http|https|ftp)://[\w#$&+,\/:;=?@.-]+)[^\w#$&+,\/:;=?@.-]*?`i';
        if (preg_match($pattern, $url, $matches)) {
            $url = $matches[1];
            $id = $this->parse($url, '');
            $url = 'http://www.youtube.com/watch?v='.$id;
        }

        return $url;
    }
}