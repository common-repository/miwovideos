<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die ;
?>

<form action="<?php echo MRoute::getActiveUrl(); ?>" method="post" name="adminForm" id="adminForm">
    <?php if (MiwoVideos::isDashboard()) { ?>
    <div style="float: left; width: 99%;">
        <?php if ($this->acl->canDelete()) { ?>
        <button class="button" onclick="submitAdminForm('delete');return false;"><span class="icon-delete"></span> <?php echo MText::_('COM_MIWOVIDEOS_DELETE'); ?></button>
        <?php } ?>
	    <button class="button" onclick="submitAdminForm('back');return false;">
		    <span class="icon-undo"></span> <?php echo MText::_('COM_MIWOVIDEOS_BACK_TO_DASHBOARD'); ?></button>
    </div>
    <br/>
    <br/>
    <?php } ?>
    <table width="100%">
    <tr>
        <td class="miwi_search">
            <?php echo MText::_( 'Filter' ); ?>:
            <input type="text" name="search" id="search" value="<?php echo $this->lists['search'];?>" class="text_area search-query" onchange="document.adminForm.submit();" />
            <button onclick="this.form.submit();" class="button"><?php echo MText::_( 'Go' ); ?></button>
            <button onclick="document.getElementById('search').value='';this.form.submit();" class="button"><?php echo MText::_('Reset'); ?></button>
        </td>

        <td class="miwi_filter">
			<?php echo $this->lists['bulk_actions']; ?>
                <button onclick="Miwi.submitform(document.getElementById('bulk_actions').value);" class="button"><?php echo MText::_('Apply'); ?></button>
                &nbsp;&nbsp;&nbsp;
            <?php echo $this->lists['filter_published']; ?>
			<button onclick="this.form.submit();" class="button"><?php echo MText::_('Filter'); ?></button>
        </td>
    </tr>
    </table>
