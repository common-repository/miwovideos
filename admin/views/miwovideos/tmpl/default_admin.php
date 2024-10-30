<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die('Restricted access');
?>

<td valign="top" width="58%">
    <table>
        <tr>
            <td>
                <div id="miwovideos_cpanel" width="30%">
                    <?php
                    $option = MRequest::getWord('option');

                    if (MiwoVideos::isDashboard()) {
                        $link = 'administrator/index.php?option=com_miwovideos';
                        $this->quickIconButton($link, 'icon-48-miwovideos-config.png', MText::_('COM_MIWOVIDEOS_CPANEL_CONFIGURATION'), false, 0, 0, true);
                    }
                    // @TODO : Add this line when release wordpress
                    // else if (MiwoVideos::get('utility')->is30() or MFactory::isW()) {
                    else if (MiwoVideos::get('utility')->is30() or MFactory::isW()) {
                        $uri = (string) MUri::getInstance();
                        $return = urlencode(base64_encode($uri));

                        $link = MiwoVideos::get('utility')->route('index.php?option=com_config&view=component&component=com_miwovideos&path=&amp;return='.$return);
                        $this->quickIconButton($link, 'icon-48-miwovideos-config.png', MText::_('COM_MIWOVIDEOS_CPANEL_CONFIGURATION'));
                    }
                    else {
                        $link = MiwoVideos::get('utility')->route('index.php?option=com_config&view=component&component=com_miwovideos&path=&tmpl=component');
                        $this->quickIconButton($link, 'icon-48-miwovideos-config.png', MText::_('COM_MIWOVIDEOS_CPANEL_CONFIGURATION'), true, 875, 550);
                    }

                    $link = MiwoVideos::get('utility')->route('index.php?option='.$option.'&amp;view=fields');
                    $this->quickIconButton($link, 'icon-48-miwovideos-fields.png', MText::_('COM_MIWOVIDEOS_CPANEL_FIELDS'));

                    $link = MiwoVideos::get('utility')->route('index.php?option='.$option.'&view=restoremigrate');
                    $this->quickIconButton($link, 'icon-48-miwovideos-restore.png', MText::_('COM_MIWOVIDEOS_CPANEL_RESTORE'));

                    $link = MiwoVideos::get('utility')->route('index.php?option='.$option.'&amp;view=upgrade');
                    $this->quickIconButton($link, 'icon-48-miwovideos-upgrade.png', MText::_('COM_MIWOVIDEOS_CPANEL_UPGRADE'));
                    ?>

                    <br /><br /><br /><br /><br /><br /><br /><?php if (!MiwoVideos::is30() and !MFactory::isW()) { ?><br /><br /><?php } ?>

                    <?php

                    $link = MiwoVideos::get('utility')->route('index.php?option='.$option.'&amp;view=categories');
                    $this->quickIconButton($link, 'icon-48-miwovideos-categories.png', MText::_('COM_MIWOVIDEOS_CPANEL_CATEGORIES'));

                    $link = MiwoVideos::get('utility')->route('index.php?option='.$option.'&amp;view=channels');
                    $this->quickIconButton($link, 'icon-48-miwovideos-channels.png', MText::_('COM_MIWOVIDEOS_CPANEL_CHANNELS'));

                    $link = MiwoVideos::get('utility')->route('index.php?option='.$option.'&amp;view=playlists');
                    $this->quickIconButton($link, 'icon-48-miwovideos-playlists.png', MText::_('COM_MIWOVIDEOS_CPANEL_PLAYLISTS'));

                    $link = MiwoVideos::get('utility')->route('index.php?option='.$option.'&amp;view=videos');
                    $this->quickIconButton($link, 'icon-48-miwovideos-videos.png', MText::_('COM_MIWOVIDEOS_CPANEL_VIDEOS'));

                    ?>

                    <br /><br /><br /><br /><br /><br /><br /><?php if (!MiwoVideos::is30() and !MFactory::isW()) { ?><br /><br /><?php } ?>

                    <?php

                    $link = MiwoVideos::get('utility')->route('index.php?option='.$option.'&amp;view=subscriptions');
                    $this->quickIconButton($link, 'icon-48-miwovideos-subscriptions.png', MText::_('COM_MIWOVIDEOS_CPANEL_SUBSCRIPTIONS'));

                    $link = MiwoVideos::get('utility')->route('index.php?option='.$option.'&amp;view=reports');
                    $this->quickIconButton($link, 'icon-48-miwovideos-reports.png', MText::_('COM_MIWOVIDEOS_CPANEL_REPORTS'));

                    $link = MiwoVideos::get('utility')->route('index.php?option='.$option.'&amp;view=files');
                    $this->quickIconButton($link, 'icon-48-miwovideos-files.png', MText::_('COM_MIWOVIDEOS_CPANEL_FILES'));

                    $link = MiwoVideos::get('utility')->route('index.php?option='.$option.'&amp;view=processes');
                    $this->quickIconButton($link, 'icon-48-miwovideos-processes.png', MText::_('COM_MIWOVIDEOS_CPANEL_PROCESSES'));

                    ?>

                    <br /><br /><br /><br /><br /><br /><br /><?php if (!MiwoVideos::is30() and !MFactory::isW()) { ?><br /><br /><?php } ?>

                    <?php
                    $link = MiwoVideos::get('utility')->route('index.php?option='.$option.'&amp;view=support&amp;task=support');
                    $this->quickIconButton($link, 'icon-48-miwovideos-support.png', MText::_('COM_MIWOVIDEOS_CPANEL_SUPPORT'), true, 650, 420);

                    $link = MiwoVideos::get('utility')->route('index.php?option='.$option.'&amp;view=support&amp;task=translators');
                    $this->quickIconButton($link, 'icon-48-miwovideos-translators.png', MText::_('COM_MIWOVIDEOS_CPANEL_TRANSLATORS'), true);

                    $link = 'http://miwisoft.com/wordpress-plugins/miwovideos/changelog?tmpl=component';
                    $this->quickIconButton($link, 'icon-48-miwovideos-changelog.png', MText::_('COM_MIWOVIDEOS_CPANEL_CHANGELOG'), true);

                    $link = 'http://miwisoft.com';
                    $this->quickIconButton($link, 'icon-48-miwisoft.png', 'Miwisoft.com', false, 0, 0, true);
                    ?>
                </div>
            </td>
        </tr>
    </table>
