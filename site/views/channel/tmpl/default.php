<?php
/**
 * @package        MiwoVideos
 * @copyright      Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license        GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die;
$url     = MRoute::_('index.php?option=com_miwovideos&view=channel&channel_id='.$this->item->id.$this->Itemid);
$utility = MiwoVideos::get('utility');
?>
<?php if (($this->params->get('show_page_heading', '0') == '1')) { ?>
	<?php $page_heading = $this->params->get('page_heading', ''); ?>
	<?php if (!empty($page_heading)) { ?>
		<h1><?php echo $page_heading; ?></h1>
	<?php }
	else if (!empty($this->item->title)) { ?>
		<h1><?php echo $this->item->title; ?></h1>
	<?php } ?>
<?php } ?>
<div id="notification"></div>
<div class="miwovideos_box">
	<div class="miwovideos_box_heading">
		<h1 class="miwovideos_box_h1"><?php echo $this->item->title; ?></h1>
	</div>

	<div class="miwovideos_box_content">
		<!-- content -->
		<?php if ($this->item->banner and file_exists(MIWOVIDEOS_UPLOAD_DIR.'/images/channels/'.$this->item->id.'/banner/thumb/'.$this->item->banner)) {
			$background_image = "url(".MURL_MEDIA."/miwovideos/images/channels/".$this->item->id."/banner/thumb/".$this->item->banner.") no-repeat;";
		}
		else {
			$background_image = "";
		} ?>
		<div class="banner_image" style="background: <?php echo $background_image; ?>; background-repeat: round;">
			<a href="<?php echo $url; ?>">
				<img class="channel-items-list-thumb" src="<?php echo $utility->getThumbPath($this->item->id, 'channels', $this->item->thumb); ?>" title="<?php echo $this->item->title; ?>" alt="<?php echo $this->item->title; ?>"/>
			</a>
		</div>
		<div class="miwovideos_channel_header">
			<div class="miwovideos_channel_title">
				<h1>
					<a href="<?php echo MRoute::_('index.php?option=com_miwovideos&view=channel&channel_id='.$this->item->id.$this->Itemid); ?>"><?php echo $this->item->title; ?></a>
				</h1>
			</div>
			<?php if (MiwoVideos::get('channels')->getDefaultChannel()->id == $this->item->id or $this->item->share_others) { ?>
				<span class="miwovideos_upload">
                    <a href="<?php echo MRoute::_('index.php?option=com_miwovideos&view=upload&channel_id='.$this->item->id.'&dashboard=1'); ?>" class="<?php echo MiwoVideos::getButtonClass(); ?>">
	                    <?php echo MText::_('COM_MIWOVIDEOS_UPLOAD'); ?>
                    </a>
                </span>
			<?php } ?>
			<?php if ($this->config->get('subscriptions')) { ?>
				<div class="miwovideos_subscribe" id="<?php echo $this->item->id ?>">
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
					<div class="miwovideos_subscribe_count" id="subs_count<?php echo $this->item->id ?>">
						<span class="subs_count"><?php echo number_format($this->item->subs); ?></span></div>
					<div class="subs_nub"><s></s><i></i></div>
				</div>
			<?php } ?>
		</div>
		<form method="post" name="adminForm" id="adminForm" action="<?php echo MRoute::_('index.php?option=com_miwovideos&channel_id='.$this->item->id.'&view=channel'.$this->Itemid); ?>">
			<div class="miwovideos_searchbox">
				<input type="text" name="miwovideos_search" id="miwovideos_search" placeholder="Search..." value="<?php echo empty($this->lists['search']) ? "" : $this->lists['search']; ?>" onchange="document.adminForm.submit();"/>
			</div>
			<?php echo MHtml::_('tabs.start', 'left', array('useCookie' => 1)); ?>
			<?php echo MHtml::_('tabs.panel', MText::_('COM_MIWOVIDEOS_VIDEOS'), 'sl_videos'); ?>

			<div class="miwovideos_subheader">
				<div class="miwovideos_filter_videos"><?php echo $this->lists['filter_videos']; ?></div>
				<div class="miwovideos_filter_videos"><?php echo $this->lists['order']; ?></div>
				<div class="miwovideos_flow_select">
					<?php  $grid = $list = '';
					if (strpos($this->display, 'grid') !== false) {
						$grid = 'active';
					}
					else {
						$list = 'active';
					} ?>
					<a class="<?php echo MiwoVideos::getButtonClass(); ?> <?php echo $grid; ?>" href="<?php echo MRoute::_('index.php?option=com_miwovideos&view=channel&channel_id='.$this->item->id.'&display=grid'.$this->Itemid); ?>" title="<?php echo MText::_('COM_MIWOVIDEOS_GRID'); ?>"><?php echo MText::_('COM_MIWOVIDEOS_GRID'); ?></a>
					<a class="<?php echo MiwoVideos::getButtonClass(); ?> <?php echo $list; ?>" href="<?php echo MRoute::_('index.php?option=com_miwovideos&view=channel&channel_id='.$this->item->id.'&display=list'.$this->Itemid); ?>" title="<?php echo MText::_('COM_MIWOVIDEOS_LIST'); ?>"><?php echo MText::_('COM_MIWOVIDEOS_LIST'); ?></a>
				</div>
			</div>

			<input type="hidden" name="option" value="com_miwovideos"/>
			<input type="hidden" name="view" value="channel"/>
			<input type="hidden" name="task" value=""/>
			<input type="hidden" name="channel_id" value="<?php echo $this->item->id; ?>"/>
			<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>"/>
			<?php echo MHtml::_('form.token'); ?>
			<div class="clr"></div>
			<div id="channel_items">
				<?php echo $this->loadTemplate($this->display); ?>
			</div>
			<?php if ($this->total_items > $this->config->get('videos_per_page')) { ?>
				<div class="video_more">
					<button type="button" id="loadmore" class="video_more_buttons"><?php echo MText::_('COM_MIWOVIDEOS_LOAD_MORE'); ?></button>
					<input id="pages" type="hidden" value="<?php echo $this->total_pages; ?>">
				</div>
			<?php } ?>
		</form>
		<?php if ($this->config->get('comments') != "0") { ?>
			<?php echo MHtml::_('tabs.panel', MText::_('COM_MIWOVIDEOS_COMMENTS'), 'sl_comments'); ?>
			<?php MiwoVideos::get('utility')->trigger('getComments', array($this->item->id, $this->item->title)); ?>
		<?php } ?>
		<?php echo MHtml::_('tabs.panel', MText::_('COM_MIWOVIDEOS_DESCRIPTION'), 'sl_description'); ?>
		<div class="miwovideos_description"><?php echo html_entity_decode($this->item->description, ENT_QUOTES); ?></div>
		<div class="miwovideos_description_stats">
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
			<?php if ($this->config->get('subscriptions')) { ?>
				<div class="title"><?php echo number_format($this->item->subs); ?></div> <?php echo $this->item->subs > 1 ? MText::_('COM_MIWOVIDEOS_SUBSCRIBERS') : MText::_('COM_MIWOVIDEOS_SUBSCRIBER'); ?>
				<br>
			<?php } ?>
			<div class="title"><?php echo number_format($this->item->hits); ?></div> <?php echo $this->item->hits > 1 ? MText::_('COM_MIWOVIDEOS_VIEW') : MText::_('COM_MIWOVIDEOS_VIEWS'); ?>
			<br>

			<div class="content"><?php echo MText::_('COM_MIWOVIDEOS_JOINED'); ?> <?php echo MHtml::_('date', $this->item->created, MText::_('DATE_FORMAT_LC4')); ?></div>
		</div>
		<!-- content // -->
	</div>
</div>
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
<script>
	jQuery(function () {
		var page = 1;
		var pages = jQuery("#pages").val(); //TOTAL NUMBER OF PAGES

		//WHEN THE 'LOAD MORE' BUTTON IS PRESSED
		jQuery("#loadmore").live("click", function () {
			var next = page += 1;

			jQuery.ajax({
				url : '<?php echo MURL_ADMIN; ?>/admin-ajax.php?action=miwovideos&view=channel&format=raw&channel_id=<?php echo $this->item->id; ?>',
				type: 'post',
				data: 'page='+next,
				dataType: 'json',
				success: function (json) {
					if (next == pages) {
						jQuery("#loadmore").remove();
					}
					jQuery("#channel_items").append(json);
				}
			});
		});
	});
</script>
<script>
	function miwovideossubmit() {
		var form = document.getElementById("adminForm");
		var filter_order = jQuery('#filter_order');
		var filter_videos = jQuery('#filter_videos');
		var selected = filter_order.val();
		if (selected.indexOf('_asc') > -1) {
			jQuery("input[name*='filter_order_Dir']").val('ASC');
			selected = selected.replace('_asc', '');
		}
		else {
			jQuery("input[name*='filter_order_Dir']").val('DESC');
			selected = selected.replace('_desc', '');
		}
		if (filter_videos.val() == 'playlists') {
			selected = selected.replace('v.', 'p.');
			filter_order.children('option[value="'+filter_order.val()+'"]').val(selected);
		}
		else {
			selected = selected.replace('p.', 'v.');
			filter_order.children('option[value="'+filter_order.val()+'"]').val(selected);
		}
		form.submit();
	}
</script>