<div id="editcell">
	<table class="wp-list-table widefat">
        <thead>
            <tr>
                
                <th width="20" style="text-align: center;">
                    <input type="checkbox" name="checkall-toggle" value="" title="<?php echo MText::_('MGLOBAL_CHECK_ALL'); ?>" onclick="Miwi.checkAll(this)" />
                </th>

                <th style="text-align: left;">
                    <?php echo MHtml::_('grid.sort', MText::_('COM_MIWOVIDEOS_VIDEO'), 'f.video_id', $this->lists['order_Dir'], $this->lists['order']); ?>
                </th>

                <th style="text-align: left;">
                    <?php echo MHtml::_('grid.sort', MText::_('COM_MIWOVIDEOS_FILES_TYPE'), 'f.process_type', $this->lists['order_Dir'], $this->lists['order']); ?>
                </th>

                <?php if (!MiwoVideos::isDashboard()) { ?>
                <th style="text-align: left;">
                    <?php echo MText::_('COM_MIWOVIDEOS_FILES_PATH'); ?>
                </th>
                <?php } ?>

                <th width="5%" style="text-align: center;">
                    <?php echo MHtml::_('grid.sort', MText::_('MSTATUS'), 'f.published', $this->lists['order_Dir'], $this->lists['order']); ?>
                </th>

                <th width="5%" style="text-align: center;">
                    <?php echo MHtml::_('grid.sort', MText::_('COM_MIWOVIDEOS_FILES_EXTENSION'), 'f.ext', $this->lists['order_Dir'], $this->lists['order']); ?>
                </th>

                <th width="10%" style="text-align: center;">
                    <?php echo MHtml::_('grid.sort', MText::_('COM_MIWOVIDEOS_FILES_SIZE'), 'f.file_size', $this->lists['order_Dir'], $this->lists['order']); ?>
                </th>

                <th style="text-align: center;">
                    <?php echo MText::_('COM_MIWOVIDEOS_FILES_DOWNLOAD'); ?>
                </th>

                <?php if (!MiwoVideos::isDashboard()) { ?>
                <th width="3%">
                    <?php echo MHtml::_('grid.sort', MText::_('COM_MIWOVIDEOS_ID'), 'f.id', $this->lists['order_Dir'], $this->lists['order']); ?>
                </th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
        <?php
        if (!empty($this->items)){
            $k = 0;
            $n = count($this->items);

            for ($i=0; $i < $n; $i++) {
                $row = $this->items[$i];

                $video_link = MiwoVideos::get('utility')->route('index.php?option=com_miwovideos&view=videos&task=edit&cid[]='.$row->video_id);

                $p_type = MiwoVideos::get('processes')->getTypeTitle($row->process_type);

                $item = MiwoVideos::getTable('MiwovideosVideos');
                $item->load($row->video_id);
                if ($row->process_type == 100) { // HTML5 formats
                    if ($row->ext == 'jpg') {
                        $file_path = MiwoVideos::get('utility')->getThumbPath($row->video_id, 'videos', $row->source, null, 'default');
                        $p_type = 'Thumbnail';
                    } elseif ($row->ext == 'mp4' or $row->ext == 'webm' or $row->ext == 'ogg' or $row->ext == 'ogv') {
                        $default_size = MiwoVideos::get('utility')->getVideoSize($item->id, $item->source);
                        $file_path = MiwoVideos::get('utility')->getVideoFilePath($row->video_id, $default_size, $row->source);
                        $p_type .= " (".$default_size."p)";
                    }
                } elseif ($row->process_type == 200) { // Original File
                    $file_path = MiwoVideos::get('utility')->getVideoFilePath($item->id, 'orig', $item->source);
                    $p_type = 'Original';
                } else {
                    $p_size = MiwoVideos::get('processes')->getTypeSize($row->process_type);
                    if ($row->process_type < 7) {
                        $file_path = MiwoVideos::get('utility')->getThumbPath($row->video_id, 'videos', $row->source, $p_size, 'default');
                        $row->ext = 'jpg';
                    } else {
                        $file_path = MiwoVideos::get('utility')->getVideoFilePath($row->video_id, $p_size, $row->source);
                    }
                }

                $published = $this->getIcon($i, $task = $row->published == '0' ? 'publish' : 'unpublish', $row->published ? 'publish_y.png' : 'publish_x.png', true);

                $checked = MHtml::_('grid.id', $i, $row->id);

                ?>
                <tr class="<?php echo "row$k"; ?>">                    <td style="text-align: center;">
                        <?php echo $checked; ?>
                    </td>

                    <td style="text-align: left;">
                        <?php if ($this->acl->canEditOwn($row->user_id)) { ?>
                            <a href="<?php echo $video_link; ?>">
                                <?php echo $row->video_title; ?>
                            </a>
                        <?php } else { ?>
                            <?php echo $row->video_title; ?>
                        <?php } ?>
                    </td>

                    <td style="text-align: left;">
                        <?php echo $p_type; ?>
                    </td>

                    <?php if (!MiwoVideos::isDashboard()) { ?>
                    <td style="text-align: left;">
                        <?php echo $file_path; ?>
                    </td>
                    <?php } ?>

                    <td class="text_center">
                        <?php echo $published; ?>
                    </td>

                    <td style="text-align: center;">
                        <?php echo $row->ext; ?>
                    </td>

                    <td style="text-align: center;">
                        <?php echo MiwoVideos::get('utility')->getFilesizeFromNumber($row->file_size); ?>
                    </td>

                    <td style="text-align: center;">
                        <a href="<?php echo MUri::root().$file_path; ?>">
                            <?php echo MText::_('COM_MIWOVIDEOS_FILES_DOWNLOAD'); ?>
                        </a>
                    </td>

                    <?php if (!MiwoVideos::isDashboard()) { ?>
                    <td class="text_center">
                        <?php echo $row->id; ?>
                    </td>
                    <?php } ?>

                </tr>
                <?php
                $k = 1 - $k;
            }
        }
        ?>
        </tbody>
        <tfoot>
       		<tr>
       			<td colspan="30">
       				<?php echo $this->pagination->getListFooter(); ?>
       			</td>
       		</tr>
       	</tfoot>
	</table>
	</div>
	
	<input type="hidden" name="option" value="com_miwovideos" />
	<input type="hidden" name="view" value="files" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />

    <?php if (MiwoVideos::isDashboard()) { ?>
    <input type="hidden" name="dashboard" value="1" />
    <input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
        <?php } ?>

    <?php echo MHtml::_('form.token'); ?>
</form>