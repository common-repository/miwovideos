<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die ;

$ordering = ($this->lists['order'] == 'p.ordering');

$item_id = '';
$Itemid = MiwoVideos::getInput()->getInt('Itemid', 0);
if (!empty($Itemid)) {
    $item_id = '&Itemid='.$Itemid;
}
?>
<form action="<?php echo MRoute::getActiveUrl(); ?>" method="post" name="adminForm" id="adminForm">
    <?php if (MiwoVideos::isDashboard()) { ?>
    <div style="float: left; width: 99%;">
        <?php if ($this->acl->canCreate()) { ?>
        <button class="button btn-success" ><span class="icon-new icon-white"></span> <?php echo MText::_('COM_MIWOVIDEOS_UPLOAD'); ?></button>
        <?php } ?>
        <?php if ($this->acl->canEdit()) { ?>
        <button class="button" onclick="submitAdminForm('process');return false;"><span class="icon-play-2"></span> <?php echo MText::_('COM_MIWOVIDEOS_PROCESS'); ?></button>
        <button class="button" onclick="submitAdminForm('processall');return false;"><span class="icon-play"></span> <?php echo MText::_('COM_MIWOVIDEOS_PROCESS_ALL'); ?></button>
        <button class="button" onclick="submitAdminForm('processforce');return false;"><span class="icon-forward"></span> <?php echo MText::_('COM_MIWOVIDEOS_PROCESS_FORCE'); ?></button>
        <?php } ?>
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
            <?php echo $this->lists['filter_status']; ?>
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

                <th width="5%" style="text-align: left;">
                    <?php echo MHtml::_('grid.sort', MText::_('COM_MIWOVIDEOS_VIDEO_ID'), 'v.id', $this->lists['order_Dir'], $this->lists['order']); ?>
                </th>

                <th width="83%" style="text-align: left;">
                    <?php echo MHtml::_('grid.sort', MText::_('COM_MIWOVIDEOS_TITLE'), 'pt.title', $this->lists['order_Dir'], $this->lists['order']); ?>
                </th>

                <th width="5%" style="text-align: center;">
                    <?php echo MHtml::_('grid.sort', MText::_('MSTATUS'), 'p.status', $this->lists['order_Dir'], $this->lists['order']); ?>
                </th>

                <th width="3%" style="text-align: center;">
                    <?php echo MHtml::_('grid.sort', MText::_('COM_MIWOVIDEOS_ID'), 'p.id', $this->lists['order_Dir'], $this->lists['order']); ?>
                </th>
            </tr>
        </thead>
        <tbody>
        <?php
        if (isset($this->items)){
        $k = 0;
        $n = count($this->items);
        for ($i=0; $i < $n; $i++) {
            $row = $this->items[$i];

            $video_link = MiwoVideos::get('utility')->route('index.php?option=com_miwovideos&view=videos&task=edit&cid[]='.$row->video_id);
            $url = MiwoVideos::get('utility')->route('index.php?option=com_miwovideos&view=processes&task=edit&cid[]='.$row->id);

            $checked = MHtml::_('grid.id', $i, $row->id );

			switch ($row->status) {
                case 1:
                    $status = MText::_('COM_MIWOVIDEOS_SUCCESSFUL');
                    break;
                case 2:
                    $status = MText::_('COM_MIWOVIDEOS_FAILED');
                    break;
                case 3:
                    $status = MText::_('COM_MIWOVIDEOS_PROCESSING');
                    break;
                default: // 0
                    $status = MText::_('COM_MIWOVIDEOS_QUEUED');
                    break;
            }

            ?>
            <tr class="<?php echo "row$k"; ?>">                <td style="text-align: center;">
                    <?php echo $checked; ?>
                </td>

                <td class="text_center">
                    <?php if (MiwoVideos::get('acl')->canEdit()) { ?>
                        <a href="<?php echo $video_link; ?>">
                            <?php echo $row->video_id; ?>
                        </a>
                    <?php } else { ?>
                        <?php echo $row->video_id; ?>
                    <?php } ?>
                </td>

                <td class="text_left">
                    <a href="<?php echo $url; ?>"><?php echo $row->title; ?></a>
                </td>
				
                <td class="text_center">
                    <?php echo $status; ?>
                </td>

                <td class="text_center">
                    <?php echo $row->id; ?>
                </td>

            </tr>
            <?php
            $k = 1 - $k;
        } } ?>
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
	<input type="hidden" name="view" value="processes" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />

    <?php if (MiwoVideos::isDashboard()) { ?>
    <input type="hidden" name="dashboard" value="1" />
    <input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>" />
        <?php } ?>

    <?php echo MHtml::_('form.token'); ?>
</form>