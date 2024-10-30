<?php

/**
 * @package        MiwoVideos
 * @copyright      Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license        GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die;

$page_title = $this->params->get('page_title', '');
if (($this->params->get('show_page_heading', '0') == '1')) {
	?>
	<?php $page_heading = $this->params->get('page_heading', ''); ?>
	<?php if (!empty($page_heading)) { ?>
		<h1><?php echo $page_heading; ?></h1>
	<?php }
	else if (!empty($page_title)) { ?>
		<h1><?php echo $page_title; ?></h1>
	<?php } ?>
<?php } ?>

<div class="miwovideos_box">
	<div class="miwovideos_box_heading">
		<h1 class="miwovideos_box_h1"><?php echo $page_title; ?></h1>
	</div>
	<div class="miwovideos_box_content">
		<!-- content -->
		<form method="post" name="adminForm" id="adminForm" action="<?php echo MRoute::_('index.php?option=com_miwovideos&view=favored'.$this->Itemid); ?>">
			<div class="miwovideos_subheader">
				<div class="miwovideos_searchbox">
					<input type="text" name="miwovideos_search" id="miwovideos_search" placeholder="Search..." value="<?php echo empty($this->lists['search']) ? '' : $this->lists['search']; ?>" onchange="document.adminForm.submit();"/>
				</div>
				<div class="miwovideos_flow_select">
					<?php  $grid = $list = '';
					if (strpos($this->display, 'grid') !== false) {
						$grid = 'active';
					}
					else {
						$list = 'active';
					} ?>
					<a class="<?php echo MiwoVideos::getButtonClass(); ?> <?php echo $grid; ?>" href="<?php echo MRoute::_('index.php?option=com_miwovideos&view=favored&display=grid'.$this->Itemid); ?>" title="<?php echo MText::_('COM_MIWOVIDEOS_GRID'); ?>"><?php echo MText::_('COM_MIWOVIDEOS_GRID'); ?></a>
					<a class="<?php echo MiwoVideos::getButtonClass(); ?> <?php echo $list; ?>" href="<?php echo MRoute::_('index.php?option=com_miwovideos&view=favored&display=list'.$this->Itemid); ?>" title="<?php echo MText::_('COM_MIWOVIDEOS_LIST'); ?>"><?php echo MText::_('COM_MIWOVIDEOS_LIST'); ?></a>
				</div>
			</div>
			<input type="hidden" name="option" value="com_miwovideos"/>
			<input type="hidden" name="view" value="favored"/>
			<input type="hidden" name="task" value=""/>
			<?php echo MHtml::_('form.token'); ?>
			<div class="clr"></div>
			<div id="video_items">
				<?php echo $this->loadTemplate($this->display); ?>
				<?php if ($this->pagination->total > $this->pagination->limit) { ?>
					<tfoot>
					<tr>
						<td colspan="5">
							<div class="pagination">
								<?php echo $this->pagination->getListFooter(); ?>
							</div>
						</td>
					</tr>
					</tfoot>
				<?php } ?>
			</div>
		</form>
		<!-- content // -->
	</div>
</div>