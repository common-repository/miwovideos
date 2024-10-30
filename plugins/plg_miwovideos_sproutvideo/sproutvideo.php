<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die ('Restricted access');

require_once(MPATH_WP_PLG.'/miwovideos/admin/library/remote.php');

class plgMiwovideosSproutvideo extends MiwovideosRemote {

    public function __construct(&$subject, $config) {
        parent::__construct($subject, $config);
    }

    public function getPlayer(&$output, $pluginParams, $item) {
        $this->width  = $this->params->get('width');
        $this->height = $this->params->get('height');

        $this->getBuffer($item->source);
        $url = $this->getEmbedUrl($item->source);

        ob_start();
        ?>
        <iframe class='sproutvideo-player' type='text/html' frameborder="0" width="<?php echo $this->width; ?>" height="<?php echo $this->height; ?>" src="<?php echo $url; ?>"></iframe>
        <?php
        $output = ob_get_contents();
        ob_end_clean();
    }

    public function getContent($type = 'title') {
        $noHtmlFilter = MFilterInput::getInstance();

        switch ($type) {
            case 'title':
                $pattern = '/<meta property = "og:title" content = "([^"]+)/';
                break;
            case 'description':
                $pattern = '/<meta property = "og:description" content = \'([^\']+)/';
                break;
            case 'image':
                $pattern = '/<meta property = "og:image" content = "([^"]+)/';
                break;
            default:
                $pattern = '';
                break;
        }
        preg_match($pattern, $this->buffer, $match);


        if (!empty($match[1])) {
            $content = $match[1];
            $content = (string)str_replace(array("\r", "\r\n", "\n"), '', $content);
            $content = $noHtmlFilter->clean($content);
            $content = trim($content);
            if ($content) {
                return $content;
            }
        }

        return MText::_('COM_MIWOVIDEOS_UNNAMED');
    }

    public function getDuration() {
        $duration = false;

        $embed_url = $this->getEmbedUrl();
        $buffer = $this->getBuffer($embed_url);

        if (!empty($buffer)) {
            preg_match('/duration = ([^,]+)/', $buffer, $match);
            if (!empty($match[1])) {
                $duration = $match[1];
            }
        }

        $this->getBuffer($this->url);

        if ((int)$duration > 0) {
            return $duration;
        } else {
            return false;
        }
    }

    protected function getEmbedUrl() {
        $pattern = "/<meta name='twitter:player' content='([^']+)/";

        preg_match($pattern, $this->buffer, $match);

        if (!empty($match[1])) {
            $url = $match[1];
            if ($url) {
                return $url;
            }
        }

        return false;
    }
}