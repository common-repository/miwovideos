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
    <?php if($params->get('position') == 1) { ?>
        <div class="miwovideos_player">
                        <?php echo MiwoVideos::get('videos')->getPlayer($rows[0]);
            unset($rows[0]); ?>        </div>
        <div class="miwovideos-top-module-wrap">
            <?php foreach ($rows as $row) {
                $Itemid = MiwoVideos::get('router')->getItemid(array('view' => 'video', 'video_id' => $row->id), null, true);
                $link = MRoute::_('index.php?option=com_miwovideos&view=video&video_id='.$row->id . $Itemid);?>
                <div class="miwovideos-top-module">
                    <a href="<?php echo $link; ?>">
                        <img style="width: <?php echo $width; ?>px; height: <?php echo $height; ?>px;" src="<?php echo $utility->getThumbPath($row->id, 'videos', $row->thumb); ?>" title="<?php echo $row->title; ?>"" alt="<?php echo $row->title; ?>"/>
                    </a>
                </div>
            <?php } ?>
        </div>
    <?php } else { ?>
        <?php foreach ($rows as $row) {
            $Itemid = MiwoVideos::get('router')->getItemid(array('view' => 'video', 'video_id' => $row->id), null, true);
            $link = MRoute::_('index.php?option=com_miwovideos&view=video&video_id='.$row->id . $Itemid);?>
            <div class="miwovideos-modules-module">
                <a href="<?php echo $link; ?>" title="<?php echo $row->title; ?>">
                    <img class="miwovideos-module-thumbs" style="width: <?php echo $width; ?>px; height: <?php echo $height; ?>px" src="<?php echo $utility->getThumbPath($row->id, 'videos', $row->thumb); ?>" title="<?php echo $row->title; ?>" alt="<?php echo $row->title; ?>"/>
                    <h3 class="miwovideos_box_h3">
                        <?php echo MHtmlString::truncate($row->title, $config->get('title_truncation'), false, false); ?>
                    </h3>
                </a>

                <div class="videos-meta">
                    <div class="miwovideos-meta-info">
                        <div class="videos-view">
                            <span class="value"><?php echo number_format($row->hits); ?></span>
                            <span class="key"><?php echo MText::_('COM_MIWOVIDEOS_VIEWS'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    <?php } ?>
<?php } ?>