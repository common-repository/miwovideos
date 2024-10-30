<?php
/**
 * @package        MiwoVideos
 * @copyright      Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license        GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die; ?>
	<form method="post" name="submitReport" id="submitReport" action="<?php echo MRoute::_('index.php?option=com_miwovideos&view=video&task=submitReport'); ?>">
		<div class="report_notification"></div>
		<div class="miwovideos_report">
			<?php if ($this->report) { ?>
				<div class="miwovideos_report_success"><?php echo MText::_('COM_MIWOVIDEOS_ALREADY_REPORT'); ?></div>
				<div class="miwovideos_report_text">
					<div style="font-weight: bold"><?php echo MText::_('COM_MIWOVIDEOS_ISSUE_REPORTED'); ?></div>
					<p id="miwovideos_reasons"><?php echo $this->report->title; ?></p>

					<div style="font-weight: bold"><?php echo MText::_('COM_MIWOVIDEOS_ADDITIONAL_DETAILS'); ?></div>
					<p><?php echo $this->report->note; ?></p>
				</div>
			<?php
			}
			else {
				?>
				<?php echo $this->lists['reasons']; ?><br>
				<?php $i = 0;
				foreach ($this->reasons as $reason) {
					$i++; ?>
					<div class="miwovideos_report_description<?php echo $i; ?> options_vp" style="display: none">
						<?php echo $reason->description; ?>
					</div>
				<?php } ?>
				<div class="miwovideos_report_explanation">
					<textarea class="miwovideos_report_text" name="miwovideos_report" id="miwovideos_report" cols="40" rows="3" class="" aria-invalid="false"></textarea>
				</div>
				<a class="<?php echo MiwoVideos::getButtonClass(); ?>" id="miwovideos_button">
					<?php echo MText::_('COM_MIWOVIDEOS_SUBMIT'); ?>
				</a>
				<input type="hidden" name="item_id" value="<?php echo $this->item->id; ?>"/>
				<input type="hidden" name="item_type" value="videos"/>
				<?php echo MHtml::_('form.token'); ?>
			<?php } ?>
		</div>
	</form>
<?php if (!$this->report) { ?>
	<script type="text/javascript"><!--
		jQuery('#miwovideos_button').click(function () {
			var postdata = jQuery('#submitReport').serialize();
			jQuery.ajax({
				url : '<?php echo MURL_ADMIN; ?>/admin-ajax.php?action=miwovideos&view=video&task=submitReport&format=raw',
				type: 'post',
				data: postdata,
				dataType: 'json',
				success: function (json) {
					if (json['success']) {
						val = jQuery('#miwovideos_reasons option:selected').text();
						jQuery('.miwovideos_report_explanation,#miwovideos_button,.miwovideos_report').remove();
						jQuery('.report_notification').html(json['success']);
						jQuery('#miwovideos_reasons').text(val);
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
		//--></script>
	<script type="text/javascript"><!--
		jQuery("#miwovideos_reasons").change(function () {
			var val = jQuery(this).val();
			jQuery(".options_vp").hide();
			jQuery('.miwovideos_report_description'+val).show();
		});
	//--></script>
<?php } ?>