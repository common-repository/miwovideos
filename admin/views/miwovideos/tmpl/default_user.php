<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die('Restricted access');

$channel = MiwoVideos::get('channels')->getDefaultChannel();
$thumb = MiwoVideos::get('utility')->getThumbPath($channel->id, 'channels', $channel->thumb, null, 'url');
$config = MiwoVideos::getConfig();
?>

          <div class="miwi_paid">
                <strong><?php echo MText::sprintf('MLIB_X_PRO_MEMBERS', 'Frontend Management'); ?></strong><br /><br />
                <?php echo MText::sprintf('MLIB_PRO_MEMBERS_DESC', 'http://miwisoft.com/wordpress-plugins/miwovideos-share-your-videos#pricing', 'MiwoVideos'); ?>
		    </div>
<td valign="top" width="42%" style="padding: 15px 0 0 5px">
    <?php echo MHtml::_('sliders.start', 'miwovideos'); ?>
    <?php echo MHtml::_('sliders.panel', MText::_('COM_MIWOVIDEOS_WELLCOME') . ' ' . $channel->title, 'welcome'); ?>
    <table class="adminlist">
        <tr>
            <td valign="top" width="50%" align="center">
                <table class="wp-list-table widefat">
                    <tr height="40">
                        <td width="%25">
                            <?php echo MText::_('COM_MIWOVIDEOS_CPANEL_CHANNELS'); ?>
                        </td>
                        <td width="%35">
                            <b><?php echo $this->stats['channels'];?></b>
                        </td>
                        <td align="center" style="vertical-align: middle;" rowspan="4">
                            <img src="<?php echo $thumb; ?>" width="140" height="140" style="display: block; margin: auto;" alt="<?php echo $channel->title; ?>" title="<?php echo $channel->title; ?>" align="middle" border="0">
                        </td>
                    </tr>
                    <?php if($config->get('playlists')) { ?>
                        <tr height="40">
                            <td>
                                <?php echo MText::_('COM_MIWOVIDEOS_CPANEL_PLAYLISTS'); ?>
                            </td>
                            <td>
                                <b><?php echo $this->stats['playlists'];?></b>
                            </td>
                        </tr>
                    <?php } ?>
                    <tr height="40">
                        <td>
                            <?php echo MText::_('COM_MIWOVIDEOS_CPANEL_VIDEOS'); ?>
                        </td>
                        <td>
                            <b><?php echo $this->stats['videos'];?></b>
                        </td>
                    </tr>
                    <?php if($config->get('subscriptions')) { ?>
                        <tr height="40">
                            <td>
                                <?php echo MText::_('COM_MIWOVIDEOS_CPANEL_SUBSCRIPTIONS'); ?>
                            </td>
                            <td>
                                <b><?php echo $this->stats['subscriptions'];?></b>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </td>
        </tr>
    </table>

    <?php echo MHtml::_('sliders.end'); ?>
</td>