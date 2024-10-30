<?php
/**
 * @package        MiwoVideos
 * @copyright      Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license        GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die;

$Itemid      = MiwoVideos::get('router')->getItemid(array('view' => 'channel', 'channel_id' => $this->channelitem->id), null, true);
$channel_url = MRoute::_('index.php?option=com_miwovideos&view=channel&channel_id='.$this->channelitem->id.$Itemid);
$seconds     = 0;
foreach ($this->items as $videos) {
	$seconds += $videos->duration;
}
$utility = MiwoVideos::get('utility');
?>
<?php if (($this->params->get('show_page_heading', '0') == '1')) { ?>
	<?php $page_heading = $this->params->get('page_heading', ''); ?>
	<?php if (!empty($page_heading)) { ?>
		<h1><?php echo $page_heading; ?></h1>
	<?php }
	else if (!empty($this->channelitem->title)) { ?>
		<h1><?php echo $this->channelitem->title; ?></h1>
	<?php } ?>
<?php } ?>
<div id="notification"></div>
<div class="miwovideos_box">
	<div class="miwovideos_box_heading">
		<h1 class="miwovideos_box_h1"><?php echo $this->channelitem->title; ?></h1>
	</div>
	<div class="miwovideos_box_content">
		<!-- content -->
		<?php if ($this->channelitem->banner and file_exists(MIWOVIDEOS_UPLOAD_DIR.'/images/channels/'.$this->channelitem->id.'/banner/thumb/'.$this->channelitem->banner)) {
			$background_image = "url(".MURL_MEDIA."/miwovideos/images/channels/".$this->channelitem->id."/banner/thumb/".$this->channelitem->banner.") no-repeat;";
		}
		else {
			$background_image = "";
		} ?>
		<div class="banner_image" style="background: <?php echo $background_image; ?>; background-repeat: round;">
			<a href="<?php echo $channel_url; ?>">
				<img class="channel-items-list-thumb" src="<?php echo $utility->getThumbPath($this->channelitem->id, 'channels', $this->channelitem->thumb); ?>" title="<?php echo $this->channelitem->title; ?>" alt="<?php echo $this->channelitem->title; ?>"/>
			</a>
		</div>
		<div class="miwovideos_channel_header">
			<div class="miwovideos_channel_title">
				<h1>
					<a href="<?php echo MRoute::_('index.php?option=com_miwovideos&view=channel&channel_id='.$this->channelitem->id.$Itemid); ?>"><?php echo $this->channelitem->title; ?></a>
				</h1>
			</div>
			<?php if (MiwoVideos::get('channels')->getDefaultChannel()->id == $this->channelitem->id or $this->channelitem->share_others) { ?>
				<span class="miwovideos_upload">
                    <a href="<?php echo $utility->route(MUri::base().'index.php?option=com_miwovideos&view=upload&channel_id='.$this->item->id.'&dashboard=1') ?>" class="<?php echo MiwoVideos::getButtonClass(); ?>">
	                    <?php echo MText::_('COM_MIWOVIDEOS_UPLOAD'); ?>
                    </a>
                </span>
			<?php } ?>
			<?php if ($this->config->get('subscriptions')) { ?>
				<div class="miwovideos_subscribe" id="<?php echo $this->channelitem->id ?>">
					<?php if (is_null($this->checksubscription)) { ?>
						<a class="<?php echo MiwoVideos::getButtonClass(); ?> subscribe" id="subscribe_button">
							<?php echo MText::_('COM_MIWOVIDEOS_SUBSCRIBE'); ?>
						</a>
						<a class="<?php echo MiwoVideos::getButtonClass(); ?> subscribed" style="display:none" id="unsubscribe_button"><?php echo MText::_('COM_MIWOVIDEOS_SUBSCRIBED'); ?></a>
					<?php }
					else { ?>
						<a class="<?php echo MiwoVideos::getButtonClass(); ?> subscribe" style="display:none" id="subscribe_button">
							<?php echo MText::_('COM_MIWOVIDEOS_SUBSCRIBE'); ?>
						</a>
						<a class="<?php echo MiwoVideos::getButtonClass(); ?> subscribed" id="unsubscribe_button"><?php echo MText::_('COM_MIWOVIDEOS_SUBSCRIBED'); ?></a>
					<?php } ?>
					<div class="miwovideos_subscribe_count" id="subs_count<?php echo $this->item->channel_id ?>">
						<span class="subs_count"><?php echo number_format($this->channelitem->subs); ?></span></div>
					<div class="subs_nub"><s></s><i></i></div>
				</div>
			<?php } ?>
		</div>
		<?php echo MHtml::_('tabs.start', 'left', array('useCookie' => 1)); ?>
		<?php echo MHtml::_('tabs.panel', $this->item->title, 'sl_playlist'); ?>
		<form method="post" name="adminForm" id="adminForm" action="<?php echo MRoute::_('index.php?option=com_miwovideos&channel_id='.$this->channelitem->id.'&view=playlist'.$this->Itemid); ?>">
			<div class="miwovideos_views_info">
				<li><?php echo $this->totalvideos == 1 ? $this->totalvideos." ".MText::_('COM_MIWOVIDEOS_VIDEO') : $this->totalvideos." ".MText::_('COM_MIWOVIDEOS_VIDEOS'); ?></li>
				<li><?php echo $utility->secondsToTime($seconds, true); ?></li>
				<li class="miwovideos_views_count"><?php echo number_format($this->item->hits); ?> <?php echo MText::_('COM_MIWOVIDEOS_VIEWS'); ?></li>
				<br>

				<div class="views_likes_dislikes">
					<span class="likes_count"><span id="miwovideos_like1"><?php echo $this->item->likes; ?></span> <?php echo MText::_('COM_MIWOVIDEOS_LIKES'); ?>
						&nbsp;</span>
					<span class="dislikes_count"><span id="miwovideos_like2"><?php echo $this->item->dislikes; ?></span> <?php echo MText::_('COM_MIWOVIDEOS_DISLIKES'); ?></span>
				</div>
			</div>
			<div class="miwovideos_expander_collapsed"><?php echo html_entity_decode($this->item->introtext, ENT_QUOTES); ?></div>
			<div class="miwovideos_expander" style="display: none">
				<?php echo html_entity_decode($this->item->introtext.$this->item->fulltext, ENT_QUOTES); ?>
				<?php if ($this->config->get('custom_fields')) { ?>
					<div class="miwovideos_custom_fields">
						<?php
						if (!empty($this->fields)) {
							foreach ($this->fields as $field) {
								?>
								<div class="title">
									<?php echo $field->title; ?>
								</div>
								<div class="content">
									<?php echo str_replace('***', ', ', $field->field_value); ?>
								</div>
								<br/>
							<?php
							}
						} ?>
					</div>
				<?php } ?>
			</div>
			<?php if (!empty($this->item->introtext)) { ?>
				<div class="video_more">
					<button type="button" class="miwovideos_more_button"><?php echo MText::_('COM_MIWOVIDEOS_SHOW_MORE'); ?></button>
				</div>
			<?php } ?>
			<?php $playlist_url = MRoute::_('index.php?option=com_miwovideos&view=video&video_id='.$this->items[0]->id.'&playlist_id='.$this->item->id.$this->Itemid); ?>
			<div class="miwovideos_playlist_subheader">
				<div class="miwovideos_actions">
					<div class="miwovideos_like_actions">
						<a class="<?php echo MiwoVideos::getButtonClass(); ?>" href="<?php echo $playlist_url; ?>">
							<span>&#9654;&nbsp;<?php echo MText::_('COM_MIWOVIDEOS_PLAY_ALL'); ?></span>
						</a>
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
						<a class="<?php echo MiwoVideos::getButtonClass(); ?>" id="share_button">
							<span><?php echo MText::_('COM_MIWOVIDEOS_SHARE'); ?></span>
						</a>
					</div>
				</div>
				<!-- AddThis Button BEGIN -->
				<div class="addthis_toolbox addthis_default_style addthis_32x32_style" id="addthis_share" style="display: none;">
					<a class="addthis_button_facebook"></a>
					<a class="addthis_button_twitter"></a>
					<a class="addthis_button_google_plusone_share"></a>
					<a class="addthis_button_blogger"></a>
					<a class="addthis_button_odnoklassniki_ru"></a>
					<a class="addthis_button_vk"></a>
					<a class="addthis_button_tumblr"></a>
					<a class="addthis_button_reddit"></a>
					<a class="addthis_button_linkedin"></a>
					<a class="addthis_button_compact"></a>
				</div>
				<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js"></script>
				<!-- AddThis Button END -->
			</div>
			<?php if (count($this->items)) {
				$thumb_size = $utility->getThumbSize($this->config->get('thumb_size'));
				foreach ($this->items as $item) {
					$video_itemId = MiwoVideos::get('router')->getItemid(array('view' => 'video', 'video_id' => $item->id), null, true);
					$video_url    = MRoute::_('index.php?option=com_miwovideos&view=video&video_id='.$item->id.$video_itemId); ?>
					<div class="videos-items-list-box">
						<div class="videos-list-item" style="width: <?php echo $thumb_size; ?>px">
							<div class="videos-aspect<?php echo $this->config->get('thumb_aspect'); ?>"></div>
							<a href="<?php echo $video_url; ?>">
								<img class="videos-items-grid-thumb" src="<?php echo $utility->getThumbPath($item->id, 'videos', $item->thumb); ?>" title="<?php echo $item->title; ?>" alt="<?php echo $item->title; ?>"/>
							</a>
						</div>
						<div class="videos-items-list-box-content">
							<h3 class="miwovideos_box_h3">
								<a href="<?php echo $video_url; ?>" title="<?php echo $item->title; ?>">
									<?php echo $this->escape(MHtmlString::truncate($item->title, $this->config->get('title_truncation'), false, false)); ?>
								</a>
							</h3>

							<div class="playlists-meta">
								<div class="miwovideos-meta-info">
									<div class="created_by">
										<span class="key"><?php echo MText::_('COM_MIWOVIDEOS_CREATED_BY'); ?></span>
                                    <span class="value">
                                        <a href="<?php echo $channel_url; ?>" title="<?php echo $this->channelitem->title; ?>">
	                                        <?php echo $this->escape(MHtmlString::truncate($this->channelitem->title, $this->config->get('title_truncation'), false, false)); ?>
                                        </a>
                                    </span>
									</div>
									<div class="date-created">
										<span class="key"><?php echo number_format($item->hits); ?></span>
										<span class="value"><?php echo MText::_('COM_MIWOVIDEOS_VIEWS'); ?></span>
									</div>
								</div>
								<div class="video_description">
									<?php echo MHtmlString::truncate(htmlspecialchars_decode($item->introtext, ENT_QUOTES), $this->config->get('desc_truncation'), false, false); ?>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>
			<?php } ?>

			<input type="hidden" name="option" value="com_miwovideos"/>
			<input type="hidden" name="view" value="playlist"/>
			<input type="hidden" name="task" value=""/>
			<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>"/>
			<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>"/>
			<?php echo MHtml::_('form.token'); ?>
			<div class="clr"></div>
		</form>
		<!-- content // -->
	</div>
	<?php
	/*if ($this->pagination->total > $this->pagination->limit) {
	?>
		<tfoot>
			<tr>
				<td colspan="5">
					<div class="pagination">
						<?php echo $this->pagination->getListFooter(); ?>
					</div>
				</td>
			</tr>
		</tfoot>
	<?php
	}*/
	?>
