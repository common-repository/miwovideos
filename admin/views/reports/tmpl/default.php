<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die ;

MHtml::_('behavior.modal', 'a.reasons_message');
$utility = MiwoVideos::get('utility');
?>
<form action="<?php echo $utility->getActiveUrl(); ?>" method="post" name="adminForm" id="adminForm">
    <?php if (MiwoVideos::isDashboard()) { ?>
    <div style="float: left; width: 99%;">
	    <button class="button" onclick="window.top.location = '<?php echo $utility->route('index.php?option=com_miwovideos&view=reasons'); ?>';return false;"><span class="icon-checkbox-partial"></span> <?php echo MText::_('COM_MIWOVIDEOS_REASONS'); ?></button>
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
                <button onclick="document.getElementById('search').value='';this.form.submit();" class="button"><?php echo MText::_( 'Reset' ); ?></button>
            </td>

            <td class="miwi_filter">
			<?php echo $this->lists['bulk_actions']; ?>
                <button onclick="Miwi.submitform(document.getElementById('bulk_actions').value);" class="button"><?php echo MText::_('Apply'); ?></button>
                &nbsp;&nbsp;&nbsp;
                <?php echo $this->lists['filter_reason']; ?>
                <?php echo $this->lists['filter_type']; ?>

                <select name="filter_language" class="inputbox" onchange="this.form.submit()">
                    <option value=""><?php echo MText::_('MOPTION_SELECT_LANGUAGE');?></option>
                    <?php echo MHtml::_('select.options', MHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->filter_language);?>
                </select>
			<button onclick="this.form.submit();" class="button"><?php echo MText::_('Filter'); ?></button>
            </td>
        </tr>
    </table>
    <div id="editcell">
        <table class="wp-list-table widefat">
        <thead>
            <tr>
                                <th width="20">
                    <input type="checkbox" name="checkall-toggle" value="" title="<?php echo MText::_('MGLOBAL_CHECK_ALL'); ?>" onclick="Miwi.checkAll(this)" />
                </th>
                <th class="title" style="text-align: left;">
                    <?php echo MHtml::_('grid.sort',  MText::_('COM_MIWOVIDEOS_TITLE'), 'rs.title', $this->lists['order_Dir'], $this->lists['order'] ); ?>
                </th>
                <th class="title" width="7%">
                    <?php echo MText::_( 'COM_MIWOVIDEOS_CHANNEL'); ?>
                </th>
                <th width="20%">
                    <?php echo MHtml::_('grid.sort', MText::_( 'COM_MIWOVIDEOS_ITEM_TITLE'), 'v.item_title', $this->lists['order_Dir'], $this->lists['order'] ); ?>
                </th>
                <th width="5%">
                    <?php echo MHtml::_('grid.sort', MText::_( 'COM_MIWOVIDEOS_TYPE'), 'r.item_type', $this->lists['order_Dir'], $this->lists['order'] ); ?>
                </th>
                <th class="title" width="10%">
                    <?php echo MHtml::_('grid.sort',  MText::_( 'COM_MIWOVIDEOS_DATE'), 'r.created', $this->lists['order_Dir'], $this->lists['order'] ); ?>
                </th>
                <th width="5%">
                    <?php echo MHtml::_('grid.sort', 'MGRID_HEADING_LANGUAGE', 'r.lang_code', $this->lists['order_Dir'], $this->lists['order']); ?>
                </th>
                <th width="1%">
                    <?php echo MHtml::_('grid.sort',  MText::_( 'COM_MIWOVIDEOS_ID'), 'r.id', $this->lists['order_Dir'], $this->lists['order'] ); ?>
                </th>
            </tr>
        </thead>
        <tbody>
        <?php
        $k = 0;
        $n = count($this->items);
        for ($i=0; $i < $n; $i++) {
            $row = &$this->items[$i];

	        $link         = $utility->route('index.php?option=com_miwovideos&view=reasons&task=edit&cid='.$row->reason_id);
	        $message_link = $utility->route('index.php?option=com_miwovideos&view=reports&layout=details&tmpl=component&cid='.$row->id);
            $channel_id = MiwoVideos::get('channels')->getDefaultChannel()->id;
	        $channel_link = $utility->route('index.php?option=com_miwovideos&view=channels&channel_id='.$channel_id);

            if ($row->item_type == 'videos') {
                $context = 'video';
	            $item_editlink = $utility->route('index.php?option=com_miwovideos&view=videos&task=edit&cid[]='.$row->item_id);
            } else {
                $context = 'channel';
	            $item_editlink = $utility->route('index.php?option=com_miwovideos&view=channels&task=edit&cid[]='.$row->item_id);
            }

            $item_viewlink = MUri::root().'index.php?option=com_miwovideos&view='.$context.'&'.$context.'_id='.$row->item_id;


            $checked = MHtml::_('grid.id', $i, $row->id);

            ?>
            <tr class="<?php echo "row$k"; ?>">                <td>
                    <?php echo $checked; ?>
                </td>
                <td>
                    <a href="<?php echo $link; ?>">
                        <?php echo $row->title; ?>
                    </a>
                    <a class="reasons_message" style="font-size: 11px;" href="<?php echo $message_link; ?>" rel="{handler: 'iframe', size: {x: 400, y: 200}}">
                        [<?php echo MText::_('COM_MIWOVIDEOS_MESSAGE'); ?>]
                    </a>
                </td>
                <td>
                    <?php if ($this->acl->canEdit()) { ?>
                        <a href="<?php echo $channel_link; ?>">
                            <?php echo $row->channel_title; ?>
                        </a>
                    <?php } else { ?>
                        <?php echo $row->channel_title; ?>
                    <?php } ?>
                </td>
                <td>
                    <?php echo $row->item_title; ?>
                    <?php if ($this->acl->canEdit()) { ?>
                        <a style="font-size: 11px;" href="<?php echo $item_editlink; ?>">
                            [<?php echo MText::_('COM_MIWOVIDEOS_EDIT'); ?>]
                        </a>
                    <?php } ?>
                    <a style="font-size: 11px;" href="<?php echo $item_viewlink; ?>">
                        [<?php echo MText::_('COM_MIWOVIDEOS_VIEW'); ?>]
                    </a>
                </td>
                <td>
                    <?php echo ucfirst($row->item_type); ?>
                </td>
                <td>
                    <?php echo MHtml::_('date', $row->created, MText::_('DATE_FORMAT_LC4')); ?>
                </td>
                <td class="center nowrap">
                    <?php if ($row->language == '*') { ?>
                    <?php echo MText::alt('MALL', 'language'); ?>
                    <?php } else { ?>
                    <?php echo isset($this->langs[$row->language]->title) ? $this->escape($this->langs[$row->language]->title) : MText::_('MUNDEFINED'); ?>
                    <?php } ?>
                </td>
                <td class="text_center">
                    <?php echo $row->id; ?>
                </td>
            </tr>
            <?php
            $k = 1 - $k;
        }
        ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="11">
                    <?php echo $this->pagination->getListFooter(); ?>
                </td>
            </tr>
        </tfoot>
    </table>
	</div>
	<input type="hidden" name="option" value="com_miwovideos" />
	<input type="hidden" name="view" value="reports" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />

    <?php if (MiwoVideos::isDashboard()) { ?>
    <input type="hidden" name="dashboard" value="1" />
    <input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
    <?php } ?>

	<?php echo MHtml::_( 'form.token' ); ?>
</form>