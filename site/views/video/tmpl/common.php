<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die ('Restricted access');
$utility = MiwoVideos::get('utility');
$thumb_size = $utility->getThumbSize($this->config->get('thumb_size'));
?>
<div class="miwovideos_box">
	<div class="miwovideos_box_heading">
		<h3 class="miwovideos_box_h3">
            <a href="<?php echo $url; ?>" title="<?php echo $item->title; ?>">
                <?php echo $this->escape(MHtmlString::truncate($item->title, $this->config->get('title_truncation'), false, false)); ?>
            </a>
        </h3>
	</div>
    <div class="miwovideos_box_content">
        <div class="videos-items-list-box">
            <div class="videos-list-item" style="width: <?php echo $thumb_size; ?>px">
                <div class="videos-aspect<?php echo $this->config->get('thumb_aspect'); ?>"></div>
                <a href="<?php echo $url; ?>">
                    <img class="videos-items-grid-thumb" src="<?php echo $utility->getThumbPath($item->id, 'videos', $item->thumb); ?>" title="<?php echo $item->title; ?>" alt="<?php echo $item->title; ?>"/>
                </a>
            </div>
            <div class="videos-items-list-box-content">
                <div class="videos-meta">
                    <div class="miwovideos-meta-info">
                        <div class="videos-view">
                            <span class="value"><?php echo number_format($item->hits); ?></span>
                            <span class="key"><?php echo MText::_('COM_MIWOVIDEOS_VIEWS'); ?></span>
                        </div>
                        <div class="date-created">
                            <span class="value"><?php echo MiwoVideos::agoDateFormat($item->created); ?></span>
                        </div>
                    </div>
                </div>
                <div class="videos-description">
                    <?php if (!empty($item->introtext)) { ?>
                        <?php echo MHtmlString::truncate(html_entity_decode($item->introtext, ENT_QUOTES), $this->config->get('desc_truncation'), false, false); ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function() {
        var box_width = document.getElementById("miwovideos_docs").offsetWidth;
        var thumb_size = <?php echo $thumb_size; ?>;
        var thumb_percent = Math.round((thumb_size/box_width)*100);
        var desc_percent = 100 - thumb_percent - 3;
        jQuery('div[class^="videos-items-list-box-content"]').css('width', desc_percent+'%');
        jQuery('div[class^="videos-list-item"]').css('width', thumb_percent+'%');
    });
</script>