</td>

<td valign="top" width="42%" style="padding: <?php echo ($this->_mainframe->isAdmin() ? '7' : '0'); ?>px 0 0 5px">
    <?php echo MHtml::_('sliders.start', 'miwovideos'); ?>
    <?php echo MHtml::_('sliders.panel', MText::_('COM_MIWOVIDEOS_CPANEL_WELLCOME'), 'welcome'); ?>
    <table class="adminlist">
        <tr>
            <td valign="top" width="50%" align="center">
                <table class="wp-list-table widefat">
                    <?php
                        $rowspan = 5;
                                            ?>
                    <tr height="70">
                        <td width="%25">
                            <?php
                                $icon = 'help';
                                if ($this->info['version_enabled'] == 0) {
                                    $icon = 'noinfo';
                                } elseif ($this->info['version_status'] == 0) {
                                    $icon = 'latest';
                                }

                                $img_path = MURL_MIWOVIDEOS.'/admin/assets/images/icon-48-v-'.$icon.'.png';
                            ?>

                            <img src="<?php echo $img_path; ?>" />
                        </td>
                        <td width="%35">
                            <?php
                                if ($this->info['version_enabled'] == 0) {
                                    echo '<b>'.MText::_('COM_MIWOVIDEOS_CPANEL_VERSION_CHECKER_DISABLED_1').'</b>';
                                } elseif ($this->info['version_status'] == 0) {
                                    echo '<b><font color="green">'.MText::_('COM_MIWOVIDEOS_CPANEL_LATEST_VERSION_INSTALLED').'</font></b>';
                                } elseif($this->info['version_status'] == -1) {
                                    echo '<b><font color="red">'.MText::_('COM_MIWOVIDEOS_CPANEL_OLD_VERSION').'</font></b>';
                                } else {
                                    echo '<b><font color="orange">'.MText::_('COM_MIWOVIDEOS_CPANEL_NEWER_VERSION').'</font></b>';
                                }
                            ?>
                        </td>
                        <td align="center" style="vertical-align: middle;" rowspan="<?php echo $rowspan; ?>">
                            <a href="http://www.miwisoft.com/wordpress-plugins/miwovideos" target="_blank">
                            <img src="<?php echo MURL_MIWOVIDEOS; ?>/admin/assets/images/logo.png" width="140" height="140" style="display: block; margin: auto;" alt="MiwoVideos" title="MiwoVideos" align="middle" border="0">
                            </a>
                        </td>
                    </tr>
                                        <tr height="40">
                        <td>
                            <?php
                                if($this->info['version_status'] == 0 || $this->info['version_enabled'] == 0) {
                                    echo MText::_('COM_MIWOVIDEOS_CPANEL_LATEST_VERSION');
                                } elseif($this->info['version_status'] == -1) {
                                    echo '<b><font color="red">'.MText::_('COM_MIWOVIDEOS_CPANEL_LATEST_VERSION').'</font></b>';
                                } else {
                                    echo '<b><font color="orange">'.MText::_('COM_MIWOVIDEOS_CPANEL_LATEST_VERSION').'</font></b>';
                                }
                            ?>
                        </td>
                        <td>
                            <?php
                                if ($this->info['version_enabled'] == 0) {
                                    echo MText::_('COM_MIWOVIDEOS_CPANEL_VERSION_CHECKER_DISABLED_2');
                                } elseif($this->info['version_status'] == 0) {
                                    echo $this->info['version_latest'];
                                } elseif($this->info['version_status'] == -1) {
                                    // Version output in red
                                    echo '<b><font color="red">'.$this->info['version_latest'].'</font></b>&nbsp;&nbsp;&nbsp;&nbsp;';
                                    ?>
                                    <input type="button" class="button btn-danger" class="button hasTip" value="<?php echo MText::_('COM_MIWOVIDEOS_CPANEL_UPGRADE'); ?>" onclick="upgrade();" />
                                    <?php
                                } else {
                                    echo '<b><font color="orange">'.$this->info['version_latest'].'</font></b>';
                                }
                            ?>
                        </td>
                    </tr>
                    <tr height="40">
                        <td>
                            <?php echo MText::_('COM_MIWOVIDEOS_CPANEL_INSTALLED_VERSION'); ?>
                        </td>
                        <td>
                            <?php
                                if ($this->info['version_enabled'] == 0) {
                                    echo MText::_('COM_MIWOVIDEOS_CPANEL_VERSION_CHECKER_DISABLED_2');
                                } else {
                                    echo $this->info['version_installed'];
                                }
                            ?>
                        </td>
                    </tr>
                    <tr height="40">
                        <td>
                            <?php echo MText::_('COM_MIWOVIDEOS_CPANEL_COPYRIGHT'); ?>
                        </td>
                        <td>
                            <a href="http://miwisoft.com" target="_blank"><?php echo MiwoVideos::get('utility')->getXmlText(MPATH_WP_PLG.'/miwovideos/miwovideos.xml', 'copyright'); ?></a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <?php echo MHtml::_('sliders.panel', MText::_('COM_MIWOVIDEOS_CPANEL_SERVER'), 'server_status'); ?>
    <table class="wp-list-table widefat">
    <?php
        foreach ($this->info['server'] as $server) {
            $color = ($server['value'] == MText::_('MNO')) ? '#FF0000' : '#339900';
        ?>
        <tr>
            <td width="40%">
                <?php echo $server['name']; ?>
            </td>
            <td width="60%">
                <strong style="color:<?php echo $color; ?>"><?php echo $server['value']; ?></strong>
            </td>
        </tr>
    <?php } ?>
    </table>

    <?php echo MHtml::_('sliders.panel', MText::_('COM_MIWOVIDEOS_CPANEL_STATISTICS'), 'stats'); ?>
    <table class="wp-list-table widefat">
        <tr>
            <td width="40%">
                <?php echo MText::_('COM_MIWOVIDEOS_CPANEL_CATEGORIES'); ?>
            </td>
            <td width="60%">
                <b><?php echo $this->stats['categories']; ?></b>
            </td>
        </tr>
        <tr>
            <td width="40%">
                <?php echo MText::_('COM_MIWOVIDEOS_CPANEL_CHANNELS'); ?>
            </td>
            <td width="60%">
                <b><?php echo $this->stats['channels'];?></b>
            </td>
        </tr>
        <tr>
            <td width="40%">
                <?php echo MText::_('COM_MIWOVIDEOS_CPANEL_PLAYLISTS'); ?>
            </td>
            <td width="60%">
                <b><?php echo $this->stats['playlists'];?></b>
            </td>
        </tr>
        <tr>
            <td width="40%">
                <?php echo MText::_('COM_MIWOVIDEOS_CPANEL_VIDEOS'); ?>
            </td>
            <td width="60%">
                <b><?php echo $this->stats['videos'];?></b>
            </td>
        </tr>
    </table>

    <?php echo MHtml::_('sliders.end'); ?>
</td>