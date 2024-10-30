<?php
$document = MFactory::getDocument();
$document->addStyleSheet(MURL_MIWOVIDEOS.'/site/assets/css/perfect-scrollbar.css');
$document->addScript(MURL_MIWOVIDEOS.'/site/assets/js/jquery.mousewheel.js');
$document->addScript(MURL_MIWOVIDEOS.'/site/assets/js/perfect-scrollbar.js');
?>
<script type="text/javascript">
	jQuery(document).ready(function () {
		jQuery('#miwovideos_video_player_playlist').perfectScrollbar({suppressScrollX: true});
		var offsetTop = jQuery('.miwovideos_playing')[0].offsetTop;
		document.getElementById('miwovideos_video_player_playlist').scrollTo(0, offsetTop);
		document.getElementById("miwovideos_video_player_playlist").style.height = document.getElementById("miwovideos_video_player_playlist").style.height-30;
	});
</script>
<div id="miwovideos_video_player_playlist" class="miwovideos_video_player_playlist">
	<ul id="miwovideos_playlist">
		<?php
		$count = count($this->playlistvideos);
		foreach ($this->playlistvideos as $key => $playlistvideo) {
			$playing = ($this->item->id == $playlistvideo->video_id) ? true : false;
			if ($playing) {
				if ($key + 1 == $count) {
					$forward_url = $_url = MRoute::_('index.php?option=com_miwovideos&view=video&playlist_id='.$playlistvideo->playlist_id.'&video_id='.$this->playlistvideos[0]->video_id.$this->Itemid);
				}
				else {
					$forward_url = $_url = MRoute::_('index.php?option=com_miwovideos&view=video&playlist_id='.$playlistvideo->playlist_id.'&video_id='.$this->playlistvideos[ $key + 1 ]->video_id.$this->Itemid);
				}

				if ($key == 0) {
					$backward_url = $_url = MRoute::_('index.php?option=com_miwovideos&view=video&playlist_id='.$playlistvideo->playlist_id.'&video_id='.$this->playlistvideos[ $count - 1 ]->video_id.$this->Itemid);
				}
				else {
					$backward_url = $_url = MRoute::_('index.php?option=com_miwovideos&view=video&playlist_id='.$playlistvideo->playlist_id.'&video_id='.$this->playlistvideos[ $key - 1 ]->video_id.$this->Itemid);
				}
			}

			$_url = MRoute::_('index.php?option=com_miwovideos&view=video&playlist_id='.$playlistvideo->playlist_id.'&video_id='.$playlistvideo->video_id.$this->Itemid); ?>

			<li <?php echo ($playing) ? 'class = "miwovideos_playing"' : ''; ?>>
				<a href="<?php echo $_url; ?>">
					<div><?php echo (!$playing) ? ($key + 1) : '▶'; ?></div>
					<img class="video_thumb" src="<?php echo MiwoVideos::get('utility')->getThumbPath($playlistvideo->video_id, 'videos', $playlistvideo->thumb); ?>">
					<span><?php echo $playlistvideo->title; ?></span>
				</a>
			</li>
		<?php } ?>
		<li class="miwovideos_li_bottom"></li>
	</ul>
</div>
<script type="text/javascript">
	jQuery(document).ready(function () {
		<?php if ($this->config->get('video_player') == 'videojs') { ?>
			var myPlayer = videojs("plg_videojs_<?php echo $this->item->id; ?>");
			myPlayer.ready(function () {
				<?php if ($this->config->get('autoplay')) { ?>
					this.play();
				<?php } ?>
				this.on("ended", function () {
					window.location = "<?php echo $forward_url; ?>";
				});
			});
		<?php } elseif ($this->config->get('video_player') == 'jwplayer') { ?>
			jwplayer('mediaspace1').onReady(function () {
				<?php if ($this->config->get('autoplay')) { ?>
					this.play();
				<?php } ?>
				this.onComplete(function () {
					window.location = "<?php echo $forward_url; ?>";
				});
			});
		<?php } ?>
	});
</script>
<div id="miwovideos-control-bar" class="miwovideos-control-bar">
	<a href="<?php echo $forward_url; ?>">
		<div class="miwovideos_forward"></div>
	</a>
	<a href="<?php echo $backward_url; ?>">
		<div class="miwovideos_backward"></div>
	</a>
	<span>Playlist : <?php echo substr($playlistvideo->title, 0, 25);
		if (strlen($playlistvideo->title) > 25) {
			echo '...';
		} ?></span>
</div>