<?php
//******************************************************************************************************
//   ATTENTION: THIS FILE HEADER MUST REMAIN INTACT. DO NOT DELETE OR MODIFY THIS FILE HEADER.
//
//   Name: ubr_default_config.php
//   Revision: 1.4
//   Date: 2/18/2008 5:36:25 PM
//   Link: http://uber-uploader.sourceforge.net
//   Initial Developer: Peter Schmandra  http://www.webdice.org
//   Description: Configure upload options
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
//********************************************************************************************************

$config = MiwoVideos::getConfig();

$app = MFactory::getApplication();
$admin = MRequest::getInt('admin');
$is_admin = (($app->isAdmin() or $admin == 1) ? true : false);

if ($is_admin) {
    $redirect_url = MUri::root().'wp-admin/admin.php?page=miwovideos&option=com_miwovideos&amp;view=upload&amp;task=uberupload';
} else {
    $redirect_url = MUri::root() . 'index.php?option=com_miwovideos&amp;view=upload&amp;task=uberupload';
}

$_CONFIG['config_file_name']                      = 'ubr_default_config';
$_CONFIG['upload_dir']                            = MIWOVIDEOS_UPLOAD_DIR.'/videos/';
$_CONFIG['multi_upload_slots']                    = 1;
$_CONFIG['max_upload_slots']                      = 10;
$_CONFIG['embedded_upload_results']               = 0;
$_CONFIG['check_file_name_format']                = 1;
$_CONFIG['check_null_file_count']                 = 1;
$_CONFIG['check_duplicate_file_count']            = 1;
$_CONFIG['show_percent_complete']                 = 1;
$_CONFIG['show_files_uploaded']                   = 1;
$_CONFIG['show_current_position']                 = 1;
$_CONFIG['show_elapsed_time']                     = 1;
$_CONFIG['show_est_time_left']                    = 1;
$_CONFIG['show_est_speed']                        = 1;
$_CONFIG['cedric_progress_bar']                   = 1;
$_CONFIG['progress_bar_width']                    = 250;
$_CONFIG['unique_upload_dir']                     = 0;
$_CONFIG['unique_file_name']                      = 1;
$_CONFIG['unique_file_name_length']               = 16;
$_CONFIG['max_upload_size']                       = $config->get('upload_max_filesize', 30) * 1024 * 1024;
$_CONFIG['overwrite_existing_files']              = 0;
$_CONFIG['redirect_url']                          = $redirect_url;
$_CONFIG['redirect_using_location']               = 1;
$_CONFIG['redirect_using_html']                   = 0;
$_CONFIG['redirect_using_js']                     = 0;
$_CONFIG['check_allow_extensions_on_client']      = 1;
$_CONFIG['check_disallow_extensions_on_client']   = 0;
$_CONFIG['check_allow_extensions_on_server']      = 1;
$_CONFIG['check_disallow_extensions_on_server']   = 0;
$_CONFIG['allow_extensions']                      = '('.$config->get('allow_file_types').')';
$_CONFIG['disallow_extensions']                   = '(sh|php|php3|php4|php5|py|shtml|phtml|cgi|pl|plx|htaccess|htpasswd)';  // Add more extensions but do not remove the ones already present
$_CONFIG['normalize_file_names']                  = 1;
$_CONFIG['normalize_file_delimiter']              = '_';
$_CONFIG['normalize_file_length']                 = 48;
$_CONFIG['link_to_upload']                        = 0;
$_CONFIG['path_to_upload']                        = 'http://'. $_SERVER['HTTP_HOST'] . '/ubr_uploads/'; //Used for web link
$_CONFIG['send_email_on_upload']                  = 0;
$_CONFIG['html_email_support']                    = 0;
$_CONFIG['link_to_upload_in_email']               = 0;
$_CONFIG['email_subject']                         = 'File Upload';
$_CONFIG['to_email_address']                      = 'none@none.com';
$_CONFIG['from_email_address']                    = 'none@none.com';
$_CONFIG['log_uploads']                           = 0;
$_CONFIG['log_dir']                               = '/tmp/ubr_logs/';
$_CONFIG['opera_browser']                         = (strstr(getenv("HTTP_USER_AGENT"), "Opera"))  ? 1 : 0;
$_CONFIG['safari_browser']                        = (strstr(getenv("HTTP_USER_AGENT"), "Safari")) ? 1 : 0;

?>