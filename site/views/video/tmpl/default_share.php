<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die ; ?>
<?php echo MHtml::_('tabs.start', 'miwovideos', array('useCookie'=>0)); ?>

<?php echo MHtml::_('tabs.panel', MText::_('COM_MIWOVIDEOS_SHARE_THIS_VIDEO'), 'sl_share_this'); ?>
    <!-- AddThis Button BEGIN -->
    <div class="addthis_toolbox addthis_default_style addthis_32x32_style">
        <a class="addthis_button_facebook"></a>
        <a class="addthis_button_twitter"></a>
        <a class="addthis_button_google_plusone_share"></a>
        <a class="addthis_button_blogger"></a>
        <a class="addthis_button_odnoklassniki_ru"></a>
        <a class="addthis_button_vk"></a>
        <a class="addthis_button_tumblr"></a>
        <a class="addthis_button_reddit"></a>
        <a class="addthis_button_linkedin"></a>
        <a class="addthis_button_compact"></a>
    </div>
    <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js"></script>
    <!-- AddThis Button END -->
    <div class="share-panel-url-container">
        <span>
            <input type="text" value="<?php echo htmlspecialchars(MiwoVideos::get('utility')->getActiveUrl()); ?>" id="share_url"/>
        </span>
        <span>
            <input type="checkbox" id="start-time-checkbox"></span>
        <?php echo MText::_('COM_MIWOVIDEOS_START_AT') ?>
        <input type="text" value="" id="share_at_time"/>
    </div>
<?php echo MHtml::_('tabs.panel', MText::_('COM_MIWOVIDEOS_EMBED'), 'sl_embed'); ?>
    <textarea class="miwovideos_embed">
        <?php echo "<iframe width=\"600\" height=\"360\" src=\"".rtrim(MUri::root(), '/').MRoute::_("index.php?option=com_miwovideos&view=video&video_id=".$this->item->id.$this->Itemid."&layout=player&tmpl=component")."\" frameborder=\"0\" allowfullscreen></iframe>"; ?>
    </textarea>

<?php echo MHtml::_('tabs.end'); ?>