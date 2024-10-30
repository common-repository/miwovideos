<?php
/**
 * @package        MiwoVideos
 * @copyright      Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license        GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die;
$Itemid      = MiwoVideos::get('router')->getItemid(array('view' => 'video', 'video_id' => $this->item->id), null, true);
$url         = MRoute::_('index.php?option=com_miwovideos&view=video&video_id='.$this->item->id.$Itemid);
$Itemid      = MiwoVideos::get('router')->getItemid(array('view' => 'channel', 'channel_id' => $this->item->channel_id), null, true);
$channel_url = MRoute::_('index.php?option=com_miwovideos&view=channel&channel_id='.$this->item->channel_id.$Itemid);
$socialUrl   = MUri::base().$url;
$utility     = MiwoVideos::get('utility');
?>
<div id="notification"></div>
<div class="miwovideos_box">
	<div class="miwovideos_box_heading">
		<h1 class="miwovideos_box_h1"><?php echo $this->item->title; ?></h1>
	</div>
	<div class="miwovideos_box_content">
		<div class="miwovideos_video_player">
							<?php echo $this->loadTemplate('player'); ?>
				<?php echo (!empty($this->playlistvideos)) ? $this->loadTemplate('playlist') : ''; ?>		</div>
		<br/>

		<div class="miwovideos_user_header">
			<a href="<?php echo $channel_url; ?>">
				<img class="miwovideos_channel_thumb48" alt="<?php echo $this->item->channel->title; ?>" src="<?php echo $utility->getThumbPath($this->item->channel->id, 'channels', $this->item->channel->thumb); ?>"/>
			</a>

			<div class="miwovideos_channel_info">
				<a class="miwovideos_channel_title" href="<?php echo $channel_url; ?>">
					<?php echo $this->escape(MHtmlString::truncate($this->item->channel->title, $this->config->get('title_truncation'), false, false)); ?>
				</a>
				<a href="<?php echo $channel_url; ?>"><span class="miwovideos-meta-info date-created"><?php echo $this->item->channel_videos_count; ?> <?php echo MText::_('COM_MIWOVIDEOS_VIDEOS'); ?></span></a><br>
				<?php if ($this->config->get('subscriptions')) { ?>
					<div class="miwovideos_subscribe" id="<?php echo $this->item->channel_id ?>">
						<?php if (is_null($this->checksubscription)) { ?>
							<a class="<?php echo MiwoVideos::getButtonClass(); ?> subscribe" id="subscribe_button">
								<?php echo MText::_('COM_MIWOVIDEOS_SUBSCRIBE'); ?>
							</a>
							<div class="miwovideos_subscribe_count" id="subs_count<?php echo $this->item->channel_id ?>">
								<span class="subs_count"><?php echo $this->item->channel_subs; ?></span></div>
							<div class="subs_nub"><s></s><i></i></div>
							<a class="<?php echo MiwoVideos::getButtonClass(); ?> subscribed" style="display:none" id="unsubscribe_button"><?php echo MText::_('COM_MIWOVIDEOS_SUBSCRIBED'); ?></a>
						<?php
						}
						else {
							?>
							<a class="<?php echo MiwoVideos::getButtonClass(); ?> subscribe" style="display:none" id="subscribe_button">
								<?php echo MText::_('COM_MIWOVIDEOS_SUBSCRIBE'); ?>
							</a>
							<div style="display:none" class="miwovideos_subscribe_count" id="subs_count<?php echo $this->item->channel_id ?>">
								<span class="subs_count"><?php echo $this->item->channel_subs; ?></span></div>
							<div style="visibility:hidden" class="subs_nub"><s></s><i></i></div>
							<a class="<?php echo MiwoVideos::getButtonClass(); ?> subscribed" id="unsubscribe_button"><?php echo MText::_('COM_MIWOVIDEOS_SUBSCRIBED'); ?></a>
						<?php } ?>
					</div>
				<?php } ?>
			</div>
			<div class="miwovideos_views_info">
				<div class="miwovideos_views_count"><?php echo number_format($this->item->hits); ?> <?php echo MText::_('COM_MIWOVIDEOS_VIEWS'); ?></div>
				<br>
				<?php if ($this->config->get('likes_dislikes')) { ?>
					<div class="views_likes_dislikes">
						<span class="likes_count"><span id="miwovideos_like1"><?php echo $this->item->likes; ?></span> <?php echo MText::_('COM_MIWOVIDEOS_LIKES'); ?>
							&nbsp;</span>
						<span class="dislikes_count"><span id="miwovideos_like2"><?php echo $this->item->dislikes; ?></span> <?php echo MText::_('COM_MIWOVIDEOS_DISLIKES'); ?></span>
					</div>
				<?php } ?>
			</div>
		</div>
		<?php if ($this->config->get('likes_dislikes')) { ?>
			<div class="miwovideos_actions">
				<div class="miwovideos_like_actions">
					<a class="<?php echo MiwoVideos::getButtonClass(); ?> <?php if ($this->checklikesdislikes == 1) {
						echo 'active';
					} ?>" id="like_action">
						<div class="like_button" id="like1"></div>
						<span>&nbsp;<?php echo MText::_('COM_MIWOVIDEOS_LIKE'); ?></span>
					</a>
					<a class="<?php echo MiwoVideos::getButtonClass(); ?> <?php if ($this->checklikesdislikes == 2) {
						echo 'active';
					} ?>" id="dislike_action">
						<div class="dislike_button" id="like2"></div>
						<span>&nbsp;<?php echo MText::_('COM_MIWOVIDEOS_DISLIKE'); ?></span>
					</a>
				</div>
			</div>
		<?php } ?>
		<?php echo MHtml::_('tabs.start', 'right', array('useCookie' => 0)); ?>
		<?php echo MHtml::_('tabs.panel', MText::_('COM_MIWOVIDEOS_DESCRIPTION'), 'sl_desc'); ?>
		<?php echo $this->loadTemplate('description'); ?>
		<?php echo MHtml::_('tabs.panel', MText::_('COM_MIWOVIDEOS_SHARE'), 'sl_share'); ?>
		<?php echo $this->loadTemplate('share'); ?>
		<?php if ($this->config->get('playlists')) { ?>
			<?php echo MHtml::_('tabs.panel', MText::_('COM_MIWOVIDEOS_ADDTO'), 'sl_addto'); ?>
			<?php echo $this->loadTemplate('addto'); ?>
		<?php } ?>
		<?php if ($this->config->get('reports') != "0") { ?>
			<?php echo MHtml::_('tabs.panel', MText::_('COM_MIWOVIDEOS_REPORT'), 'sl_report'); ?>
		<?php } ?>
		<?php if ($this->config->get('reports') != "0") { ?>
			<?php echo $this->loadTemplate('report'); ?>
		<?php } ?>
		<?php echo MHtml::_('tabs.end'); ?>
		<?php if ($this->config->get('comments') != "0") { ?>
			<div class="miwovideos_video_comments">
				<?php
				MiwoVideos::get('utility')->trigger('getComments', array($this->item->id, $this->item->title));
				?>
			</div>
		<?php } ?>
	</div>
	<div class="clr"></div>
</div>
<?php if ($this->config->get('likes_dislikes')) { ?>
	<script type="text/javascript"><!--
		jQuery('#like_action,#dislike_action').click(function () {
			var id = jQuery(this).children().attr("id");
			var type = id.replace("like", "");
			jQuery.ajax({
				url : '<?php echo MURL_ADMIN; ?>/admin-ajax.php?action=miwovideos&view=video&task=checkLikesDislikes&format=raw&item_type=videos&item_id=<?php echo $this->item->id; ?>',
				type: 'post',
				data: {type: type},
				dataType: 'json',
				success: function (json) {
					if (json['success']) {
						var count = jQuery('#miwovideos_like'+type).text();
						var oldtype = json['type'];
						if (json['type'] == -1) {
							jQuery.ajax({
								url : '<?php echo MURL_ADMIN; ?>/admin-ajax.php?action=miwovideos&view=video&task=likeDislikeItem&format=raw&item_type=videos&item_id=<?php echo $this->item->id; ?>',
								type: 'post',
								data: {type: type},
								dataType: 'json',
								success: function (json) {
									if (json['success']) {
										jQuery('#'+id).parent().addClass('active');
										jQuery('#miwovideos_like'+type).text(parseInt(count)+1);
									}
									if (json['redirect']) location = json['redirect'];
								}
							});
						} else {
							if (json['type'] == type) {
								jQuery.ajax({
									url : '<?php echo MURL_ADMIN; ?>/admin-ajax.php?action=miwovideos&view=video&task=likeDislikeItem&format=raw&item_type=videos&item_id=<?php echo $this->item->id; ?>',
									type: 'post',
									data: {type: type, change: -1},
									dataType: 'json',
									success: function (json) {
										if (json['success']) {
											jQuery('#'+id).parent().removeClass('active');
											jQuery('#miwovideos_like'+type).text(parseInt(count)-1);
										}
										if (json['redirect']) location = json['redirect'];
									}
								});
							} else {
								jQuery.ajax({
									url : '<?php echo MURL_ADMIN; ?>/admin-ajax.php?action=miwovideos&view=video&task=likeDislikeItem&format=raw&item_type=videos&item_id=<?php echo $this->item->id; ?>',
									type: 'post',
									data: {type: type, change: 1},
									dataType: 'json',
									success: function (json) {
										if (json['success']) {
											jQuery('#'+id).parent().addClass('active');
											jQuery('#like'+oldtype).parent().removeClass('active');
											jQuery('#miwovideos_like'+type).text(parseInt(count)+1);
											jQuery('#miwovideos_like'+oldtype).text(parseInt(jQuery('#miwovideos_like'+oldtype).text())-1);
										}
										if (json['redirect']) location = json['redirect'];
									}
								});
							}
						}
					}
					if (json['redirect']) location = json['redirect'];
				}
			});
		});
		//--></script>
<?php } ?>
<?php if ($this->config->get('subscriptions')) { ?>
	<script type="text/javascript"><!--
		jQuery('#subscribe_button, #unsubscribe_button').click(function () {
			var clicked_button = jQuery(this).attr('id').replace("_button", "");
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
							jQuery('#'+id+' #unsubscribe_button').hide();
							jQuery('#'+id+' #subscribe_button').show();
							subs.children().text(parseInt(count)-1);
							subs.show();
							subs.next().css('visibility', 'visible');
						} else {
							jQuery('#'+id+' #subscribe_button').hide();
							jQuery('#'+id+' #unsubscribe_button').show();
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
		//--></script>
<?php } ?>


