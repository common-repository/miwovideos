<?php
/**
 * @package        MiwoVideos
 * @copyright      Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license        GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die;

if (!count($this->items)) {
	return;
}
$utility    = MiwoVideos::get('utility');
$thumb_size = $utility->getThumbSize($this->config->get('thumb_size'));
foreach ($this->items as $item) {
	$Itemid = MiwoVideos::get('router')->getItemid(array('view' => 'category', 'category_id' => $item->id), null, true);
	$url    = MRoute::_('index.php?option=com_miwovideos&view=category&category_id='.$item->id.$Itemid);
	$thumb  = $utility->getThumbPath($item->id, 'categories', $item->thumb); ?>
	<div class="videos-items-list-box">
		<div class="playlists-list-item" style="width: <?php echo $thumb_size; ?>px">
			<div class="videos-aspect<?php echo $this->config->get('thumb_aspect'); ?>"></div>
			<a href="<?php echo $url; ?>">
				<img class="videos-items-grid-thumb" src="<?php echo $thumb; ?>" alt="<?php echo $item->thumb; ?>"/>
			</a>
		</div>
		<div class="playlists-items-list-box-content">
			<h3 class="miwovideos_box_h3">
				<a href="<?php echo $url; ?>" title="<?php echo $item->title; ?>">
					<?php echo $this->escape(MHtmlString::truncate($item->title, $this->config->get('title_truncation'), false, false)); ?>
				</a>
			</h3>

			<div class="playlists-meta">
				<div class="miwovideos-meta-info">
					<?php if ($this->config->get('show_number_videos')) { ?>
						<div class="created_by">(<?php echo $item->total_videos; ?> <?php echo $item->total_videos > 1 ? MText::_('COM_MIWOVIDEOS_VIDEOS') : MText::_('COM_MIWOVIDEOS_VIDEO'); ?>)</div>
					<?php } ?>
					<div class="date-created">
						<span class="value"><?php echo MiwoVideos::agoDateFormat($item->created); ?></span>
					</div>
				</div>
			</div>
			<?php if (!empty($item->introtext)) { ?>
				<div class="playlists-items">
					<?php echo MHtmlString::truncate(html_entity_decode($item->introtext, ENT_QUOTES), $this->config->get('desc_truncation'), false, false); ?>
				</div>
			<?php } ?>
		</div>
	</div>
<?php } ?>
<script type="text/javascript">
	jQuery(document).ready(function () {
		var box_width = document.getElementById("adminForm").offsetWidth;
		var thumb_size = <?php echo $thumb_size; ?>;
		var thumb_percent = Math.round((thumb_size / box_width) * 100);
		var desc_percent = 100-thumb_percent-3;
		jQuery('div[class^="playlists-items-list-box-content"]').css('width', desc_percent+'%');
		jQuery('div[class^="playlists-list-item"]').css('width', thumb_percent+'%');
	});
</script>