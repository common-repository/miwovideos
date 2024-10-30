<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die ;

?>
<?php foreach ($this->items as $item) { 
			if ($item->id == MRequest::getInt('cid', null)) { ?>
				<div style="word-wrap: break-word;"><?php echo $item->note; ?></div>
			<?php } ?>
<?php } ?>