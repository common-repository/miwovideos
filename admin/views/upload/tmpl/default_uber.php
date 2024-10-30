<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die;

$config = MiwoVideos::getConfig();

$types = explode('|', $config->get('allow_file_types'));

$largeExtensionReadable = '';
if (is_array($types)) {
    $last_item = end($types);
    foreach ($types as $type) {
        if ($type == $last_item) {
            $largeExtensionReadable .= $type;
        } else {
            $largeExtensionReadable .= $type . ', ';
        }
    }
}

//******************************************************************************************************
//   ATTENTION: THIS FILE HEADER MUST REMAIN INTACT. DO NOT DELETE OR MODIFY THIS FILE HEADER.
//
//   Name: ubr_file_upload.php
//   Revision: 1.5
//   Date: 3/2/2008 11:16:38 AM
//   Link: http://uber-uploader.sourceforge.net
//   Initial Developer: Peter Schmandra  http://www.webdice.org
//   Description: Select and submit upload files.
//
//   Licence:
//   The contents of this file are subject to the Mozilla Public
//   License Version 1.1 (the "License"); you may not use this file
//   except in compliance with the License. You may obtain a copy of
//   the License at http://www.mozilla.org/MPL/
//
//   Software distributed under the License is distributed on an "AS
//   IS" basis, WITHOUT WARRANTY OF ANY KIND, either express or
//   implied. See the License for the specific language governing
//   rights and limitations under the License.
//
//******************************************************************************************************

$THIS_VERSION = '1.5';

require_once(MPATH_MIWOVIDEOS_ADMIN . '/library/uber/ubr_ini.php');
require_once(MPATH_MIWOVIDEOS_ADMIN . '/library/uber/ubr_lib.php');

// Load config file
require $DEFAULT_CONFIG;

//******************************************************************************************************
// The following possible query string formats are assumed
//
// 1. No query string
// 2. ?about=1
//******************************************************************************************************

if ($DEBUG_PHP) {
    phpinfo();
    exit();
} elseif ($DEBUG_CONFIG) {
    mijoDebug($_CONFIG['config_file_name'], $_CONFIG);
    exit();
} elseif (isset($_GET['about']) && $_GET['about'] == 1) {
    kak("<u><b>UBER UPLOADER FILE UPLOAD</b></u><br>UBER UPLOADER VERSION =  " . $UBER_VERSION . "<br>UBR_FILE_UPLOAD = " . $THIS_VERSION . "<br>\n", 1, __LINE__);
}

//******************************************************************************************************
//   Set custom head tags
//******************************************************************************************************

