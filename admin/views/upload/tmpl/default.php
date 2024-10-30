<?php
/**
 * @package        MiwoVideos
 * @copyright      Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license        GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die;

$maxUpload    = (int)$this->config->get('upload_max_filesize');
$maxPhpUpload = min((int)ini_get('post_max_size'), (int)ini_get('upload_max_filesize'), (int)$maxUpload);
$isVideoPage  = false;
$utility      = MiwoVideos::get('utility');
$cache = MFactory::getCache('com_miwovideos', 'output');
$cache->setCaching(1);
if(!$server_info = $cache->get('server_info', 'com_miwovideos')) {
	$ffmpeg_version = $utility->getFfmpegVersion();
}
else {
	$ffmpeg_version = $server_info[0]['value'];
}
if (MRequest::getWord('task') == 'edit' and MRequest::getWord('view') == 'videos') {
	$isVideoPage = true;
}
if (MiwoVideos::isDashboard()) {
	$action =  $utility->route('wp-admin/admin-ajax.php?action=miwovideos&page=miwovideos&option=com_miwovideos&view=upload&task=upload&format=raw');
} else {
	$action = $utility->route('index.php?option=com_miwovideos&view=upload&task=upload&format=raw');
}
if (MiwoVideos::isDashboard()) {
	$dashboard = '&dashboard=1';
}
?>

<?php echo MHtml::_('sliders.start', 'miwovideos_slider_upload'); ?>

	<?php if ($this->config->get('normal_upload') == 1) { ?>
	    <?php echo MHtml::_('sliders.panel', MText::sprintf('COM_MIWOVIDEOS_UPLOAD_VIDEOS_LESS_THAN_N_MB', $maxPhpUpload), 'small'); ?>
	    <form action="<?php echo $action; ?>" method="post" target="_parent" name="uploadForm" <?php echo $this->config->get('upload_script') == 'dropzone' ? "id=\"dropzone\" class=\"dropzone\"" : "id=\"uploadForm\" class=\"form-validate\"" ?> enctype="multipart/form-data">

        <?php if ($this->config->get('upload_script') == 'dropzone') { // Dropzone ?>
            <div class="miwi_paid">
                <strong><?php echo MText::sprintf('MLIB_X_PRO_MEMBERS', 'Drag & Drop'); ?></strong><br /><br />
                <?php echo MText::sprintf('MLIB_PRO_MEMBERS_DESC', 'http://miwisoft.com/wordpress-plugins/miwovideos-share-your-videos#pricing', 'MiwoVideos'); ?>
		    </div>
		    <div class="dz-default dz-message"></div>
        <?php } ?>

	        <?php if ($this->config->get('upload_script') != 'dropzone') { // Dropzone ?>
	            <fieldset class="adminform" id="miwovideos_fallback">
	                <ul class="panelform">
	                    <li>
	                        <label for="miwovideos_photoupload">
	                            <?php echo MText::_('COM_MIWOVIDEOS_UPLOAD_A_FILE') ?>
	                        </label>
	                        <input type="file" name="Filedata"/>
	                    </li>
	                    <li>
	                        <label></label>
	                        <button type="button" class="button btn-success" onclick="Miwi.submitbutton('upload')">
	                            <?php echo MText::_('COM_MIWOVIDEOS_NEXT') ?>
	                        </button>
	                    </li>
	                </ul>
	            </fieldset>
	        <?php } ?>
	        <?php if ($this->config->get('upload_script') == 'fancy') { // Fancy
	            ?>
	            <?php echo $this->loadTemplate('fancy'); ?>

	            <fieldset class="adminform">
	                <div id="miwovideos_status" class="hide">
	                    <p>
	                        <img src="<?php echo MURL_MIWOVIDEOS; ?>/site/assets/images/fancy/button_select.png" href="#" id="miwovideos_browse" class="button"/>
	                        <img src="<?php echo MURL_MIWOVIDEOS; ?>/site/assets/images/fancy/button_clear.png" href="#" id="miwovideos_clear" class="button"/>
	                        <img src="<?php echo MURL_MIWOVIDEOS; ?>/site/assets/images/fancy/button_start.png" href="#" id="miwovideos_upload" class="button"/>
	                    </p>

	                    <div>
	                        <span class="overall-title"></span>
	                        <img src="<?php echo MURL_MIWOVIDEOS; ?>/site/assets/images/fancy/bar.gif" class="progress overall-progress"/>
	                    </div>
	                    <div class="clr"></div>
	                    <div>
	                        <span class="current-title"></span>
	                        <img src="<?php echo MURL_MIWOVIDEOS; ?>/site/assets/images/fancy/bar.gif" class="progress current-progress"/>
	                    </div>
	                    <div class="current-text"></div>
	                </div>
	                <ul id="miwovideos_list"></ul>
	            </fieldset>
	        <?php } ?>

	        <?php if (MiwoVideos::isDashboard()) { ?>
	            <input type="hidden" name="dashboard" value="1"/>
	            <input type="hidden" name="Itemid" value="<?php echo MiwoVideos::getInput()->getInt('Itemid', 0); ?>"/>
	        <?php } ?>
	    </form>
	<?php } ?>

    <?php if ($this->config->get('perl_upload') == 1) { ?>
        <?php echo MHtml::_('sliders.panel', MText::sprintf('COM_MIWOVIDEOS_UPLOAD_LARGE_VIDEOS_UP_TO_N_MB', $maxUpload), 'large'); ?>
        <form action="<?php echo $utility->route('index.php?option=com_miwovideos&view=upload&task=upload'); ?>" method="post" target="_parent" name="ubr_upload" id="ubr_upload" class="form-validate" enctype="multipart/form-data">
            <?php echo $this->loadTemplate('uber'); ?>

            <?php if (MiwoVideos::isDashboard()) { ?>
                <input type="hidden" name="dashboard" value="1"/>
                <input type="hidden" name="Itemid" value="<?php echo MiwoVideos::getInput()->getInt('Itemid', 0); ?>"/>
            <?php } ?>
        </form>
    <?php } ?>
<?php if ($this->config->get('remote_video_link', 1) == 1) { ?>
	<?php echo MHtml::_('sliders.panel', MText::_('COM_MIWOVIDEOS_REMOTE_VIDEO'), 'remote'); ?>
	    <form action="<?php echo $utility->route('index.php?option=com_miwovideos&view=upload&task=remoteLink'); ?>" method="post" target="_parent" name="remote_links" id="remote_links" class="form-validate" enctype="multipart/form-data">
	        <fieldset class="adminform">

	            <?php if ($isVideoPage) { ?>
	                <div class="miwovideos_remote_links"><?php echo MText::_('COM_MIWOVIDEOS_REMOTE_VIDEO_LINK'); ?></div>
	                <input type="text" name="remote_links" style="width: 20%">
	            <?php }
	            else { ?>
	                <div class="miwovideos_remote_links"><?php echo MText::_('COM_MIWOVIDEOS_REMOTE_VIDEO_LINKS'); ?></div>
	                <textarea name="remote_links" class="miwovideos_remote_link_textarea"></textarea>
	            <?php } ?>
	            <button class="button btn-success" onclick="Miwi.submitbutton('remoteLink')"><?php echo MText::_('COM_MIWOVIDEOS_NEXT'); ?></button>
	            <?php if (MiwoVideos::isDashboard()) { ?>
	                <input type="hidden" name="dashboard" value="1"/>
	                <input type="hidden" name="Itemid" value="<?php echo MiwoVideos::getInput()->getInt('Itemid', 0); ?>"/>
	            <?php } ?>
	        </fieldset>
	    </form>
<?php } ?>
<?php if ($this->config->get('remote_video_embed_code' , 1) == 1) { ?>
	  <?php echo MHtml::_('sliders.panel', MText::_('COM_MIWOVIDEOS_UPLOAD_EMBED_CODE'), 'embed_code'); ?>
	<div class="miwi_paid">
		<strong><?php echo MText::sprintf('MLIB_X_PRO_MEMBERS', 'Remote video embed code'); ?></strong><br /><br />
	<?php echo MText::sprintf('MLIB_PRO_MEMBERS_DESC', 'http://miwisoft.com/wordpress-plugins/miwovideos-share-your-videos#pricing', 'MiwoVideos'); ?>
	</div><?php } ?>
<?php if (!MiwoVideos::isDashboard()) { ?>
	  <?php echo MHtml::_('sliders.panel', MText::_('COM_MIWOVIDEOS_BULK_IMPORT_FROM_SERVER'), 'import'); ?>
    <div class="miwi_paid">
		<strong><?php echo MText::sprintf('MLIB_X_PRO_MEMBERS', 'Bulk Import From Server'); ?></strong><br /><br />
	<?php echo MText::sprintf('MLIB_PRO_MEMBERS_DESC', 'http://miwisoft.com/wordpress-plugins/miwovideos-share-your-videos#pricing', 'MiwoVideos'); ?>
	</div><?php } ?>
<?php echo MHtml::_('sliders.end'); ?>

<div class="clr"></div>

<?php if ($this->config->get('upload_script') == 'dropzone') { // Dropzone ?>
	<style type="text/css">
		.dropzone a.dz-remove, .dropzone-previews a.dz-remove {
			margin-top : 10px;
		}
	</style>
	<script type="text/javascript">
		jQuery(document).ready(function() {
				});
	</script>
<?php } ?>
<?php if (MRequest::getString('view') != 'videos' && MRequest::getString('task') != 'edit') { ?>
<script type="text/javascript">
	jQuery('#dropzone').append('<input type="hidden" name="channel_id" value="<?php echo MRequest::getInt('channel_id', MiwoVideos::get('channels')->getDefaultChannel()->id); ?>">');
	jQuery('#ubr_upload').append('<input type="hidden" name="channel_id" id="channel_id" value="<?php echo MRequest::getInt('channel_id', MiwoVideos::get('channels')->getDefaultChannel()->id); ?>">');
	jQuery('#remote_links').append('<input type="hidden" name="channel_id" value="<?php echo MRequest::getInt('channel_id', MiwoVideos::get('channels')->getDefaultChannel()->id); ?>">');
	jQuery('#serverImport').append('<input type="hidden" name="channel_id" value="<?php echo MRequest::getInt('channel_id', MiwoVideos::get('channels')->getDefaultChannel()->id); ?>">');
</script>
<?php } ?>

