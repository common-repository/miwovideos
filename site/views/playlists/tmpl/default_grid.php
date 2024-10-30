<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die ;

if (count($this->items)) {
    $utility = MiwoVideos::get('utility');

    foreach ($this->items as $item) {
        $video_id = empty($item->videos) ? '' : $item->videos[0]->video_id;

        if(!empty($item->thumb)) {
            $thumb = $utility->getThumbPath($item->id, 'playlists', $item->thumb);
        } else {
            $thumb = $utility->getThumbPath($video_id, 'videos', $item->videos[0]->thumb);
        }
		$url = MRoute::_('index.php?option=com_miwovideos&view=video&video_id='.$video_id.'&playlist_id='.$item->id.$this->Itemid);
	    $Itemid = MiwoVideos::get('router')->getItemid(array('view' => 'channel', 'channel_id' => $item->channel_id), null, true);
	    $channel_url = MRoute::_('index.php?option=com_miwovideos&view=channel&channel_id='.$item->channel_id.$Itemid); ?>
        <div class="miwovideos_column<?php echo $this->config->get('items_per_column'); ?>">
            <div class="videos-grid-item">
                <div class="videos-aspect<?php echo $this->config->get('thumb_aspect'); ?>"></div>
                <a href="<?php echo $url; ?>">
                    <img class="videos-items-grid-thumb" src="<?php echo $thumb; ?>" alt="<?php echo $item->title; ?>"/>
                </a>
            </div>
            <div class="playlists-items-grid-box-content">
				<h3 class="miwovideos_box_h3">
					<a href="<?php echo $url; ?>" title="<?php echo $item->title; ?>">
                        <?php echo $this->escape(MHtmlString::truncate($item->title, $this->config->get('title_truncation'), false, false)); ?>
					</a>
				</h3>
				<div class="playlists-meta">
					<div class="miwovideos-meta-info">
						<div class="created_by">
                            <span class="key"><?php echo MText::_('COM_MIWOVIDEOS_CREATED_BY'); ?></span>
                            <span class="value">
                                <a href="<?php echo $channel_url; ?>" title="<?php echo $item->channel_title; ?>">
                                    <?php echo $this->escape(MHtmlString::truncate($item->channel_title, $this->config->get('title_truncation'), false, false)); ?>
                                </a>
                            </span>
						</div>
						<div class="date-created">
                            <span class="value"><?php echo MiwoVideos::agoDateFormat($item->created); ?></span>
						</div>
					</div>
				</div>
            </div>
        </div>
    <?php } ?>
<?php } ?>