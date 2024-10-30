<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die;

$plugin = MPluginHelper::getPlugin('miwovideos', 'videojs');
$params = new MObject(json_decode($plugin->params));
if ($params->get('show_related_carousel', 0)) { ?>
	<div class="miwi_paid">
		<strong><?php echo MText::sprintf('MLIB_X_PRO_MEMBERS', 'Related Carousel'); ?></strong><br /><br />
	<?php echo MText::sprintf('MLIB_PRO_MEMBERS_DESC', 'http://miwisoft.com/wordpress-plugins/miwovideos-share-your-videos#pricing', 'MiwoVideos'); ?>
	</div>
	<div class="dz-default dz-message"></div>
<?php
}
else {
	echo MiwoVideos::get('videos')->getPlayer($this->item);
}