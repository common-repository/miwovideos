<?php
/**
 * @package        MiwoVideos
 * @copyright      Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license        GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die; ?>
<?php if (count($this->playlistitems)) { ?>
	<div class="playlist_notification"></div>
	<h4><?php echo MText::_('COM_MIWOVIDEOS_ADD_TO_PLAYLIST'); ?></h4>
	<div class="miwovideos_playlist_addto_filter">
		<input type="checkbox" class="miwovideos_checkbox" name="add_to_top"/>&nbsp;<span><?php echo MText::_('COM_MIWOVIDEOS_ADD_TO_TOP'); ?></span>
		<?php echo $this->lists['filter_order']; ?>
	</div>
	<div class="miwovideos_playlist_items">
		<?php foreach ($this->playlistitems as $item) { ?>
			<?php $playlist_videos = array();
			foreach ($item->videos as $video) {
				$playlist_videos[] = $video->video_id;
			} ?>
			<li class="miwovideos_playlist_item">
				<a class="playlist_item" id="playlist_item<?php echo $item->id; ?>">
					<img src="<?php echo MURL_MIWOVIDEOS; ?>/site/assets/images/tick.png" style="<?php if (!in_array($this->item->id, $playlist_videos)) { ?>visibility: hidden<?php } ?>"/>
					<span class="miwovideos_playlist_title" id="<?php if ($item->type == 1) {
						echo "type1";
					} ?>"><?php echo $item->title; ?>&nbsp;(<span id="total_videos"><?php echo $item->total; ?></span>)</span>
					<span class="miwovideos_playlist_access"><?php echo $this->levels[ $item->access ]->title; ?></span>
					<span class="miwovideos_playlist_created"><?php echo MiwoVideos::agoDateFormat($item->created); ?></span>
				</a>
			</li>
		<?php } ?>
	</div>
<?php } ?>
<form method="post" name="adminForm" id="adminForm" action="<?php echo MRoute::_('index.php?option=com_miwovideos&task=save'); ?>">
	<div class="miwovideos_create_playlist">
		<input type="text" class="miwovideos_playlist_name" value="" name="playlist_title" placeholder="<?php echo MText::_('COM_MIWOVIDEOS_ENTER_PLAYLIST_NAME'); ?>"/>

		<div class="miwovideos_playlist_actions">
			
			<a class="<?php echo MiwoVideos::getButtonClass(); ?>" id="create_playlist"><?php echo MText::_('COM_MIWOVIDEOS_CREATE_PLAYLIST'); ?></a>
		</div>
	</div>
	<?php echo MHtml::_('form.token'); ?>
</form>
<script type="text/javascript"><!--
	function ajaxOrder() {
		var filter_order = jQuery('#filter_order');
		var selected = filter_order.val();
		if (selected.indexOf('_asc') > -1) {
			filter_order_Dir = 'ASC';
			selected = selected.replace('_asc', '');
		}
		else {
			filter_order_Dir = 'DESC';
			selected = selected.replace('_desc', '');
		}
		jQuery.ajax({
			url : '<?php echo MURL_ADMIN; ?>/admin-ajax.php?action=miwovideos&view=video&task=ajaxOrder&format=raw&video_id=<?php echo $this->item->id; ?>',
			type: 'post',
			data: {filter_videos: "playlists", filter_order: selected, filter_order_Dir: filter_order_Dir},
			dataType: 'json',
			success: function (json) {
				if (json['html']) {
					jQuery('.miwovideos_playlist_items').html(json['html']);
				}
				if (json['redirect']) {
					location = json['redirect'];
				}
			}
		});
	}
	//--></script>
<script type="text/javascript"><!--
	jQuery('#create_playlist').click(function () {
		var token = jQuery("#adminForm input[type='hidden']")[0].name;
		var tokenval = jQuery("#adminForm input[type='hidden']")[0].value;
		var playlist_title = jQuery('.miwovideos_playlist_name').val();
		if (!playlist_title) {
			jQuery('.miwovideos_playlist_name').css({"border-color": "rgba(255, 0, 0, 0.8)",
													"outline": "0",
													"outline": "thin dotted \9",
													"-webkit-box-shadow": "rgba(0, 0, 0, 0.0745098) 0px 1px 1px inset, rgba(255, 0, 0, 0.6) 0px 0px 8px",
													"-moz-box-shadow": "inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(255, 0, 0, 0.6)",
													"box-shadow": "rgba(0, 0, 0, 0.0745098) 0px 1px 1px inset, rgba(255, 0, 0, 0.6) 0px 0px 8px"
			});
			return;
		}
		var access = 1;
		jQuery.ajax({
			url : '<?php echo MURL_ADMIN; ?>/admin-ajax.php?action=miwovideos&view=playlists&task=save&format=raw',
			type: 'post',
			data: 'title='+playlist_title+'&access='+access+'&'+token+'='+tokenval,
			dataType: 'json',
			success: function (json) {
				if (json['success']) {
					html = '<li class="miwovideos_playlist_item">';
					html += '   <a class="playlist_item" id="playlist_item'+json['id']+'">';
					html += '       <img src="<?php echo MURL_MIWOVIDEOS; ?>/site/assets/images/tick.png" style="visibility: hidden"/>';
					html += '       <span class="miwovideos_playlist_title">'+playlist_title+'&nbsp;(<span id="total_videos">0</span>)</span>';
					
					html += '       <span class="miwovideos_playlist_created">-</span>';
					html += '   </a>';
					html += '</li>';
					jQuery('.miwovideos_playlist_items').append(html);
					var id = json['id'];
					var ordering = jQuery('.miwovideos_checkbox:checked').val();
					var total = jQuery('#playlist_item'+id).find('.miwovideos_playlist_title').find('#total_videos').text();
					jQuery.ajax({
						url : '<?php echo MURL_ADMIN; ?>/admin-ajax.php?action=miwovideos&view=playlists&task=addVideoToPlaylist&format=raw',
						type: 'post',
						data: {playlist_id: id, video_id: <?php echo $this->item->id; ?>, ordering: ordering},
						dataType: 'json',
						success: function (json) {
							if (json['success']) {
								jQuery('#playlist_item'+id).find("img").css('visibility', 'visible');
								jQuery('#playlist_item'+id).find('.miwovideos_playlist_title').find('#total_videos').text(parseInt(total)+1);
							}
							if (json['redirect']) {
								location = json['redirect'];
							}
							if (json['error']) {
								jQuery('.playlist_notification').html('<div class="miwovideos_warning" style="display: none;">'+json['error']+'</div>');
								jQuery('.miwovideos_warning').fadeIn('slow');
								jQuery('.miwovideos_warning').delay(5000).fadeOut('slow');
							}
						}

					});
				}
				if (json['redirect']) {
					location = json['redirect'];
				}
				if (json['error']) {
					jQuery('#notification').html('<div class="miwovideos_warning" style="display: none;">'+json['error']+'</div>');
					jQuery('.miwovideos_warning').fadeIn('slow');
					jQuery('.miwovideos_warning').delay(5000).fadeOut('slow');
				}
			}
		});
	});

	jQuery('.miwovideos_playlist_name').on('focus', function () {
		jQuery('.miwovideos_playlist_name').removeAttr('style');
	});

	//--></script>
<script type="text/javascript"><!--
	jQuery('.playlist_item').on('click', function () {
		var id = this.id.replace("playlist_item", "");
		var ordering = jQuery('.miwovideos_checkbox:checked').val();
		var type = jQuery(this).find('.miwovideos_playlist_title').attr('id');
		var total = jQuery(this).find('.miwovideos_playlist_title').find('#total_videos').text();
		if (jQuery(this).children('img')[0].style['visibility'] !== 'hidden') {
			jQuery.ajax({
				url : '<?php echo MURL_ADMIN; ?>/admin-ajax.php?action=miwovideos&view=playlists&task=removeVideoFromPlaylist&format=raw',
				type: 'post',
				data: {playlist_id: id, video_id: <?php echo $this->item->id; ?>, type: type},
				dataType: 'json',
				success: function (json) {
					if (json['success']) {
						jQuery('#playlist_item'+id).find("img").css('visibility', 'hidden');
						jQuery('#playlist_item'+id).find('.miwovideos_playlist_title').find('#total_videos').text(parseInt(total)-1);
						jQuery('.playlist_notification').html('<div class="miwovideos_success" style="display: none;">'+json['success']+'</div>');
						jQuery('.miwovideos_success').fadeIn('slow');
						jQuery('.miwovideos_success').delay(5000).fadeOut('slow');
					}
					if (json['redirect']) {
						location = json['redirect'];
					}
					if (json['error']) {
						jQuery('.playlist_notification').html('<div class="miwovideos_warning" style="display: none;">'+json['error']+'</div>');
						jQuery('.miwovideos_warning').fadeIn('slow');
						jQuery('.miwovideos_warning').delay(5000).fadeOut('slow');
					}
				}

			});
		} else {
			jQuery.ajax({
				url : '<?php echo MURL_ADMIN; ?>/admin-ajax.php?action=miwovideos&view=playlists&task=addVideoToPlaylist&format=raw',
				type: 'post',
				data: {playlist_id: id, video_id: <?php echo $this->item->id; ?>, ordering: ordering, type: type},
				dataType: 'json',
				success: function (json) {
					if (json['success']) {
						jQuery('#playlist_item'+id).find("img").css('visibility', 'visible');
						jQuery('#playlist_item'+id).find('.miwovideos_playlist_title').find('#total_videos').text(parseInt(total)+1);
						jQuery('.playlist_notification').html('<div class="miwovideos_success" style="display: none;">'+json['success']+'</div>');
						jQuery('.miwovideos_success').fadeIn('slow');
						jQuery('.miwovideos_success').delay(5000).fadeOut('slow');
					}
					if (json['redirect']) {
						location = json['redirect'];
					}
					if (json['error']) {
						jQuery('.playlist_notification').html('<div class="miwovideos_warning" style="display: none;">'+json['error']+'</div>');
						jQuery('.miwovideos_warning').fadeIn('slow');
						jQuery('.miwovideos_warning').delay(5000).fadeOut('slow');
					}
				}

			});
		}

	});
	//--></script>