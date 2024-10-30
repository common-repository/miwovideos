<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die ('Restricted access');

mimport('framework.plugin.plugin');
require_once(MPATH_WP_PLG.'/miwovideos/admin/library/miwovideos.php');

class plgMiwovideosVideoJs extends MPlugin {

    public function __construct(&$subject, $config) {
        parent::__construct($subject, $config);
        $this->config = MiwoVideos::getConfig();
    }

	public function getPlayer(&$output, $pluginParams, $item) {
        if (strpos($output, '{miwovideos ') === false) {
            return false;
        } else {
	        $output .= '}';
        }

        $this->output = $output;
        $this->pluginParams = $pluginParams;
        $this->item = $item;

        $output = preg_replace_callback('#{miwovideos\s*(.*?)}#s', array(&$this, '_processMatches'), $output);

		$input = MiwoVideos::getInput();
		$document = MFactory::getDocument();

		$document->addStyleSheet(MURL_WP_CNT.'/miwi/plugins/plg_miwovideos_videojs/video-js/video-js.css');
		$document->addScript(MURL_WP_CNT.'/miwi/plugins/plg_miwovideos_videojs/video-js/video.dev.js');

		#Video Plugins
        if ($item->duration) {
            $document->addStyleSheet(MURL_WP_CNT.'/miwi/plugins/plg_miwovideos_videojs/video-js/videojs.plugins.css');
            $document->addScript(MURL_WP_CNT.'/miwi/plugins/plg_miwovideos_videojs/video-js/videojs.plugins.js');
        }
		if ($input->getCmd('view') == 'video' and $input->getInt('playlist_id', 0) > 0) {
			$document->addStyleSheet(MURL_MIWOVIDEOS.'/site/assets/css/playlist_videojs.css');
		}

		$document->addStyleDeclaration('
		.videoWrapper {
			position: relative;
			padding-top: 0px;
			height: 0px;
			z-index: 3;
			/*overflow: hidden;*/
		}
		video {
			position: absolute !important;
			top: 0;
			left: 0;
			width: 100% !important;
			height: 100% !important;
			/*z-index: 1;*/
		}
		video.video-js {
			z-index: 3;
		}
		.video-js .vjs-controls {
			z-index: 1002;
		}
		.video-js .vjs-big-play-button {
			z-index: 1002;
		}
		.videoWrapper .video-js {
			position: absolute;
			top: 0;
			left: 0;
			width: 100% !important;
			height: 100% !important;
			z-index: 1;
			background: #000000;
			outline: none;
		}
		.videoWrapper object,
		.videoWrapper embed {
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100% !important;
			z-index: 0;
		}
		.vjs-spinner {
		  /*display: none !important;*/
		}
		.video-js img.vjs-poster {
			height: 100% !important;
			width: 100% !important;
			max-width: 100%;
			z-index: 1;
		}');

        $tmpl = MFactory::getApplication()->getTemplate();

        if (!MRequest::getInt('playlist_id') and MFolder::exists(MPATH_THEMES.'/'.$tmpl.'/html/com_miwovideos')) {
            $document->addStyleDeclaration('
            .videoSizer_'.$this->item->id.' {
                margin-bottom : 30px
            }');
		}

		$tech = $this->config->get('fallback');
		if ($tech) {
			$document->addScriptDeclaration('videojs.options.techOrder = ["flash", "html5"];');
		}
		else {
			$document->addScriptDeclaration('videojs.options.techOrder = ["html5", "html5"];');
		}

		$document->addScriptDeclaration('videojs.options.children.loadingSpinner = false;');

		return true;
	}

    public function _processMatches(&$matches) {
	    $utility = MiwoVideos::get('utility');
	    $result = null;
	    $options = '';
        $videoParams = $matches[1];
        $videoParamsList = $this->getParams($videoParams);
        $html = $this->getHtmlOutput($videoParamsList);
        if ($this->item->duration and $this->config->get('frames')) {
            $html .= $this->getFramesOutput();
        }

	    $watch_later_id = $utility->getWatchlater()->id;
	    if (!empty($watch_later_id)) {
		    $result = $utility->checkVideoInPlaylists($watch_later_id, $this->item->id);
	    }
	    if (!empty($result) and !empty($watch_later_id)) {
		    $options .= "{name: 'watchlater', already_added: true}";
	    } else {
		    $options .= "{name: 'watchlater', already_added: false}";
	    }

	    $tech = '';
	    if ($this->pluginParams->get('id')) {
		    $tech = ', {"techOrder": ["youtube"], "src": "http://www.youtube.com/watch?v='.$this->pluginParams->get('id').'"}';
	    }

	    $ads = '';
	    $loop_button = '';
	    if ($this->pluginParams->get('loop_button', 1)) {
		    $loop_button = "if (typeof video.controlBar.getChild('loopbutton') === 'undefined') {
						        video.loopbutton({name: 'loopbutton'});
						    }";
	    }

	    MFactory::getDocument()->addScriptDeclaration("
            jQuery(document).ready( function (){
		        var video = videojs('plg_videojs_".$this->item->id."'$tech);
                if (typeof video.controlBar.getChild('watchlater') === 'undefined') {
		            video.watchlater($options);
		        }
                if (typeof video.controlBar.getChild('seek') === 'undefined') {
		            video.seek({name: 'seek'});
		        }
                	            ".$loop_button."
	            ".$ads."
	        });
        ");

	    
        $pattern = str_replace('[', '\[', $matches[0]);
        $pattern = str_replace(']', '\]', $pattern);
        $pattern = str_replace('/', '\/', $pattern);
        $pattern = str_replace('|', '\|', $pattern);

        $output = preg_replace('/'.$pattern.'/', $html, $this->output, 1);

        return $output;
    }

	protected function getParams($videoParams) {
        $pluginParams = $this->pluginParams;
		$videoParamsList['width'] 				= $pluginParams->get('width');
		$videoParamsList['height'] 				= $pluginParams->get('height');
		$videoParamsList['controls']			= $pluginParams->get('controls');
		$videoParamsList['autoplay']			= $pluginParams->get('autoplay');
		$videoParamsList['preload']				= $pluginParams->get('preload');
		$videoParamsList['loop']				= $pluginParams->get('loop');
		$videoParamsList['poster_visibility']	= $pluginParams->get('poster_visibility');
		$videoParamsList['playlist']	        = $pluginParams->get('playlist');
		$videoParamsList['video_mp4']			= '';
		$videoParamsList['video_webm']			= '';
		$videoParamsList['video_ogg']			= '';
		$videoParamsList['poster']				= MiwoVideos::get('utility')->getThumbPath($this->item->id, 'videos', $this->item->thumb);
		$videoParamsList['text_track']			= '';

		$items = explode(' ', $videoParams);

		foreach ($items as $item) {
			if ($item != '') {
				$item	= explode('=', $item);
				$name 	= $item[0];
				$value	= strtr($item[1], array('['=>'', ']'=>''));
				if ($name == "text_track") {
					$videoParamsList[$name][] = $value;
				} else {
					$videoParamsList[$name] = $value;
				}
			}
		}

		return $videoParamsList;
	}

	protected function getHtmlOutput(&$videoParamsList) {
		$options                  = array('text_track_html' => '', 'controls_html' => '', 'autoplay_html' => '', 'preload_html' => '', 'loop_html' => '', 'poster_html' => '');
		$item                     = $this->item;
		$options['pluginParams']  = $this->pluginParams;
		$width                    = $videoParamsList['width'];
		$height                   = $videoParamsList['height'];
		$controls                 = $videoParamsList['controls'];
		$autoplay                 = $videoParamsList['autoplay'];
		$preload                  = $videoParamsList['preload'];
		$loop                     = $videoParamsList['loop'];
		$poster_visibility        = $videoParamsList['poster_visibility'];
		$playlist                 = $videoParamsList['playlist'];
		$options['original_mp4']  = $videoParamsList['video_mp4'];
		$options['original_webm'] = $videoParamsList['video_webm'];
		$options['original_ogg']  = $videoParamsList['video_ogg'];
		$poster                   = $videoParamsList['poster'];
		$tracks                   = $videoParamsList['text_track'];
		$ratio                    = round(($height / $width) * 100, 1);

		// Controls
		if ($controls == "1") {
			$options['controls_html'] = ' controls="controls"';
		}

		// Autoplay
        switch ($autoplay) {
            case "global":
                if ($this->config->get('autoplay') == 1) {
	                $options['autoplay_html'] = ' autoplay="autoplay"';
                }
                break;
            case "1":
	            $options['autoplay_html'] = ' autoplay="autoplay"';
                break;
        }

		// Preload
        if ($preload == "auto" || $preload == "metadata" || $preload == "none") {
	        $options['preload_html'] = ' preload="'.$preload.'"';
		}

		// Loop
		if ($loop == "1") {
			$options['loop_html'] = ' loop="loop"';
		}

		// Poster image
		if ($poster_visibility == "1" && $poster != "") {
			$options['poster_html'] = ' poster="'.$poster.'"';
		}

		// Text tracks
		if (!empty($tracks)) {
			foreach ($tracks AS $track) {
				$track_items = explode('|', $track);
				$options['text_track_html'] .= '<track kind="'.$track_items[0].'" src="'.$track_items[1].'" srclang="'.$track_items[2].'" label="'.$track_items[3].'" />';
			}
		}

		$html = $this->_sourceHtml($item, $options);

		MFactory::getDocument()->addStyleDeclaration(
			'.videoSizer_'.$this->item->id.' { max-width: '.$width.'px; }
			.videoWrapper_'.$this->item->id.' { padding-bottom: '.$ratio.'%; }'
		);

		return $html;
	}

	protected function _sourceHtml($item, $options) {
		$video_mp4 = $video_webm = $video_ogg = $video_flv = '';

		$files        = MiwoVideos::get('files')->getVideoFiles($item->id);
		$utility      = MiwoVideos::get('utility');
		$default_size = $utility->getVideoSize($item->id, $item->source);
		$default_res  = '';

		if ($this->config->get('video_quality') == $default_size) {
			$default_res = 'true';
		}
		foreach ($files as $file) {

			if (!$item->duration) {
				$orig = '<source src="'.$utility->getVideoFilePath($item->id, "orig", $file->source, "url").'" type="video/'.$file->ext.'"/>';;
			}

			if ($file->process_type == '200' or $file->process_type < 7) {
				continue;
			}
			$size = $utility->getSize($file->process_type);

			if ($this->config->get('video_quality') == $size) {
				$default_res = 'true';
			}

			$src = $utility->getVideoFilePath($file->video_id, $size, $file->source, 'url');

			if ($file->ext == 'mp4' and $file->process_type == '100') {
				$src = $utility->getVideoFilePath($file->video_id, $default_size, $options['original_mp4'], 'url');
				$video_mp4 .= '<source src="'.$src.'" type="video/mp4" data-res="'.$default_size.'p" data-default="'.$default_res.'" />';
			}
			else if ($file->ext == 'mp4') {
				$video_mp4 .= '<source src="'.$src.'" type="video/mp4" data-res="'.$size.'p" data-default="'.$default_res.'" />';
			}

			if ($file->ext == 'webm' and $file->process_type == '100') {
				$src = $utility->getVideoFilePath($file->video_id, $default_size, $options['original_webm'], 'url');
				$video_webm .= '<source src="'.$src.'" type="video/webm" data-res="'.$default_size.'p" data-default="'.$default_res.'" />';
			}
			else if ($file->ext == 'webm') {
				$video_webm .= '<source src="'.$src.'" type="video/webm" data-res="'.$size.'p" data-default="'.$default_res.'" />';
			}

			if (($file->ext == 'ogg' or $file->ext == 'ogv') and $file->process_type == '100') {
				$src = $utility->getVideoFilePath($file->video_id, $default_size, $options['original_ogg'], 'url');
				$video_ogg .= '<source src="'.$src.'" type="video/ogg" data-res="'.$default_size.'p" data-default="'.$default_res.'" />';
			}
			else if ($file->ext == 'ogg' or $file->ext == 'ogv') {
				$video_ogg .= '<source src="'.$src.'" type="video/ogg" data-res="'.$size.'p" data-default="'.$default_res.'" />';
			}

			if ($file->ext == 'flv') {
				$video_flv .= '<source src="'.$src.'" type="video/flv" data-res="'.$size.'p" data-default="'.$default_res.'" />';
			}
			$default_res = '';
		}

		// HTML output
		$html = '<div class="videoSizer_'.$this->item->id.'"><div class="videoWrapper_'.$this->item->id.' videoWrapper">';
		$html .= '<video width="100%" height="100%" id="plg_videojs_'.$this->item->id.'" class="video-js vjs-default-skin vjs-big-play-centered"'.$options['controls_html'].$options['autoplay_html'].$options['preload_html'].$options['loop_html'].$options['poster_html'].'>';
		if (!$options['pluginParams']->get('id')) {
			if (!empty($video_mp4)) {
				$html .= $video_mp4;
			}

			if (!empty($video_webm)) {
				$html .= $video_webm;
			}

			if (!empty($video_ogg)) {
				$html .= $video_ogg;
			}

			if (!empty($video_flv)) {
				$html .= $video_flv;
			}

			if (!$item->duration) {
				$html .= $orig;
			}
			$html .= $options['text_track_html'];
		}

		$html .= ' </video>';
		$html .= '</div></div>';
		return $html;
	}

    public function getFramesOutput() {
        if (strpos($this->item->source,'amazonaws.com') !== false or strpos($this->item->source,'http://') === false) {
	        if (strpos($this->item->source,'amazonaws.com') !== false) {
		        $domain = substr($this->item->source, 0, strpos($this->item->source, 'wp-content/uploads/miwovideos'));
		        $frame_suffix = $domain;
	        }
	        else {
		        $frame_suffix = '';
	        }

            $output = "<script type=\"text/javascript\"><!--
						jQuery(document).ready(function() {
                        var video = videojs('plg_videojs_".$this->item->id."');
                        var duration = ".$this->item->duration.";
                        video.thumbnails({
                            0: {
                                src: '".$frame_suffix."wp-content/uploads/miwovideos/images/videos/".$this->item->id."/frames/out1.jpg',
                                style: {
                                    left: '-60px',
                                    width: '100px',
                                    height: '80px'
                                }
                            },";
            for ($i = 1; $i < $this->item->duration; $i++) {
                $output .= $i.":{
                                src: '".$frame_suffix."wp-content/uploads/miwovideos/images/videos/".$this->item->id."/frames/out". $i .".jpg',
                                    style: {
                                        left: '-60px',
                                        width: '100px',
                                        height: '80px'
                                    }
                                },";
            }
            $output .= $this->item->duration.": {
                                                src: '".$frame_suffix."wp-content/uploads/miwovideos/images/videos/".$this->item->id."/frames/out".$this->item->duration.".jpg',
                                                    style: {
                                                        width: '100px',
                                                        height: '80px'
                                                }
            }
            });
			if (typeof video.controlBar.getChild('resolutions') === 'undefined') {
                video.resolutions({name: 'resolutions'});
            }
});
            //--></script>";

            return $output;
        }
    }

	}