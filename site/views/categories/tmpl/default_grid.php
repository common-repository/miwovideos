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
$utility = MiwoVideos::get('utility');
foreach ($this->items as $item) {
	$thumb = $utility->getThumbPath($item->id, 'categories', $item->thumb);
	$url   = MRoute::_('index.php?option=com_miwovideos&view=category&category_id='.$item->id.$this->Itemid); ?>
	<div class="miwovideos_column<?php echo $this->config->get('items_per_column'); ?>">
		<div class="videos-grid-item">
			<div class="videos-aspect<?php echo $this->config->get('thumb_aspect'); ?>"></div>
			<a href="<?php echo $url; ?>">
				<img class="videos-items-grid-thumb" src="<?php echo $thumb; ?>" alt="<?php echo $item->title; ?>"/>
			</a>
		</div>
		<div class="playlists-items-grid-box-content">
			<h3 class="miwovideos_box_h3">
				<a href="<?php echo $url; ?>" title="<?php echo $item->title; ?>">
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
		</div>
	</div>
<?php } ?>