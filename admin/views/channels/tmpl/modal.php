<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die ;

MHtml::addIncludePath(MPATH_COMPONENT.'/helpers/html');
MHtml::_('behavior.tooltip');

$field		= MRequest::getCmd('field');
$function	= 'jSelectChannel_'.$field;
//$listOrder	= $this->escape($this->state->get('list.ordering'));
//$listDirn	= $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo MiwoVideos::get('utility')->route('index.php?option=com_miwovideos&view=channels&layout=modal&tmpl=component&groups='.MRequest::getVar('groups', '', 'default', 'BASE64').'&excluded='.MRequest::getVar('excluded', '', 'default', 'BASE64')); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset class="filter">
		<div class="left">
			<label for="filter_search"><?php echo MText::_('MSEARCH_FILTER'); ?></label>
            <input type="text" name="search" id="search" value="<?php echo $this->lists['search'];?>" class="text_area search-query" onchange="document.adminForm.submit();" />
            <button onclick="this.form.submit();" class="button"><?php echo MText::_( 'Go' ); ?></button>
            <button onclick="document.getElementById('search').value='';this.form.submit();" class="button"><?php echo MText::_('Reset'); ?></button>
			<button type="button" onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>('', '<?php echo MText::_('MLIB_FORM_SELECT_USER') ?>');"><?php echo MText::_('MOPTION_NO_USER')?></button>
			    <div id="editcell">
        <table class="wp-list-table widefat">
            <thead>
            <tr>
                
                <th width="20" style="text-align: center;">
                    <input type="checkbox" name="checkall-toggle" value="" title="<?php echo MText::_('MGLOBAL_CHECK_ALL'); ?>" onclick="Miwi.checkAll(this)" />
                </th>

                <th style="text-align: left;">
                    <?php echo MHtml::_('grid.sort', MText::_('COM_MIWOVIDEOS_TITLE'), 'c.title', $this->lists['order_Dir'], $this->lists['order']); ?>
                </th>


                <th width="10%" style="text-align: center;">
                    <?php echo MHtml::_('grid.sort', MText::_('MGLOBAL_USERNAME'), 'u.user_login username', $this->lists['order_Dir'], $this->lists['order']); ?>
                </th>


                <th width="5%" style="text-align: center;">
                    <?php echo MHtml::_('grid.sort', MText::_('MSTATUS'), 'c.published', $this->lists['order_Dir'], $this->lists['order']); ?>
                </th>

                <th width="5%" style="text-align: center;">
                    <?php echo MHtml::_('grid.sort', MText::_('COM_MIWOVIDEOS_FEATURE'), 'c.featured', $this->lists['order_Dir'], $this->lists['order']); ?>
                </th>

                

                <th width="3%" style="text-align: center;">
                    <?php echo MHtml::_('grid.sort', 'MGLOBAL_HITS', 'c.hits', $this->lists['order_Dir'], $this->lists['order']); ?>
                </th>

                <th width="3%">
                    <?php echo MHtml::_('grid.sort', MText::_('COM_MIWOVIDEOS_ID'), 'c.id', $this->lists['order_Dir'], $this->lists['order']); ?>
                </th>
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

                    $checked = MHtml::_('grid.id', $i, $row->id );



                        $img = $row->published ? 'tick.png' : 'publish_x.png';
                        $alt = $row->published ? MText::_('MPUBLISHED') : MText::_('MUNPUBLISHED');
                        $published = MHtml::_('image', 'admin/' . $img, $alt, null, true);

                        $img = $row->featured ? 'featured.png' : 'disabled.png';
                        $featured = MHtml::_('image', 'admin/' . $img, '', null, true);


                    ?>
                    <tr class="<?php echo "row$k"; ?>">                        <td style="text-align: center;">
                            <?php echo $checked; ?>
                        </td>

                        <td class="text_left">
                            <a class="pointer" onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>('<?php echo $row->id; ?>', '<?php echo $this->escape(addslashes($row->title)); ?>');">
                                <?php echo $row->title; ?>
                            </a>
                        </td>

                        <td class="text_center">
	                        <a href="<?php echo MiwoVideos::get('utility')->route('index.php?option=com_users&task=user.edit&id='.$row->user_id); ?>" target="_top">
                                <?php echo $row->username; ?>
                            </a>
                        </td>

                        <td class="text_center">
                            <?php echo $published; ?>
                        </td>

                        <td class="text_center">
                            <?php echo $featured; ?>
                        </td>

                        
                        <td class="text_center">
                            <?php echo $row->hits; ?>
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
    <input type="hidden" name="view" value="channels" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
    <?php echo MHtml::_('form.token'); ?>

    <script type="text/javascript">
        function submitbutton(pressbutton) {
            if (pressbutton == 'add_registrant') {
                var form = document.adminForm;
                if (form.video_id.value == 0) {
                    alert("<?php echo MText::_('COM_MIWOVIDEOS_CHOOSE_VIDEO_TO_ADD'); ?>");
                    form.video_id.focus();
                    return
                }
            }
            submitform(pressbutton);
        }
    </script>
</form>
