<?php
/**
 * @package        MiwoVideos
 * @copyright      Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license        GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die;

if (count($this->items)) {
	foreach ($this->checksubscription as $checksubscription) {
		$subs[] = $checksubscription['item_id'];
	}
	$utility = MiwoVideos::get('utility');
	foreach ($this->items as $item) {
		$Itemid = MiwoVideos::get('router')->getItemid(array('view' => 'channel', 'channel_id' => $item->id), null, true);
		$url    = MRoute::_('index.php?option=com_miwovideos&view=channel&channel_id='.$item->id.$Itemid); ?>
		<div class="miwovideos_channels_grid">
			<a href="<?php echo $url; ?>">
				<img class="miwovideos_channels_box_content_img2" src="<?php echo $utility->getThumbPath($item->id, 'channels', $item->thumb); ?>" title="<?php echo $item->title; ?>" alt="<?php echo $item->title; ?>"/>
			</a>

			<div class="miwovideos_channels_box_content">
				<div class="miwovideos_channels_box_content_header">
					<h3 class="miwovideos_box_h3">
						<a href="<?php echo $url; ?>" title="<?php echo $item->title; ?>">
							<?php echo $this->escape(MHtmlString::truncate($item->title, $this->config->get('title_truncation'), false, false)); ?>
						</a>
					</h3>
					<?php if ($this->config->get('subscriptions')) { ?>
						<div class="miwovideos_subscribe" id="<?php echo $item->id; ?>">
							<?php if (isset($subs)) { ?>
								<?php if (in_array($item->id, $subs)) { ?>
									<a class="<?php echo MiwoVideos::getButtonClass(); ?> subscribe" data-subs-toggle="subscribe" style="display:none">
										<?php echo MText::_('COM_MIWOVIDEOS_SUBSCRIBE'); ?>
									</a>
									<div class="miwovideos_subscribe_count" style="display:none" id="subs_count<?php echo $item->id ?>">
										<span class="subs_count"><?php echo is_null($item->subs) ? '0' : number_format($item->subs); ?></span>
									</div>
									<div style="visibility:hidden" class="subs_nub"><s></s><i></i></div>
									<a class="<?php echo MiwoVideos::getButtonClass(); ?> subscribed" data-subs-toggle="unsubscribe"><?php echo MText::_('COM_MIWOVIDEOS_SUBSCRIBED'); ?></a>
								<?php
								}
								else {
									?>
									<a class="<?php echo MiwoVideos::getButtonClass(); ?> subscribe" data-subs-toggle="subscribe">
										<?php echo MText::_('COM_MIWOVIDEOS_SUBSCRIBE'); ?>
									</a>
									<div class="miwovideos_subscribe_count" id="subs_count<?php echo $item->id ?>">
										<span class="subs_count"><?php echo is_null($item->subs) ? '0' : number_format($item->subs); ?></span>
									</div>
									<div class="subs_nub"><s></s><i></i></div>
									<a class="<?php echo MiwoVideos::getButtonClass(); ?> subscribed" data-subs-toggle="unsubscribe" style="display:none"><?php echo MText::_('COM_MIWOVIDEOS_SUBSCRIBED'); ?></a>
								<?php } ?>
							<?php
							}
							else {
								?>
								<a class="<?php echo MiwoVideos::getButtonClass(); ?> subscribe" data-subs-toggle="subscribe">
									<?php echo MText::_('COM_MIWOVIDEOS_SUBSCRIBE'); ?>
								</a>
								<div class="miwovideos_subscribe_count" id="subs_count<?php echo $item->id ?>">
									<span class="subs_count"><?php echo isset($item->subs) ? number_format($item->subs) : '0'; ?></span>
								</div>
								<div class="subs_nub"><s></s><i></i></div>
								<a class="<?php echo MiwoVideos::getButtonClass(); ?> subscribed" style="display:none" data-subs-toggle="unsubscribe"><?php echo MText::_('COM_MIWOVIDEOS_SUBSCRIBED'); ?></a>
							<?php } ?>
						</div>
					<?php } ?>
				</div>
				<?php echo MHtmlString::truncate(html_entity_decode($item->introtext, ENT_QUOTES), $this->config->get('desc_truncation'), false, false); ?>
				<div class="miwovideos_preview_videos">
					<div class="miwovideos_preview_videos_title"><?php echo MText::_('COM_MIWOVIDEOS_PREVIEW_VIDEOS'); ?></div>
					<?php foreach ($item->videos as $video) { ?>
						<?php $Itemid = MiwoVideos::get('router')->getItemid(array('view' => 'video', 'video_id' => $video->id), null, true); ?>
						<?php $url = MRoute::_('index.php?option=com_miwovideos&view=video&video_id='.$video->id.$Itemid); ?>
						<div class="miwovideos_preview">
							<a href="<?php echo $url; ?>">
								<img class="miwovideos_thumb_100" src="<?php echo $utility->getThumbPath($video->id, 'videos', $video->thumb); ?>" title="<?php echo $video->title; ?>" alt="<?php echo $video->title; ?>"/>
							</a>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
	<?php } ?>
<?php } ?>

<script type="text/javascript">
	jQuery('.subscribe, .subscribed').click(function () {
		var clicked_button = jQuery(this).attr('data-subs-toggle');
		var id = jQuery(this).parent().attr("id");
		jQuery.ajax({
			url : '<?php echo MURL_ADMIN; ?>/admin-ajax.php?action=miwovideos&view=channels&task=subscribeToItem&format=raw',
			type: 'post',
			data: {type: clicked_button, id: id},
			dataType: 'json',
			success: function (json) {
				if (json['success']) {
					var subs = jQuery('#subs_count'+id);
					var count = subs.children().text();
					if (clicked_button == "unsubscribe") {
						jQuery('#'+id+' .subscribed').hide();
						jQuery('#'+id+' .subscribe').show();
						subs.children().text(parseInt(count)-1);
						subs.show();
						subs.next().css('visibility', 'visible');
					} else {
						jQuery('#'+id+' .subscribe').hide();
						jQuery('#'+id+' .subscribed').show();
						subs.children().text(parseInt(count)+1);
						subs.hide();
						subs.next().css('visibility', 'hidden');
					}
				}
				if (json['redirect']) {
					location = json['redirect'];
				}
				if (json['error']) {
					jQuery('#notification').html('<div class="miwovideos_warning" style="display: none;">'+json['error']+'</div>');
					jQuery('.miwovideos_warning').fadeIn('slow');
					jQuery('.miwovideos_box, body').animate({scrollTop: 0}, 'slow');
					jQuery('.miwovideos_warning').delay(5000).fadeOut('slow');
				}
			}
		});
	});
	jQuery(document).ready(function () {
		jQuery('.subscribed').hover(
			function () {
				jQuery(this).text("<?php echo MText::_('COM_MIWOVIDEOS_UNSUBSCRIBE'); ?>");
			}, function () {
				jQuery(this).text("<?php echo MText::_('COM_MIWOVIDEOS_SUBSCRIBED'); ?>");
			}
		);
	});
</script>