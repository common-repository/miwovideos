<?php
/**
 * @package        MiwoVideos
 * @copyright      Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license        GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die; ?>
<form action="<?php echo MRoute::getActiveUrl(); ?>" method="post" name="adminForm" id="adminForm" class="form form-horizontal">
	<fieldset class="adminform">
		<legend><?php echo MText::_('COM_MIWOVIDEOS_PROCESS_LOG'); ?></legend>
		<div class="clr"></div>
		<p><?php echo MText::sprintf('COM_MIWOVIDEOS_GENERATE_X_FOR_MEDIA_X', '<strong>'.$this->item->title.'</strong>', '<a href="'.MiwoVideos::get('utility')->route('index.php?option=com_miwovideos&view=videos&task=edit&cid[]='.$this->item->video_id).'"><strong>'.$this->item->video_title.'</strong></a>'); ?></p>
		<?php if (count($this->logs) == 0) { ?>
			<p><?php echo MText::_('COM_MIWOVIDEOS_THE_LOG_IS_EMPTY'); ?></p>
		<?php } else { ?>
			<ul class="panelform">
				<?php echo MHtml::_('sliders.start', 'miwovideos-slider'); ?>
				<?php foreach ($this->logs as $log) {
					switch ($log->status) {
						case 1:
							$status = MText::_('COM_MIWOVIDEOS_SUCCESSFUL');
							break;
						case 2:
							$status = MText::_('COM_MIWOVIDEOS_FAILED');
							break;
						case 3:
							$status = MText::_('COM_MIWOVIDEOS_PROCESSING');
							break;
						default: // 0
							$status = MText::_('COM_MIWOVIDEOS_QUEUED');
							break;
					}
				?>
					<?php echo MHtml::_('sliders.panel', MText::sprintf('%s - %s', MHtml::_('date', $log->created, MText::_('DATE_FORMAT_LC2')), $status), 'publishing'); ?>
					<fieldset class="adminform">
						<ul class="panelform">
							<li>
								<label title="" class="hasTip" for="mform_input" id="mform_input-lbl"><?php echo MText::_('COM_MIWOVIDEOS_INPUT'); ?></label>
								<textarea style="width: 100%; height: 100px;" rows="0" cols="0" id="mform_input" name="mform[input]"><?php echo $log->input; ?></textarea>
							</li>
							<li>
								<label title="" class="hasTip" for="mform_output" id="mform_output-lbl"><?php echo MText::_('COM_MIWOVIDEOS_OUTPUT'); ?></label>
								<textarea style="width: 100%; height: 100px;" rows="0" cols="0" id="mform_output" name="mform[output]"><?php echo $log->output; ?></textarea>
							</li>
						</ul>
					</fieldset>
				<?php } ?>
				<?php echo MHtml::_('sliders.end'); ?>

			</ul>
		<?php } ?>
	</fieldset>
	<div>
		<input type="hidden" name="task" value=""/>
		<?php echo MHtml::_('form.token'); ?>
	</div>
</form>

