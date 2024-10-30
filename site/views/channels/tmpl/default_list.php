<?php
/**
 * @package        MiwoVideos
 * @copyright      Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license        GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die;
if (count($this->items)) {
	$utility = MiwoVideos::get('utility'); ?>
	<table class="category table table-striped" style="margin-top: 10px;">
		<thead>
		<tr>
			<th>
				<?php echo MText::_('COM_MIWOVIDEOS_THUMB'); ?>
			</th>
			<th>
				<?php echo MHtml::_('grid.sort', MText::_('COM_MIWOVIDEOS_NAME'), 'c.title', $this->lists['order_Dir'], $this->lists['order']); ?>
			</th>
			<th style="text-align: center">
				<?php echo MHtml::_('grid.sort', MText::_('COM_MIWOVIDEOS_SUBSCRIPTIONS'), 'cs.subs', $this->lists['order_Dir'], $this->lists['order']); ?>
			</th>
			<th style="text-align: center">
				<?php echo MHtml::_('grid.sort', MText::_('COM_MIWOVIDEOS_LIKES'), 'c.likes', $this->lists['order_Dir'], $this->lists['order']); ?>
			</th>
			<th style="text-align: center">
				<?php echo MHtml::_('grid.sort', MText::_('COM_MIWOVIDEOS_DISLIKES'), 'c.dislikes', $this->lists['order_Dir'], $this->lists['order']); ?>
			</th>
			<th style="text-align: center">
				<?php echo MHtml::_('grid.sort', MText::_('COM_MIWOVIDEOS_HITS'), 'c.hits', $this->lists['order_Dir'], $this->lists['order']); ?>
			</th>
		</tr>
		</thead>
		<tbody>
		<?php
		$i = $k = 0;
		foreach ($this->items as $item) {
			$Itemid = MiwoVideos::get('router')->getItemid(array('view' => 'channel', 'channel_id' => $item->id), null, true);
			$url    = MRoute::_('index.php?option=com_miwovideos&view=channel&channel_id='.$item->id.$Itemid);
			?>
			<tr class="cat-list-row-<?php echo $i % 2; ?>">
				<td>
					<div class="videos-list-item">
						<div class="videos-aspect<?php echo $this->config->get('thumb_aspect'); ?>"></div>
						<a href="<?php echo $url; ?>">
							<img class="videos-items-grid-thumb" src="<?php echo $utility->getThumbPath($item->id, 'channels', $item->thumb); ?>" title="<?php echo $item->title; ?>" alt="<?php echo $item->title; ?>"/>
						</a>
					</div>
				</td>
				<td>
					<a href="<?php echo $url; ?>" title="<?php echo $item->title; ?>">
						<?php echo $this->escape(MHtmlString::truncate($item->title, $this->config->get('title_truncation'), false, false)); ?>
					</a><br>
					<?php echo MHtmlString::truncate(html_entity_decode($item->introtext, ENT_QUOTES), $this->config->get('desc_truncation'), false, false); ?>
				</td>
				<td style="text-align: center">
					<?php echo isset($item->subs) ? number_format($item->subs) : '0'; ?>
				</td>
				<td style="text-align: center">
					<?php echo $item->likes; ?>
				</td>
				<td style="text-align: center">
					<?php echo $item->dislikes; ?>
				</td>
				<td style="text-align: center">
					<?php echo number_format($item->hits); ?>
				</td>
			</tr>
			<?php
			$k = 1 - $k;
			$i++;
		}
		if (count($this->items) == 0) {
			?>
			<tr>
				<td colspan="4" style="text-align: center;">
					<div class="info"><?php echo MText::_('COM_MIWOVIDEOS_NO_LOCATION_RECORDS'); ?></div>
				</td>
			</tr>
		<?php
		}
		?>
		</tbody>

	</table>
<?php } ?>