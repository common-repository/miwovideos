<?php
/**
 * @package        MiwoVideos
 * @copyright      Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license        GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die;
if (count($this->items)) {
	$utility    = MiwoVideos::get('utility');
	$thumb_size = $utility->getThumbSize($this->config->get('thumb_size'));
	foreach ($this->items as $item) {

		if (empty($item->videos)) {
			continue;
		}
		else {
			$video = $item->videos[0];
		}

		if (!empty($item->thumb)) {
			$thumb = $utility->getThumbPath($item->id, 'playlists', $item->thumb);
		}
		else {
			$thumb = $utility->getThumbPath($video->video_id, 'videos', $video->thumb);
		}
		$this->Itemid = MiwoVideos::get('router')->getItemid(array('view' => 'playlist', 'playlist_id' => $item->id), null, true);
		$playlist_url = MRoute::_('index.php?option=com_miwovideos&view=video&video_id='.$video->video_id.'&playlist_id='.$item->id.$this->Itemid);
		$Itemid       = MiwoVideos::get('router')->getItemid(array('view' => 'playlist', 'playlist_id' => $item->id), null, true);
		$full_url     = MRoute::_('index.php?option=com_miwovideos&view=playlist&playlist_id='.$item->id.$Itemid);
		$Itemid       = MiwoVideos::get('router')->getItemid(array('view' => 'channel', 'channel_id' => $item->channel_id), null, true);
		$channel_url  = MRoute::_('index.php?option=com_miwovideos&view=channel&channel_id='.$item->channel_id.$Itemid); ?>
		<div class="videos-items-list-box">
			<div class="playlists-list-item" style="width: <?php echo $thumb_size; ?>px">
				<div class="videos-aspect<?php echo $this->config->get('thumb_aspect'); ?>"></div>
				<a href="<?php echo $playlist_url; ?>">
					<img class="videos-items-grid-thumb" src="<?php echo $thumb; ?>" title="<?php echo $item->title; ?>" alt="<?php echo $item->title; ?>"/>
				</a>
			</div>
			<div class="playlists-items-list-box-content">
				<h3 class="miwovideos_box_h3">
					<a href="<?php echo $playlist_url; ?>" title="<?php echo $item->title; ?>">
						<?php echo $this->escape(MHtmlString::truncate($item->title, $this->config->get('title_truncation'), false, false)); ?>
					</a>
				</h3>

				<div class="playlists-meta">
					<div class="miwovideos-meta-info">
						<div class="date-created">
							<span class="value"><?php echo MiwoVideos::agoDateFormat($item->created); ?></span>
						</div>
					</div>
				</div>
				<div class="playlists-items">
					<?php $i = 0;
					foreach ($item->videos as $video) {
						$i++;
						$Itemid = MiwoVideos::get('router')->getItemid(array('view' => 'video', 'video_id' => $video->video_id), null, true); ?>
						<div class="playlists-item">
							<a href="<?php echo MRoute::_('index.php?option=com_miwovideos&view=video&video_id='.$video->video_id.$Itemid); ?>">
								<?php echo $this->escape(MHtmlString::truncate($video->title, $this->config->get('title_truncation'), false, false)); ?>
							</a>
							<span class="miwovideos-duration"><?php echo $utility->secondsToTime($video->duration); ?></span>
						</div>
						<?php if ($i == 2) {
							break;
						} ?>
					<?php } ?>
				</div>
				<div class="playlists-meta">
					<div class="miwovideos-meta-info">
						<a class="date-created" href="<?php echo $full_url; ?>"><?php echo MText::_('COM_MIWOVIDEOS_VIEW_PLAYLIST'); ?>
							&nbsp;&nbsp;(<?php echo isset($item->total) ? $item->total : '0'; ?>
							&nbsp;<?php echo MText::_('COM_MIWOVIDEOS_VIDEOS') ?>)</a>
					</div>
				</div>
			</div>
		</div>
	<?php } ?>
<?php } ?>
<script type="text/javascript">
	jQuery(document).ready(function () {
		var box_width = document.getElementById("channel_items").offsetWidth;
		var thumb_size = <?php echo $thumb_size; ?>;
		var thumb_percent = Math.round((thumb_size / box_width) * 100);
		var desc_percent = 100-thumb_percent-3;
		jQuery('div[class^="playlists-items-list-box-content"]').css('width', desc_percent+'%');
		jQuery('div[class^="playlists-list-item"]').css('width', thumb_percent+'%');
	});
</script>