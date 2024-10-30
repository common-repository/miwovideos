<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die('Restricted access');
			

?>

<div id="editcell">
    <table class="wp-list-table widefat">
        <thead>
            <tr>
                <th width="5" style="text-align: center;">
                    <?php echo MText::_('COM_MIWOVIDEOS_ID'); ?>
                </th>

                <th width="20%" style="text-align: left;">
                    <?php echo MText::_('COM_MIWOVIDEOS_FILES_TYPE'); ?>
                </th>

                <th style="text-align: left;">
                    <?php echo MText::_('COM_MIWOVIDEOS_FILES_PATH'); ?>
                </th>

                <th width="10%" style="text-align: center;">
                    <?php echo MText::_('COM_MIWOVIDEOS_FILES_EXTENSION'); ?>
                </th>

                <th width="10%" style="text-align: center;">
                    <?php echo MText::_('COM_MIWOVIDEOS_FILES_SIZE'); ?>
                </th>

                <th width="10%" style="text-align: center;">
                    <?php echo MText::_('COM_MIWOVIDEOS_FILES_DOWNLOAD'); ?>
                </th>
            </tr>
        </thead>
        <tbody>
        <?php
        if (!empty($this->files)){
            $k = 0;
            foreach ($this->files as $file) {
                $p_type = MiwoVideos::get('processes')->getTypeTitle($file->process_type);

                if ($file->process_type < 7) {
                    $file_path = MiwoVideos::get('utility')->getThumbPath($file->video_id, 'videos', $file->source, null, 'default');
                }
                else {
                    $p_size = MiwoVideos::get('processes')->getTypeSize($file->process_type);

                    $file_path = MiwoVideos::get('utility')->getVideoFilePath($file->video_id, $p_size, $file->source);
                }

                $item = MiwoVideos::getTable('MiwovideosVideos');
                $item->load($file->video_id);
                if ($file->process_type == 100) { // HTML5 formats
                    if ($file->ext == 'jpg') {
                        $file_path = MiwoVideos::get('utility')->getThumbPath($file->video_id, 'videos', $file->source, null, 'default');
                        $p_type = 'Thumbnail';
                    } elseif ($file->ext == 'mp4' or $file->ext == 'webm' or $file->ext == 'ogg' or $file->ext == 'ogv') {
                        $default_size = MiwoVideos::get('utility')->getVideoSize($item->id, $item->source);
                        $file_path = MiwoVideos::get('utility')->getVideoFilePath($file->video_id, $default_size, $file->source);
                        $p_type .= " (".$default_size."p)";
                    }
                } elseif ($file->process_type == 200) { // Original File
                    $file_path = MiwoVideos::get('utility')->getVideoFilePath($item->id, 'orig', $item->source);
                    $p_type = 'Original';
                } else {
                    $p_size = MiwoVideos::get('processes')->getTypeSize($file->process_type);
                    if ($file->process_type < 7) {
                        $file_path = MiwoVideos::get('utility')->getThumbPath($file->video_id, 'videos', $file->source, $p_size, 'default');
                        $file->ext = 'jpg';
                    } else {
                        $file_path = MiwoVideos::get('utility')->getVideoFilePath($file->video_id, $p_size, $file->source);
                    }
                }

                ?>
                <tr class="<?php echo "row$k"; ?>">
                    <td style="text-align: center;">
                        <?php echo $file->id; ?>
                    </td>

                    <td style="text-align: left;">
                        <?php echo $p_type; ?>
                    </td>

                    <td style="text-align: left;">
                        <?php echo $file_path; ?>
                    </td>

                    <td style="text-align: center;">
                        <?php echo $file->ext; ?>
                    </td>

                    <td style="text-align: center;">
                        <?php echo MiwoVideos::get('utility')->getFilesizeFromNumber($file->file_size); ?>
                    </td>

                    <td style="text-align: center;">
                        <a href="<?php echo MUri::root().$file_path; ?>">
                            <?php echo MText::_('COM_MIWOVIDEOS_FILES_DOWNLOAD'); ?>
                        </a>
                    </td>

                </tr>
                <?php
                $k = 1 - $k;
            }
        }
        ?>
        </tbody>
    </table>
</div>