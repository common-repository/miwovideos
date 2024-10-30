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
        <button class="button" onclick="window.top.location = '<?php echo MiwoVideos::get('utility')->route('index.php?option=com_miwovideos&view=reports'); ?>';return false;"><span class="icon-warning"></span> <?php echo MText::_('COM_MIWOVIDEOS_CPANEL_REPORTS'); ?></button>
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
                <button onclick="document.getElementById('search').value='';this.form.submit();" class="button"><?php echo MText::_( 'Reset' ); ?></button>
            </td>

            <td class="miwi_filter">
			<?php echo $this->lists['bulk_actions']; ?>
                <button onclick="Miwi.submitform(document.getElementById('bulk_actions').value);" class="button"><?php echo MText::_('Apply'); ?></button>
                &nbsp;&nbsp;&nbsp;
                <?php echo $this->lists['filter_published']; ?>

                
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
                    <th width="10%" style="text-align: center;">
                        <?php echo MHtml::_('grid.sort', MText::_( 'COM_MIWOVIDEOS_PUBLISHED'), 'rs.published', $this->lists['order_Dir'], $this->lists['order'] ); ?>
                    </th>
                                        <th width="15%" style="text-align: center;">
                        <?php echo MHtml::_('grid.sort', 'MGRID_HEADING_LANGUAGE', 'rs.language', $this->lists['order_Dir'], $this->lists['order']); ?>
                    </th>
                    <th width="5%" style="text-align: center;">
                        <?php echo MHtml::_('grid.sort',  MText::_( 'COM_MIWOVIDEOS_ID'), 'rs.id', $this->lists['order_Dir'], $this->lists['order'] ); ?>
                    </th>
                </tr>
            </thead>

            <tbody>
            <?php
            $k = 0;
            $n = count($this->items);
            for ($i=0; $i < $n; $i++) {
                $row = &$this->items[$i];

                $link = MiwoVideos::get('utility')->route('index.php?option=com_miwovideos&view=reasons&task=edit&cid[]='.$row->id);

                $checked = MHtml::_('grid.id', $i, $row->id);

                $published = $this->getIcon($i, $task = $row->published == '0' ? 'publish' : 'unpublish', $row->published ? 'publish_y.png' : 'publish_x.png', true);
                ?>
                <tr class="<?php echo "row$k"; ?>">                    <td>
                        <?php echo $checked; ?>
                    </td>
                    <td>
                        <?php if (MiwoVideos::get('acl')->canEdit()) { ?>
                        <a href="<?php echo $link; ?>">
                            <?php echo $row->title; ?>
                        </a>
                        <?php } else { ?>
                        <?php echo $row->title; ?>
                        <?php } ?>
                    </td>
                    <td class="text_center">
                        <?php echo $published; ?>
                    </td>
                                        <td class="text_center">
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
	<input type="hidden" name="view" value="reasons" />
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