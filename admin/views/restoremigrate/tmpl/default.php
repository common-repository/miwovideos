<?php
/**
 * @package		MiwoVideos
 * @copyright	2009-2016 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die('Restricted access');

?>
<fieldset class="adminform" style="background-color: #FFFFFF;">
    <legend><?php echo MText::_('COM_MIWOVIDEOS_RESTOREMIGRATE_BACKUPRESTORE'); ?></legend>
    <table class="noshow" width="100%">
        <tr style="vertical-align: top;">
            <td width="50%">
                <form action="<?php echo MiwoVideos::get('utility')->getActiveUrl(); ?>" enctype="multipart/form-data" method="post" name="adminForm">
                    <fieldset class="adminform" <?php echo MiwoVideos::is30() ? '' : 'style="background-color: #F4F4F4;"'; ?>>
                        <legend><?php echo MText::_('COM_MIWOVIDEOS_RESTOREMIGRATE_MIWOVIDEOS_BACKUP'); ?></legend>
                        <table class="adminform">
                            <tr>
                                <td>
                                    <input class="button btn btn-success" type="submit" name="backup_categories" value="<?php echo MText::_('COM_MIWOVIDEOS_CPANEL_CATEGORIES'); ?>" style="margin-bottom: 3px;"/>
                                    &nbsp;
                                    <input class="button btn btn-success" type="submit" name="backup_channels" value="<?php echo MText::_('COM_MIWOVIDEOS_CPANEL_CHANNELS'); ?>" style="margin-bottom: 3px;"/>
                                    &nbsp;
                                    <input class="button btn btn-success" type="submit" name="backup_playlists" value="<?php echo MText::_('COM_MIWOVIDEOS_CPANEL_PLAYLISTS'); ?>" style="margin-bottom: 3px;"/>
                                    &nbsp;
                                    <input class="button btn btn-success" type="submit" name="backup_playlistvideos" value="<?php echo MText::_('COM_MIWOVIDEOS_CPANEL_PLAYLISTS')." ".MText::_('COM_MIWOVIDEOS_VIDEOS'); ?>" />
                                    &nbsp;
                                    <input class="button btn btn-success" type="submit" name="backup_videos" value="<?php echo MText::_('COM_MIWOVIDEOS_CPANEL_VIDEOS'); ?>" style="margin-bottom: 3px;"/>
                                    &nbsp;
                                    <input class="button btn btn-success" type="submit" name="backup_videoscategories" value="<?php echo MText::_('COM_MIWOVIDEOS_VIDEOS')." ".MText::_('COM_MIWOVIDEOS_CPANEL_CATEGORIES'); ?>" style="margin-bottom: 3px;"/>
                                    &nbsp;
                                    <input class="button btn btn-success" type="submit" name="backup_subscriptions" value="<?php echo MText::_('COM_MIWOVIDEOS_CPANEL_SUBSCRIPTIONS'); ?>" style="margin-bottom: 3px;"/>
                                    &nbsp;
                                    <input class="button btn btn-success" type="submit" name="backup_likes" value="<?php echo MText::_('COM_MIWOVIDEOS_LIKES'); ?>" style="margin-bottom: 3px;"/>
                                    &nbsp;
                                    <input class="button btn btn-success" type="submit" name="backup_reports" value="<?php echo MText::_('COM_MIWOVIDEOS_CPANEL_REPORTS'); ?>" style="margin-bottom: 3px;"/>
                                    &nbsp;
                                    <input class="button btn btn-success" type="submit" name="backup_files" value="<?php echo MText::_('COM_MIWOVIDEOS_CPANEL_FILES'); ?>" style="margin-bottom: 3px;"/>
                                    &nbsp;
                                    <input class="button btn btn-success" type="submit" name="backup_reasons" value="<?php echo MText::_('COM_MIWOVIDEOS_REASONS'); ?>" style="margin-bottom: 3px;"/>
                                </td>
                            </tr>
                        </table>
                    </fieldset>

                    <input type="hidden" name="option" value="com_miwovideos" />
                    <input type="hidden" name="view" value="restoremigrate" />
                    <input type="hidden" name="task" value="backup" />

                    <?php if (MiwoVideos::isDashboard()) { ?>
                    <input type="hidden" name="dashboard" value="1" />
                    <input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
                    <?php } ?>

                    <?php echo MHTML::_('form.token'); ?>
                </form>
            </td>
            <td width="50%">
                <form action="<?php echo MiwoVideos::get('utility')->getActiveUrl(); ?>" enctype="multipart/form-data" method="post" name="adminForm">
                    <fieldset class="adminform" <?php echo MiwoVideos::is30() ? '' : 'style="background-color: #F4F4F4;"'; ?>>
                        <legend><?php echo MText::_('COM_MIWOVIDEOS_RESTOREMIGRATE_MIWOVIDEOS_RESTORE'); ?></legend>
                        <table class="adminform">
                            <tr>
                                <td width="120">
                                    <?php echo MiwoVideos::is30() ? '' : '<label for="install_package">'; ?><?php echo MText::_('COM_MIWOVIDEOS_COMMON_SELECT_FILE'); ?>:<?php echo MiwoVideos::is30() ? '' : '</label>'; ?>&nbsp;&nbsp;&nbsp;<input class="input_box" id="file_restore" name="file_restore" type="file" size="30" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input class="button btn btn-danger" type="submit" name="restore_categories" value="<?php echo MText::_('COM_MIWOVIDEOS_CPANEL_CATEGORIES'); ?>" style="margin-bottom: 3px;"/>
                                    &nbsp;
                                    <input class="button btn btn-danger" type="submit" name="restore_channels" value="<?php echo MText::_('COM_MIWOVIDEOS_CPANEL_CHANNELS'); ?>" style="margin-bottom: 3px;"/>
                                    &nbsp;
                                    <input class="button btn btn-danger" type="submit" name="restore_playlists" value="<?php echo MText::_('COM_MIWOVIDEOS_CPANEL_PLAYLISTS'); ?>" style="margin-bottom: 3px;"/>
                                    &nbsp;
                                    <input class="button btn btn-danger" type="submit" name="restore_playlistvideos" value="<?php echo MText::_('COM_MIWOVIDEOS_CPANEL_PLAYLISTS')." ".MText::_('COM_MIWOVIDEOS_VIDEOS'); ?>" style="margin-bottom: 3px;"/>
                                    &nbsp;
                                    <input class="button btn btn-danger" type="submit" name="restore_videos" value="<?php echo MText::_('COM_MIWOVIDEOS_CPANEL_VIDEOS'); ?>" style="margin-bottom: 3px;"/>
                                    &nbsp;
                                    <input class="button btn btn-danger" type="submit" name="restore_videoscategories" value="<?php echo MText::_('COM_MIWOVIDEOS_VIDEOS')." ".MText::_('COM_MIWOVIDEOS_CPANEL_CATEGORIES'); ?>" style="margin-bottom: 3px;"/>
                                    &nbsp;
                                    <input class="button btn btn-danger" type="submit" name="restore_subscriptions" value="<?php echo MText::_('COM_MIWOVIDEOS_CPANEL_SUBSCRIPTIONS'); ?>" style="margin-bottom: 3px;"/>
                                    &nbsp;
                                    <input class="button btn btn-danger" type="submit" name="restore_likes" value="<?php echo MText::_('COM_MIWOVIDEOS_LIKES'); ?>" style="margin-bottom: 3px;"/>
                                    &nbsp;
                                    <input class="button btn btn-danger" type="submit" name="restore_reports" value="<?php echo MText::_('COM_MIWOVIDEOS_CPANEL_REPORTS'); ?>" style="margin-bottom: 3px;"/>
                                    &nbsp;
                                    <input class="button btn btn-danger" type="submit" name="restore_files" value="<?php echo MText::_('COM_MIWOVIDEOS_CPANEL_FILES'); ?>" style="margin-bottom: 3px;"/>
                                    &nbsp;
                                    <input class="button btn btn-danger" type="submit" name="restore_reasons" value="<?php echo MText::_('COM_MIWOVIDEOS_REASONS'); ?>" style="margin-bottom: 3px;"/>
                                </td>
                            </tr>
                        </table>
                    </fieldset>

                    <input type="hidden" name="option" value="com_miwovideos" />
                    <input type="hidden" name="view" value="restoremigrate" />
                    <input type="hidden" name="task" value="restore" />

                    <?php if (MiwoVideos::isDashboard()) { ?>
                    <input type="hidden" name="dashboard" value="1" />
                    <input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
                    <?php } ?>

                    <?php echo MHTML::_('form.token'); ?>
                </form>
            </td>
        </tr>
    </table>
</fieldset>

<fieldset class="adminform" style="background-color: #FFFFFF;">
	<legend><?php echo MText::_('COM_MIWOVIDEOS_RESTOREMIGRATE_MIGRATE'); ?></legend>
	<div class="miwi_paid">
		<strong><?php echo MText::sprintf('MLIB_X_PRO_MEMBERS', 'Migrate Tools'); ?></strong><br /><br />
		<?php echo MText::sprintf('MLIB_PRO_MEMBERS_DESC', 'http://miwisoft.com/wordpress-plugins/miwovideos-share-your-videos#pricing', 'MiwoVideos'); ?>
	</div>
</fieldset>
