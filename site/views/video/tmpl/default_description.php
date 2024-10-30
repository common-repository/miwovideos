<?php
/**
 * @package        MiwoVideos
 * @copyright      Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license        GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die; ?>
<div class="miwovideos_description">
	<p class="video_published_date"><?php echo MText::_('COM_MIWOVIDEOS_PUBLISHED'); ?> <?php echo MHtml::_('date', $this->item->created, MText::_('DATE_FORMAT_LC4')); ?></p>

	<div class="miwovideos_expander_collapsed"><?php echo html_entity_decode($this->item->introtext, ENT_QUOTES); ?></div>
	<div class="miwovideos_expand" style="display: none">
		<div class="video_description"><?php echo html_entity_decode($this->item->description); ?></div>
		<br/>

		<div class="miwovideos_custom_fields">
			<?php if ($this->config->get('categories')) { ?>
				<div class="title">
					<?php echo MText::_('COM_MIWOVIDEOS_CATEGORY'); ?>
				</div>
				<div class="content">
					<?php
					$cats = array();
					foreach ($this->item->categories as $category) {
						$Itemid       = MiwoVideos::get('router')->getItemid(array('view' => 'category', 'category_id' => $category->id), null, true);
						$category_url = MRoute::_('index.php?option=com_miwovideos&view=category&category_id='.$category->id.$Itemid);
						$cats[]       = '<a href="'.$category_url.'" >'.$category->title.'</a>';
					}

					echo implode(', ', $cats);
					?>
				</div>
			<?php } ?>
			<br/>
			<?php if (MiwoVideos::is31() and !empty($this->item->tags) and $this->config->get('tags')) { ?>
				<div class="title">
					<?php echo MText::_('COM_MIWOVIDEOS_TAGS'); ?>
				</div>
				<div class="content">
					<?php
					$tags = array();
					foreach ($this->item->tags as $tag) {
						$tag_url = MRoute::_('index.php?option=com_tags&view=tag&id='.$tag->id.':'.$tag->alias);
						$tags[]  = '<a href="'.$tag_url.'" >'.$tag->title.'</a>';
					}

					echo implode(', ', $tags);
					?>
				</div>
				<br/>
			<?php } ?>
			<?php if ($this->config->get('custom_fields')) { ?>
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
			<?php } ?>
		</div>
	</div>
	<div class="video_more">
		<button type="button" class="miwovideos_more_button"><?php echo MText::_('COM_MIWOVIDEOS_SHOW_MORE'); ?></button>
	</div>
</div>
<script type="text/javascript"><!--
	jQuery(".miwovideos_more_button").toggle(showPanel, hidePanel);
	function showPanel() {
		jQuery(".miwovideos_expand").show();
		jQuery('.miwovideos_expander_collapsed').hide();
		jQuery(".miwovideos_more_button").text('<?php echo MText::_('COM_MIWOVIDEOS_SHOW_LESS'); ?>')
	}
	function hidePanel() {
		jQuery(".miwovideos_expand").hide();
		jQuery('.miwovideos_expander_collapsed').show();
		jQuery(".miwovideos_more_button").text('<?php echo MText::_('COM_MIWOVIDEOS_SHOW_MORE'); ?>');
	}
	//--></script>