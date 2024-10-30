<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die ;

MHtml::_('behavior.tooltip');
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
        <button class="button" onclick="submitAdminForm('defaultchannel');return false;"><span class="icon-star"></span> <?php echo MText::_('COM_MIWOVIDEOS_DEFAULT'); ?></button>
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
                    <?php echo MHtml::_('grid.sort', MText::_('COM_MIWOVIDEOS_TITLE'), 'c.title', $this->lists['order_Dir'], $this->lists['order']); ?>
                </th>

                <?php if ($this->acl->canManage()) { ?>
                <th width="80px" style="text-align: center;">
                    <?php echo MHtml::_('grid.sort', MText::_('MGLOBAL_USERNAME'), 'u.user_login username', $this->lists['order_Dir'], $this->lists['order']); ?>
                </th>
                <?php } ?>

                <th width="10%" style="text-align: center;">
                    <?php echo MText::_('COM_MIWOVIDEOS_VIDEOS'); ?>
                </th>

                <th width="5%" style="text-align: center;">
                    <?php echo MHtml::_('grid.sort', MText::_('MSTATUS'), 'c.published', $this->lists['order_Dir'], $this->lists['order']); ?>
                </th>

                <?php if ($this->acl->canManage()) { ?>
                <th width="5%" style="text-align: center;">
                    <?php echo MHtml::_('grid.sort', MText::_('COM_MIWOVIDEOS_FEATURE'), 'c.featured', $this->lists['order_Dir'], $this->lists['order']); ?>
                </th>
                <?php } ?>

                <th width="5%" style="text-align: center;">
                    <?php echo MHtml::_('grid.sort', MText::_('COM_MIWOVIDEOS_DEFAULT'), 'c.default', $this->lists['order_Dir'], $this->lists['order']); ?>
                </th>

                
                <?php if ($this->acl->canManage()) { ?>
                <th width="5%" style="text-align: center;">
                    <?php echo MHtml::_('grid.sort', 'MGRID_HEADING_LANGUAGE', 'c.language', $this->lists['order_Dir'], $this->lists['order']); ?>
                </th>
                <?php } ?>

                <th width="3%" style="text-align: center;">
                    <?php echo MHtml::_('grid.sort', 'MGLOBAL_HITS', 'c.hits', $this->lists['order_Dir'], $this->lists['order']); ?>
                </th>

                <?php if ($this->acl->canManage()) { ?>
                <th width="5px">
                    <?php echo MHtml::_('grid.sort', MText::_('COM_MIWOVIDEOS_ID'), 'c.id', $this->lists['order_Dir'], $this->lists['order']); ?>
                </th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
        <?php
        if (isset($this->cChannels)){
        $k = 0;
        $n = count($this->cChannels);
        for ($i=0; $i < $n; $i++) {
            $row = $this->cChannels[$i];

            $link = MiwoVideos::get('utility')->route('index.php?option=com_miwovideos&view=channels&task=edit&cid[]='.$row->id);

            $checked = MHtml::_('grid.id', $i, $row->id);

            $published = $this->getIcon($i, $task = $row->published == '0' ? 'publish' : 'unpublish', $row->published ? 'publish_y.png' : 'publish_x.png', true);
            $featured = $this->getIcon($i, $task = $row->featured == '0' ? 'feature' : 'unfeature', $row->featured ? 'featured.png' : 'disabled.png', true);
            $default = $this->getIcon($i, $task = $row->default == '0' ? 'defaultchannel' : 'notdefaultchannel', $row->default ? 'default.png' : 'notdefault.png');

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

                <?php if ($this->acl->canManage()) { ?>
                <td class="text_center">
                    <?php if (!MiwoVideos::isDashboard()) { ?>
	                    <a href="<?php echo MiwoVideos::get('utility')->route('index.php?option=com_users&task=user.edit&id='.$row->user_id); ?>">
						<?php echo $row->username; ?>
					</a>
                    <?php } else { ?>
                        <?php echo $row->username; ?>
                    <?php } ?>
                </td>
                <?php } ?>

                <td class="text_center">
					<a href="<?php echo MiwoVideos::get('utility')->route('index.php?option=com_miwovideos&view=videos&filter_channel='.$row->id); ?>">
						<?php echo $row->videos; ?>
					</a>
                </td>

                <td class="text_center">
                    <?php echo $published; ?>
                </td>

                <?php if ($this->acl->canManage()) { ?>
                <td class="text_center">
                    <?php echo $featured; ?>
                </td>
                <?php } ?>

                <td class="text_center">
                    <?php echo $default; ?>
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
	<input type="hidden" name="view" value="channels" />
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