<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die ;

$ordering = ($this->lists['order'] == 'p.ordering');

?>

<form action="<?php echo MRoute::getActiveUrl(); ?>" method="post" name="adminForm" id="adminForm">
    <?php if (MiwoVideos::isDashboard()) { ?>
    <div style="float: left; width: 99%;">
        <?php if ($this->acl->canCreate()) { ?>
        <button class="button btn-success" onclick="submitAdminForm('add');return false;"><span class="icon-new icon-white"></span> <?php echo MText::_('COM_MIWOVIDEOS_NEW'); ?></button>
        <?php } ?>
        <?php if ($this->acl->canEdit()) { ?>
        <button class="button" onclick="submitAdminForm('edit');return false;"><span class="icon-edit"></span> <?php echo MText::_('COM_MIWOVIDEOS_EDIT'); ?></button>
        <?php } ?>
        <?php if ($this->acl->canEditState()) { ?>
        <button class="button" onclick="submitAdminForm('publish');return false;"><span class="icon-publish"></span> <?php echo MText::_('COM_MIWOVIDEOS_PUBLISH'); ?></button>
        <button class="button" onclick="submitAdminForm('unpublish');return false;"><span class="icon-unpublish"></span> <?php echo MText::_('COM_MIWOVIDEOS_UNPUBLISH'); ?></button>
        <?php } ?>
        <?php if ($this->acl->canCreate()) { ?>
        <button class="button" onclick="submitAdminForm('copy');return false;"><span class="icon-copy"></span> <?php echo MText::_('COM_MIWOVIDEOS_COPY'); ?></button>
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
            <?php echo $this->lists['filter_published']; ?>

            
            <?php if ($this->acl->canManage()) { ?>
            <select name="filter_language" class="inputbox" onchange="this.form.submit()">
                <option value=""><?php echo MText::_('MOPTION_SELECT_LANGUAGE');?></option>
                <?php echo MHtml::_('select.options', MHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->filter_language);?>
            </select>
            <?php } ?>
			<button onclick="this.form.submit();" class="button"><?php echo MText::_('Filter'); ?></button>
        </td>
    </tr>
    </table>
<div id="editcell">
	<table class="wp-list-table widefat">
        <thead>
            <tr>
                
                <th width="20px" style="text-align: center;">
                    <input type="checkbox" name="checkall-toggle" value="" title="<?php echo MText::_('MGLOBAL_CHECK_ALL'); ?>" onclick="Miwi.checkAll(this)" />
                </th>

                <th style="text-align: left;">
                    <?php echo MHtml::_('grid.sort', MText::_('COM_MIWOVIDEOS_TITLE'), 'p.title', $this->lists['order_Dir'], $this->lists['order']); ?>
                </th>

                <th class="title" width="7%">
                    <?php echo MText::_( 'COM_MIWOVIDEOS_CHANNEL'); ?>
                </th>

                <th width="5%" style="text-align: center;">
                    <?php echo MHtml::_('grid.sort', MText::_('MSTATUS'), 'p.published', $this->lists['order_Dir'], $this->lists['order']); ?>
                </th>

                <?php if ($this->acl->canManage()) { ?>
                <th width="5%" style="text-align: center;">
                    <?php echo MHtml::_('grid.sort', MText::_('MFEATURED'), 'p.featured', $this->lists['order_Dir'], $this->lists['order']); ?>
                </th>
                <?php } ?>
				
				<th width="<?php echo MiwoVideos::is30() ? '12' : '8'; ?>%" style="text-align: right;">
					<?php echo MHtml::_('grid.sort', MText::_('MGRID_HEADING_ORDERING'), 'p.ordering', $this->lists['order_Dir'], $this->lists['order']); ?>
					<?php if ($ordering) { ?>
					<?php echo MHtml::_('grid.order', $this->cPlaylists, 'filesave.png', 'saveOrder'); ?>
					<?php } ?>
				</th>

                
                <?php if ($this->acl->canManage()) { ?>
				<th width="5%" style="text-align: center;">
					<?php echo MHtml::_('grid.sort', 'MGRID_HEADING_LANGUAGE', 'p.language', $this->lists['order_Dir'], $this->lists['order']); ?>
				</th>
                <?php } ?>

                <th width="3%" style="text-align: center;">
                    <?php echo MHtml::_('grid.sort', 'MGLOBAL_HITS', 'p.hits', $this->lists['order_Dir'], $this->lists['order']); ?>
                </th>

                <?php if ($this->acl->canManage()) { ?>
                <th width="5px">
                    <?php echo MHtml::_('grid.sort', MText::_('COM_MIWOVIDEOS_ID'), 'p.id', $this->lists['order_Dir'], $this->lists['order']); ?>
                </th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
        <?php
        if (isset($this->cPlaylists)){
        $k = 0;
        $n = count($this->cPlaylists);
        for ($i=0; $i < $n; $i++) {
            $row = $this->cPlaylists[$i];

            $link = MiwoVideos::get('utility')->route('index.php?option=com_miwovideos&view=playlists&task=edit&cid[]='.$row->id);
            $channel_link = MiwoVideos::get('utility')->route('index.php?option=com_miwovideos&view=channels&task=edit&cid[]='.$row->channel_id);

            $checked = MHtml::_('grid.id', $i, $row->id );

            $published = $this->getIcon($i, $task = $row->published == '0' ? 'publish' : 'unpublish', $row->published ? 'publish_y.png' : 'publish_x.png', true);
            $featured = $this->getIcon($i, $task = $row->featured == '0' ? 'feature' : 'unfeature', $row->featured ? 'featured.png' : 'disabled.png', true);

            ?>
            <tr class="<?php echo "row$k"; ?>">                <td style="text-align: center;">
                    <?php echo $checked; ?>
                </td>

                <td class="text_left">
                    <?php if ($this->acl->canEditOwn($row->user_id)) { ?>
                        <a href="<?php echo $link; ?>">
                            <?php echo $row->title; ?>
                        </a>
                    <?php } else { ?>
                        <?php echo $row->title; ?>
                    <?php } ?>
                </td>

                <td class="text_left">
                    <?php if ($this->acl->canEditOwn($row->user_id)) { ?>
                        <a href="<?php echo $channel_link; ?>">
                            <?php echo $row->channel_title; ?>
                        </a>
                    <?php } else { ?>
                        <?php echo $row->channel_title; ?>
                    <?php } ?>
                </td>
				
                <td class="text_center">
                    <?php echo $published; ?>
                </td>

                <?php if ($this->acl->canManage()) { ?>
                <td class="text_center">
                    <?php echo $featured; ?>
                </td>
                <?php } ?>
				
				<td class="ordering" style="text-align: right;">
					<?php if ($ordering) { ?>
					<span><?php echo $this->pagination->orderUpIcon($i, ($row->user_id==0 || $row->user_id == @$this->cPlaylists[$i-1]->user_id),'orderup', 'Move Up', $ordering ); ?></span>
					<span><?php echo $this->pagination->orderDownIcon($i, $n, ($row->user_id ==0 || $row->user_id == @$this->cPlaylists[$i+1]->user_id), 'orderdown', 'Move Down', $ordering ); ?></span>
					<?php } ?>
					<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>				
					<input type="text" name="order[]" size="5" value="<?php echo $row->ordering;?>" style="text-align: center; width: 30px;" <?php echo $disabled; ?> />
				</td>

                
                <?php if ($this->acl->canManage()) { ?>
				<td style="text-align: center;">
					<?php if ($row->language == '*') { ?>
					<?php echo MText::alt('MALL', 'language'); ?>
					<?php } else { ?>
					<?php echo isset($this->langs[$row->language]->title) ? $this->escape($this->langs[$row->language]->title) : MText::_('MUNDEFINED'); ?>
					<?php } ?>
				</td>
                <?php } ?>

                <td class="text_center">
                    <?php echo $row->hits; ?>
                </td>

                <?php if ($this->acl->canManage()) { ?>
                <td class="text_center">
                    <?php echo $row->id; ?>
                </td>
                <?php } ?>

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
	<input type="hidden" name="view" value="playlists" />
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