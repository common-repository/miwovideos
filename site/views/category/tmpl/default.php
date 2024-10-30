<?php
/**
 * @package        MiwoVideos
 * @copyright      Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license        GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die;

$utility    = MiwoVideos::get('utility');
$page_title = $this->params->get('page_title', '');
if (($this->params->get('show_page_heading', '0') == '1')) { ?>
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
		<div class="miwovideos_cat">
			<img class="category-item-thumb80" src="<?php echo $utility->getThumbPath($this->category->id, 'categories', $this->category->thumb); ?>" title="<?php echo $this->category->title; ?>" alt="<?php echo $this->category->title; ?>"/>
			<?php if (!empty($this->category->introtext) or !empty($this->category->fulltext)) { ?>
				<div class="miwi_description"><?php echo html_entity_decode($this->category->introtext.$this->category->fulltext, ENT_QUOTES); ?></div>
			<?php } ?>
		</div>
		<div class="clr"></div>
		<?php if (!empty($this->categories)) { ?>
			<div class="miwovideos_box">
				<div class="miwovideos_box_heading">
					<h2 class="miwovideos_box_h1"><?php echo MText::_('COM_MIWOVIDEOS_SUB_CATEGORIES'); ?></h2>
				</div>
				<div class="miwovideos_box_content">
					<?php foreach ($this->categories as $category) {
						$thumb_size = $utility->getThumbSize($this->config->get('thumb_size'));
						$Itemid     = MiwoVideos::get('router')->getItemid(array('view' => 'category', 'category_id' => $category->id), null, true);
						$url        = MRoute::_('index.php?option=com_miwovideos&view=category&category_id='.$category->id.$Itemid);
						$thumb      = $utility->getThumbPath($category->id, 'categories', $category->thumb); ?>
						<div class="videos-items-list-box">
							<div class="playlists-list-item" style="width: <?php echo $thumb_size; ?>px">
								<div class="videos-aspect<?php echo $this->config->get('thumb_aspect'); ?>"></div>
								<a href="<?php echo $url; ?>">
									<img class="videos-items-grid-thumb" src="<?php echo $thumb; ?>" alt="<?php echo $category->thumb; ?>"/>
								</a>
							</div>
							<div class="playlists-items-list-box-content">
								<h3 class="miwovideos_box_h3">
									<a href="<?php echo $url; ?>" title="<?php echo $category->title; ?>">
										<?php echo $this->escape(MHtmlString::truncate($category->title, $this->config->get('title_truncation'), false, false)); ?>
									</a>
								</h3>

								<div class="playlists-meta">
									<div class="miwovideos-meta-info">
										<?php if ($this->config->get('show_number_videos')) { ?>
											<div class="created_by">(<?php echo $category->total_videos; ?> <?php echo $category->total_videos > 1 ? MText::_('COM_MIWOVIDEOS_VIDEOS') : MText::_('COM_MIWOVIDEOS_VIDEO'); ?>)</div>
										<?php } ?>
										<div class="date-created">
											<span class="value"><?php echo MiwoVideos::agoDateFormat($category->created); ?></span>
										</div>
									</div>
								</div>
								<?php if (!empty($category->introtext)) { ?>
									<div class="playlists-items">
										<?php echo MHtmlString::truncate(html_entity_decode($category->introtext, ENT_QUOTES), $this->config->get('desc_truncation'), false, false); ?>
									</div>
								<?php } ?>
							</div>
						</div>
					<?php } ?>
				</div>
				<script type="text/javascript">
					jQuery(document).ready(function () {
						var box_width = document.getElementById("adminForm").offsetWidth;
						var thumb_size = <?php echo $thumb_size; ?>;
						var thumb_percent = Math.round((thumb_size / box_width) * 100);
						var desc_percent = 100-thumb_percent-3;
						jQuery('div[class^="playlists-items-list-box-content"]').css('width', desc_percent+'%');
						jQuery('div[class^="playlists-list-item"]').css('width', thumb_percent+'%');
					});
				</script>
			</div>
			<div class="clr"></div>
		<?php } ?>
		<!-- content -->
		<div id="miwovideos_cats"><h2 class="miwovideos_title"><?php echo MText::_('COM_MIWOVIDEOS_VIDEOS'); ?></h2></div>
		<form method="post" name="adminForm" id="adminForm" action="<?php echo MRoute::_('index.php?option=com_miwovideos&view=category&category_id='.$this->category->id.$this->Itemid); ?>">
			<div class="miwovideos_subheader">
				<div class="miwovideos_searchbox">
					<input type="text" name="miwovideos_search" id="miwovideos_search" placeholder="Search..." value="<?php echo empty($this->lists['search']) ? "" : $this->lists['search']; ?>" onchange="document.adminForm.submit();"/>
				</div>
				<div class="miwovideos_flow_select">
					<?php $grid = $list = '';
					if (strpos($this->display, 'grid') !== false) {
						$grid = 'active';
					}
					else {
						$list = 'active';
					} ?>
					<a class="<?php echo MiwoVideos::getButtonClass(); ?> <?php echo $grid; ?>" href="<?php echo MRoute::_('index.php?option=com_miwovideos&view=category&display=grid&category_id='.$this->category->id.$this->Itemid); ?>" title="<?php echo MText::_('COM_MIWOVIDEOS_GRID'); ?>"><?php echo MText::_('COM_MIWOVIDEOS_GRID'); ?></a>
					<a class="<?php echo MiwoVideos::getButtonClass(); ?> <?php echo $list; ?>" href="<?php echo MRoute::_('index.php?option=com_miwovideos&view=category&display=list&category_id='.$this->category->id.$this->Itemid); ?>" title="<?php echo MText::_('COM_MIWOVIDEOS_LIST'); ?>"><?php echo MText::_('COM_MIWOVIDEOS_LIST'); ?></a>
				</div>
			</div>
			<input type="hidden" name="option" value="com_miwovideos"/>
			<input type="hidden" name="view" value="category"/>
			<input type="hidden" name="task" value=""/>
			<?php echo MHtml::_('form.token'); ?>
			<div class="clr"></div>
			<?php echo $this->loadTemplate($this->display); ?>
			<?php if ($this->pagination->total > $this->pagination->limit) { ?>
				<?php echo $this->pagination->getListFooter(); ?>
			<?php } ?>
		</form>
		<!-- content // -->
	</div>
</div>