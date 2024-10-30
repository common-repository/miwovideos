<?php
/**
 * @package        MiwoVideos
 * @copyright      Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license        GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die;

MHtml::addIncludePath(MPATH_COMPONENT.'/helpers/html');
MHtml::_('behavior.tooltip');

$field     = MRequest::getCmd('field');
$function  = 'jSelectUser_'.$field;
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo MRoute::_('index.php?option=com_miwovideos&view=users&layout=modal&groups='.MRequest::getVar('groups', '', 'default', 'BASE64').'&excluded='.MRequest::getVar('excluded', '', 'default', 'BASE64')); ?>" method="post" name="adminForm" id="adminForm">
	<table width="100%">
		<tr>
			<td class="miwi_search">
				<?php echo MText::_('Filter'); ?>:
				<input type="text" name="search" id="search" value="<?php echo $this->lists['search']; ?>" class="text_area search-query" onchange="document.adminForm.submit();"/>
				<button onclick="this.form.submit();" class="button"><?php echo MText::_('Go'); ?></button>
				<button onclick="document.getElementById('search').value='';this.form.submit();" class="button"><?php echo MText::_('Reset'); ?></button>
				<button class="button" onclick="if (window.parent) window.parent.<?php echo $this->escape($function); ?>('', '<?php echo MText::_('MLIB_FORM_SELECT_USER') ?>');"><?php echo MText::_('MOPTION_NO_USER') ?></button>
			</td>
		</tr>
	</table>

	<table class="wp-list-table widefat">
		<thead>
		<tr>
			<th class="left">
				<?php echo MHtml::_('grid.sort', 'COM_MIWOVIDEOS_NAME', 'u.user_login', $this->lists['order_Dir'], $this->lists['order']); ?>
			</th>
			<th style="text-align: center" width="25%">
				<?php echo MHtml::_('grid.sort', 'MGLOBAL_USERNAME', 'u.display_name', $this->lists['order_Dir'], $this->lists['order']); ?>
			</th>
		</tr>
		</thead>
		<tfoot>
		<tr>
			<td colspan="15">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
		</tfoot>
		<tbody>
		<?php
		$k = 0;
		foreach ($this->items as $item) {
			?>
			<tr class="row<?php echo $k; ?>">
				<td>
					<a class="pointer" onclick="if (window.parent) window.parent.<?php echo $this->escape($function); ?>('<?php echo $item->id; ?>', '<?php echo $this->escape(addslashes($item->user_login)); ?>');">
						<?php echo $item->user_login; ?></a>
				</td>
				<td align="center">
					<?php echo $item->display_name; ?>
				</td>
			</tr>
			<?php $k = 1 - $k; ?>
		<?php } ?>
		</tbody>
	</table>
	<div>
		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="field" value="<?php echo $this->escape($field); ?>"/>
		<input type="hidden" name="boxchecked" value="0"/>
		<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>"/>
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>"/>
		<?php echo MHtml::_('form.token'); ?>
	</div>
</form>