</div>

<script type="text/javascript"><!--
	jQuery('#like_action,#dislike_action').click(function () {
		var id = jQuery(this).children().attr("id");
		var type = id.replace("like", "");
		jQuery.ajax({
			url : '<?php echo MURL_ADMIN; ?>/admin-ajax.php?action=miwovideos&view=playlist&task=checkLikesDislikes&format=raw&item_type=playlists&item_id=<?php echo $this->item->id; ?>',
			type: 'post',
			data: {type: type},
			dataType: 'json',
			success: function (json) {
				if (json['success']) {
					var count = jQuery('#miwovideos_like'+type).text();
					var oldtype = json['type'];
					if (json['type'] == -1) {
						jQuery.ajax({
							url : '<?php echo MURL_ADMIN; ?>/admin-ajax.php?action=miwovideos&view=playlist&task=likeDislikeItem&format=raw&item_type=playlists&item_id=<?php echo $this->item->id; ?>',
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
								url : '<?php echo MURL_ADMIN; ?>/admin-ajax.php?action=miwovideos&view=playlist&task=likeDislikeItem&format=raw&item_type=playlists&item_id=<?php echo $this->item->id; ?>',
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
								url : '<?php echo MURL_ADMIN; ?>/admin-ajax.php?action=miwovideos&view=playlist&task=likeDislikeItem&format=raw&item_type=playlists&item_id=<?php echo $this->item->id; ?>',
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
					} else {
						jQuery('#'+id+' #subscribe_button').hide();
						jQuery('#'+id+' #unsubscribe_button').show();
						subs.children().text(parseInt(count)+1);
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

	jQuery("#share_button").toggle(showPanel, hidePanel);
	function showPanel() {
		jQuery("#addthis_share").show();
	}
	function hidePanel() {
		jQuery("#addthis_share").hide();
	}

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
<script type="text/javascript">
	jQuery(document).ready(function () {
		var box_width = document.getElementById("adminForm").offsetWidth;
		var thumb_size = <?php echo $thumb_size; ?>;
		var thumb_percent = Math.round((thumb_size / box_width) * 100);
		var desc_percent = 100-thumb_percent-3;
		jQuery('div[class^="videos-items-list-box-content"]').css('width', desc_percent+'%');
		jQuery('div[class^="videos-list-item"]').css('width', thumb_percent+'%');
	});
</script>
<script type="text/javascript"><!--
	jQuery(".miwovideos_more_button").toggle(showPanel, hidePanel);
	function showPanel() {
		jQuery(".miwovideos_expander").show();
		jQuery('.miwovideos_expander_collapsed').hide();
		jQuery(".miwovideos_more_button").text('<?php echo MText::_('COM_MIWOVIDEOS_SHOW_LESS'); ?>')
	}
	function hidePanel() {
		jQuery(".miwovideos_expander").hide();
		jQuery('.miwovideos_expander_collapsed').show();
		jQuery(".miwovideos_more_button").text('<?php echo MText::_('COM_MIWOVIDEOS_SHOW_MORE'); ?>');
	}
	//--></script>