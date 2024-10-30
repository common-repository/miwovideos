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

$utility = MiwoVideos::get('utility');
//$listOrder	= $this->escape($this->state->get('list.ordering'));
//$listDirn	= $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo MiwoVideos::get('utility')->route('index.php?option=com_miwovideos&view=videos&layout=modal&tmpl=component&groups='.MRequest::getVar('groups', '', 'default', 'BASE64').'&excluded='.MRequest::getVar('excluded', '', 'default', 'BASE64')); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset class="filter">
		<div class="left">
			<label for="filter_search"><?php echo MText::_('MSEARCH_FILTER'); ?></label>
			<input type="text" name="search" id="search" value="<?php echo $this->lists['search']; ?>" class="text_area search-query" onchange="document.adminForm.submit();"/>
			<button onclick="this.form.submit();" class="button"><?php echo MText::_('Go'); ?></button>
			<button onclick="document.getElementById('search').value='';this.form.submit();" class="button"><?php echo MText::_('Reset'); ?></button>
				<div id="editcell">
		<table class="wp-list-table widefat">
			<thead>
			<tr>
								<th width="20px">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo MText::_('MGLOBAL_CHECK_ALL'); ?>" onclick="Miwi.checkAll(this)"/>
				</th>
				<th width="80px" style="text-align: center;">
					<?php echo MText::_('COM_MIWOVIDEOS_THUMB'); ?>
				</th>
				<th class="title" style="text-align: left;">
					<?php echo MHtml::_('grid.sort', MText::_('COM_MIWOVIDEOS_TITLE'), 'v.title', $this->lists['order_Dir'], $this->lists['order']); ?>
				</th>
				<?php if ($this->acl->canManage()) { ?>
					<th width="18%" style="text-align: left;">
						<?php echo MText::_('COM_MIWOVIDEOS_CATEGORY'); ?>
					</th>
				<?php } ?>
				<th width="7%">
					<?php echo MText::_('COM_MIWOVIDEOS_CHANNEL'); ?>
				</th>
				<th width="5%">
					<?php echo MHtml::_('grid.sort', MText::_('COM_MIWOVIDEOS_PUBLISHED'), 'v.published', $this->lists['order_Dir'], $this->lists['order']); ?>
				</th>
				<?php if ($this->acl->canManage()) { ?>
					<th width="6%">
						<?php echo MHtml::_('grid.sort', MText::_('COM_MIWOVIDEOS_FEATURE'), 'v.featured', $this->lists['order_Dir'], $this->lists['order']); ?>
					</th>
				<?php } ?>
								<th width="90px" style="text-align: center;">
					<?php echo MHtml::_('grid.sort', MText::_('COM_MIWOVIDEOS_DATE_CREATED'), 'v.created', $this->lists['order_Dir'], $this->lists['order']); ?>
				</th>
				<?php if ($this->acl->canManage()) { ?>
					<th width="5%">
						<?php echo MHtml::_('grid.sort', 'MGRID_HEADING_LANGUAGE', 'v.language', $this->lists['order_Dir'], $this->lists['order']); ?>
					</th>
				<?php } ?>
				<?php if ($this->acl->canManage()) { ?>
					<th width="5px">
						<?php echo MHtml::_('grid.sort', MText::_('COM_MIWOVIDEOS_ID'), 'v.id', $this->lists['order_Dir'], $this->lists['order']); ?>
					</th>
				<?php } ?>
			</tr>
			</thead>
			<tfoot>
			<tr>
				<td colspan="11">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
			</tfoot>
			<tbody>
			<?php
			$i      = $k = 0;
			$config = MiwoVideos::getConfig();
			foreach ($this->get('LinkedVideos') as $row) {
				$link         = $utility->route('index.php?option=com_miwovideos&view=videos&task=edit&cid[]='.$row->id);
				$channel_link = $utility->route('index.php?option=com_miwovideos&view=channels&task=edit&cid[]='.$row->channel_id);
				$checked      = MHtml::_('grid.id', $i, $row->id);
				$img          = $row->published ? 'publish_y.png' : 'publish_x.png';
				$alt          = $row->published ? MText::_('MPUBLISHED') : MText::_('MUNPUBLISHED');
				$published    = MHtml::_('image', MURL_MIWOVIDEOS.'/admin/assets/images/'.$img, $alt, null, true);
				$img          = $row->featured ? 'featured.png' : 'disabled.png';
				$featured     = MHtml::_('image', MURL_MIWOVIDEOS.'/admin/assets/images/'.$img, '', null, true);
				?>
				<tr class="<?php echo "row$k"; ?>">					<td style="vertical-align: middle">
						<?php echo $checked; ?>
					</td>
					<td style="vertical-align: middle">
						<a href="<?php echo $utility->getThumbPath($row->id, 'videos', $row->thumb); ?>" class="modal"><img src="<?php echo $utility->getThumbPath($row->id, 'videos', $row->thumb); ?>" class="img_preview_list"/></a>
					</td>
					<td style="vertical-align: middle">
						<?php if ($this->acl->canEditOwn($row->user_id)) { ?>
							<a href="<?php echo $link; ?>">
								<?php echo $row->title; ?>
							</a>
						<?php
						}
						else {
							?>
							<?php echo $row->title; ?>
						<?php } ?>
					</td>
					<?php if ($this->acl->canManage()) { ?>
						<td style="vertical-align: middle">
							<?php echo $row->categories; ?>
						</td>
					<?php } ?>
					<td style="vertical-align: middle">
						<?php if ($this->acl->canEditOwn($row->user_id)) { ?>
							<a href="<?php echo $channel_link; ?>">
								<?php echo $row->channel_title; ?>
							</a>
						<?php
						}
						else {
							?>
							<?php echo $row->channel_title; ?>
						<?php } ?>
					</td>
					<td class="text_center" style="vertical-align: middle">
						<?php echo $published; ?>
					</td>
					<?php if ($this->acl->canManage()) { ?>
						<td class="text_center" style="vertical-align: middle">
							<?php echo $featured; ?>
						</td>
					<?php } ?>
										<td style="text-align: center; vertical-align: middle;">
						<?php echo MHtml::_('date', $row->created, MText::_('DATE_FORMAT_LC4')); ?>
					</td>
					<?php if ($this->acl->canManage()) { ?>
						<td class="center nowrap" style="vertical-align: middle">
							<?php if ($row->language == '*') { ?>
								<?php echo MText::alt('MALL', 'language'); ?>
							<?php
							}
							else {
								?>
								<?php echo isset($this->langs[ $row->language ]->title) ? $this->escape($this->langs[ $row->language ]->title) : MText::_('MUNDEFINED'); ?>
							<?php } ?>
						</td>
					<?php } ?>
					<?php if ($this->acl->canManage()) { ?>
						<td class="text_center" style="vertical-align: middle">
							<?php echo $row->id; ?>
						</td>
					<?php } ?>
				</tr>
				<?php
				$k = 1 - $k;
				$i++;
			}
			?>
			</tbody>
		</table>
	</div>

	<input type="hidden" name="option" value="com_miwovideos"/>
	<input type="hidden" name="view" value="videos"/>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="playlist_id" value="<?php echo MRequest::getInt('playlist_id'); ?>"/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>"/>
	<?php echo MHtml::_('form.token'); ?>
</form>