ob_start();
?>
    .bar1 {background-color:#FFFFFF; position:relative; text-align:left; height:24px; width:250px; border:1px solid #505050; border-radius:3px; -moz-border-radius:3px; -webkit-border-radius:3px;}
    .bar2 {background-color:#99CC00; position:relative; text-align:left; height:24px; width:0%;}
<?php
$html = ob_get_contents();
ob_end_clean();

$this->document->addStyleDeclaration($html);
$this->document->addScript($PATH_TO_JS_SCRIPT);

ob_start();
?>
    var path_to_link_script = "<?php print $PATH_TO_LINK_SCRIPT; ?>";
    var path_to_set_progress_script = "<?php print $PATH_TO_SET_PROGRESS_SCRIPT; ?>";
    var path_to_get_progress_script = "<?php print $PATH_TO_GET_PROGRESS_SCRIPT; ?>";
    var path_to_upload_script = "<?php print $PATH_TO_UPLOAD_SCRIPT; ?>";
    var multi_configs_enabled = <?php print $MULTI_CONFIGS_ENABLED; ?>;
<?php if ($MULTI_CONFIGS_ENABLED) {
    print "var config_file = \"$config_file\";\n";
} ?>
    var check_allow_extensions_on_client = <?php print $_CONFIG['check_allow_extensions_on_client']; ?>;
    var check_disallow_extensions_on_client = <?php print $_CONFIG['check_disallow_extensions_on_client']; ?>;
<?php if ($_CONFIG['check_allow_extensions_on_client']) {
    print "var allow_extensions = /" . $_CONFIG['allow_extensions'] . "$/i;\n";
} ?>
<?php if ($_CONFIG['check_disallow_extensions_on_client']) {
    print "var disallow_extensions = /" . $_CONFIG['disallow_extensions'] . "$/i;\n";
} ?>
    var check_file_name_format = <?php print $_CONFIG['check_file_name_format']; ?>;
    var check_null_file_count = <?php print $_CONFIG['check_null_file_count']; ?>;
    var check_duplicate_file_count = <?php print $_CONFIG['check_duplicate_file_count']; ?>;
<?php if(MRequest::getWord('task') == 'edit' and MRequest::getWord('view') == 'videos') {
    echo 'var max_upload_slots = 1;';
} else {
    echo 'var max_upload_slots = ' . $_CONFIG['max_upload_slots'] .';';
}
?>

    var cedric_progress_bar = <?php print $_CONFIG['cedric_progress_bar']; ?>;
    var progress_bar_width = <?php print $_CONFIG['progress_bar_width']; ?>;
    var show_percent_complete = <?php print $_CONFIG['show_percent_complete']; ?>;
    var show_files_uploaded = <?php print $_CONFIG['show_files_uploaded']; ?>;
    var show_current_position = <?php print $_CONFIG['show_current_position']; ?>;
    var show_elapsed_time = <?php print $_CONFIG['show_elapsed_time']; ?>;
    var show_est_time_left = <?php print $_CONFIG['show_est_time_left']; ?>;
    var show_est_speed = <?php print $_CONFIG['show_est_speed']; ?>;
<?php
$html = ob_get_contents();
ob_end_clean();

$this->document->addScriptDeclaration($html);

?>
<?php if ($DEBUG_AJAX) {
    print "<br><div class=\"debug\" id=\"ubr_debug\"><b>AJAX DEBUG WINDOW</b><br></div><br>\n";
} ?>

<?php if (MFactory::getApplication()->isAdmin()) : ?>
    <fieldset id="ubr_alert_container" class="adminform" style="display:none">
        <h3 id="ubr_alert"></h3>
    </fieldset>
<?php else : ?>
    <div id="ubr_alert_container" style="display:none">
        <h3 id="ubr_alert"></h3>
    </div>
<?php endif; ?>

    <!-- Start Progress Bar -->
    <div id="progress_bar" style="display:none">
        <fieldset class="adminform">
            <div class="bar1" id="upload_status_wrap">
                <div class="bar2" id="upload_status"></div>
            </div>
        </fieldset>
        <?php if ($_CONFIG['show_percent_complete'] || $_CONFIG['show_files_uploaded'] || $_CONFIG['show_current_position'] || $_CONFIG['show_elapsed_time'] || $_CONFIG['show_est_time_left'] || $_CONFIG['show_est_speed']) { ?>
            <?php if (MFactory::getApplication()->isAdmin()) : ?>
                <div style="padding:10px;">
            <?php endif; ?>
            <table class="adminlist" style="width: 50%;">
                <tbody>
                <?php if ($_CONFIG['show_percent_complete']) { ?>
                    <tr>
                        <th scope="row">
                            <?php echo MText::_('COM_MIWOVIDEOS_PERCENT_COMPLETE'); ?>
                        </th>
                        <td class="center">
                            <span id="percent">0%</span>
                        </td>
                    </tr>
                <?php } ?>
                <?php if ($_CONFIG['show_files_uploaded']) { ?>
                    <tr>
                        <th scope="row">
                            <?php echo MText::_('COM_MIWOVIDEOS_FILES_UPLOADED'); ?>
                        </th>
                        <td class="center">
                            <span id="uploaded_files">0</span> of <span id="total_uploads"></span>
                        </td>
                    </tr>
                <?php } ?>
                <?php if ($_CONFIG['show_current_position']) {
                    ?>
                    <tr>
                        <th scope="row">
                            <?php echo MText::_('COM_MIWOVIDEOS_CURRENT_POSITION'); ?>
                        </th>
                        <td class="center">
                            <span id="currentupld">0</span> / <span id="total_kbytes"></span> KBs
                        </td>
                    </tr>
                <?php } ?>

                <?php if ($_CONFIG['show_current_position']) { ?>
                    <tr>
                        <th scope="row">
                            <?php echo MText::_('COM_MIWOVIDEOS_ELAPSED_TIME'); ?>
                        </th>
                        <td class="center">
                            <span id="time">0</span>
                        </td>
                    </tr>
                <?php } ?>
                <?php if ($_CONFIG['show_est_time_left']) { ?>
                    <tr>
                        <th scope="row">
                            <?php echo MText::_('COM_MIWOVIDEOS_EST_TIME_LEFT'); ?>
                        </th>
                        <td class="center">
                            <span id="remain">0</span>
                        </td>
                    </tr>
                <?php } ?>
                <?php if ($_CONFIG['show_est_time_left']) { ?>
                    <tr>
                        <th scope="row">
                            <?php echo MText::_('COM_MIWOVIDEOS_EST_SPEED'); ?>
                        </th>
                        <td class="center">
                            <span id="speed">0</span> KB/s.
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <?php if (MFactory::getApplication()->isAdmin()) : ?>
                </div>
            <?php endif; ?>
        <?php } ?>
    </div>
    <!-- End Progress Bar -->

<?php if ($_CONFIG['embedded_upload_results'] || $_CONFIG['opera_browser'] || $_CONFIG['safari_browser']) { ?>
    <div id="upload_div" style="display:none;">
        <iframe name="upload_iframe" frameborder="0" width="800" height="200" scrolling="auto"></iframe>
    </div>
<?php } ?>

<?php if (MFactory::getApplication()->isAdmin()) : ?>
    <fieldset class="adminform">
        <ul class="panelform">
            <li>
                <label><?php echo MText::_('COM_MIWOVIDEOS_SUPPORTED_FORMATS_LABEL'); ?></label>
                <span class="label" style="clear:none;"><?php echo $largeExtensionReadable; ?></span>
            </li>
        </ul>
    </fieldset>
    <!-- Start Upload Form -->
    <fieldset class="adminform">
        <ul class="panelform">
            <noscript><p><?php echo MText::_('COM_MIWOVIDEOS_PLEASE_ENABLE_JAVASCRIPT'); ?></p></noscript>
            <div id="upload_slots">
                <li>
                    <label><?php echo MText::_('COM_MIWOVIDEOS_UPLOAD_A_FILE'); ?></label>
                    <input type="file" name="upfile_0"
                           <?php if ($_CONFIG['multi_upload_slots']){ ?>onChange="addUploadSlot(1)"<?php } ?>
                           onkeypress="return handleKey(event)" value="">
                </li>
            </div>
            <li>
                <label></label>
                <button class="button btn-success" type="button" id="upload_button" name="upload_button" value="Upload"
                        onClick="linkUpload();">
                    <?php echo MText::_('COM_MIWOVIDEOS_NEXT') ?>
                </button>
            </li>
        </ul>
    </fieldset>
<?php else : ?>
    <fieldset class="adminform">
        <div class="formelm">
            <label><?php echo MText::_('COM_MIWOVIDEOS_SUPPORTED_FORMATS_LABEL'); ?></label>
            <span><?php echo $largeExtensionReadable; ?></span>
        </div>
    </fieldset>
    <fieldset class="adminform">
        <div id="upload_slots">
            <div class="formelm">
                <label><?php echo MText::_('COM_MIWOVIDEOS_UPLOAD_A_FILE'); ?></label>
                <input type="file" name="upfile_0"
                       <?php if ($_CONFIG['multi_upload_slots']){ ?>onChange="addSiteUploadSlot(1)"<?php } ?>
                       onkeypress="return handleKey(event)" value="">
            </div>
        </div>
        <div class="formelm-buttons">
            <button class="miwovideos_button" type="button" id="upload_button" name="upload_button" value="Upload"
                    onClick="linkUpload();">
                <?php echo MText::_('COM_MIWOVIDEOS_NEXT') ?>
            </button>
        </div>
    </fieldset>
<?php endif; ?>
    <!-- End Upload Form -->
    <div class="clr"></div>