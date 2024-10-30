<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die('Restricted access');

if (count($rows)) {
?>
		<?php
			foreach ($rows as $row) {
                $Itemid = MiwoVideos::get('router')->getItemid(array('view' => 'channel', 'channel_id' => $row->id), null, true);

	    		$link = MRoute::_('index.php?option=com_miwovideos&view=channel&channel_id='.$row->id . $Itemid);?>
				<div class="miwovideos-modules-module">
                    <div>
                        <a href="<?php echo $link; ?>">
                            <img class="miwovideos-module-thumbs" style="width: <?php echo $width; ?>px; height: <?php echo $height; ?>px" src="<?php echo $utility->getThumbPath($row->id, 'channels', $row->thumb); ?>"  alt="<?php echo $row->title; ?>" />
                        </a>
                    </div>
                    <div>
                        <p class="miwovideos-module-text-class" >
                            <a href="<?php echo $link; ?>">
                                <?php echo htmlspecialchars(MHtmlString::truncate($row->title, $config->get('title_truncation'), false, false)); ?>
                            </a>
                        </p>
                        <?php if (MiwoVideos::getConfig()->get('subscriptions')) { ?>
                            <span class="miwovideos-modules-span" ><?php echo MiwoVideos::get('model')->getSubscriberCount($row->id) . ' ' . MText::_('COM_MIWOVIDEOS_SUBSCRIBERS'); ?></span>
                        <?php } ?>
                    </div>
				</div>
	  <?php } ?>
<?php
}