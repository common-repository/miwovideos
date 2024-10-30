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
	<ul class="menu">
		<?php
			foreach ($rows as $row) {
                $Itemid = MiwoVideos::get('router')->getItemid(array('view' => 'playlist', 'playlist_id' => $row->id), null, true);

	    		$link = MRoute::_('index.php?option=com_miwovideos&view=playlist&playlist_id='.$row->id . $Itemid);?>
				<li>
					<a href="<?php echo $link; ?>">
                        <?php echo htmlspecialchars(MHtmlString::truncate($row->title, $config->get('title_truncation'), false, false)); ?>
                    </a>
				</li>
	  <?php } ?>
	</ul>
<?php
}