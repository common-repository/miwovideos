<?php
/**
 * @package        MiwoVideos
 * @copyright      Copyright  ( C ) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license        GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die;
?>

<div class="miwovideos_box">
	<div class="miwovideos_box_heading">
		<?php if (($this->params->get('show_page_heading', '0') == '1')) {
			$page_title = $this->params->get('page_heading', '');
			if (empty($page_title)) {
				$page_title = $this->params->get('page_title', '');
			}
			?>
			<h1 class="miwovideos_box_h1"><?php echo $page_title; ?></h1>
		<?php
		}
		?>
	</div>

	<div class="miwovideos_box_content">
		<div class="miwovideos_box_content_99">
			<?php echo $this->widget_top; ?>
		</div>
		<div class="miwovideos_box_content_99">
			<div class="miwovideos_box_content_49">
				<?php echo $this->widget_left; ?>
			</div>
			<div class="miwovideos_box_content_49">
				<?php echo $this->widget_right; ?>
			</div>
		</div>
		<div class="miwovideos_box_content_99">
			<?php echo $this->widget_bottom; ?>
		</div>
	</div>

	<div class="clr"></div>
</div>
