<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die ;

$item_id = '';
$Itemid = MiwoVideos::getInput()->getInt('Itemid', 0);
if (!empty($Itemid)) {
    $item_id = '&Itemid='.$Itemid;
}

MHtml::_('behavior.tooltip');
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
            <?php if ($this->acl->canManage()) { ?>
            <td class="miwi_filter">
			<?php echo $this->lists['bulk_actions']; ?>
                <button onclick="Miwi.submitform(document.getElementById('bulk_actions').value);" class="button"><?php echo MText::_('Apply'); ?></button>
                &nbsp;&nbsp;&nbsp;
                <?php echo $this->lists['filter_user']; ?>
            </td>
			<button onclick="this.form.submit();" class="button"><?php echo MText::_('Filter'); ?></button>
            <?php } ?>
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
                        <?php echo MText::_('COM_MIWOVIDEOS_TITLE'); ?>
                    </th>

                    <th width="10%" style="text-align: center;">
                        <?php echo MHtml::_('grid.sort', MText::_('MGLOBAL_USERNAME'), 'u.user_login username', $this->lists['order_Dir'], $this->lists['order']); ?>
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

                    $link = MiwoVideos::get('utility')->route('index.php?option=com_miwovideos&view=channels&task=edit&cid[]='.$row->channel_id);
                    $checked = MHtml::_('grid.id', $i, $row->id);

                    ?>
                    <tr class="<?php echo "row$k"; ?>">                        <td style="text-align: center;">
                            <?php echo $checked; ?>
                        </td>

                        <td class="text_left">
                            <a href="<?php echo $link; ?>">
                                <?php echo $row->title; ?>
                            </a>
                        </td>

                        <td class="text_center">
                          <?php if (!MiwoVideos::isDashboard()) { ?>
	                          <a href="<?php echo MiwoVideos::get('utility')->route('index.php?option=com_users&task=user.edit&id='.$row->user_id); ?>">
                              <?php echo $row->username; ?>
                          </a>
                          <?php } else { ?>
                              <?php echo $row->username; ?>
                          <?php } ?>
                      </td>
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
	<input type="hidden" name="view" value="subscriptions" />
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