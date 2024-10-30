<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die;

$editor = MFactory::getEditor();
?>
<form action="<?php echo MRoute::getActiveUrl(); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" class="form form-horizontal">
    <?php if (MiwoVideos::isDashboard()) { ?>
        <div style="float: left; width: 99%; margin-left: 10px;">
            <button class="button btn-success" onclick="Miwi.submitbutton('apply')"><span class="icon-apply icon-white"></span> <?php echo MText::_('COM_MIWOVIDEOS_SAVE'); ?></button>
            <button class="button" onclick="Miwi.submitbutton('save')"><span class="icon-save"></span> <?php echo MText::_('COM_MIWOVIDEOS_SAVE_CLOSE'); ?></button>
            <?php if ($this->acl->canCreate()) { ?>
            <button class="button" onclick="Miwi.submitbutton('save2new')"><span class="icon-save-new"></span> <?php echo MText::_('COM_MIWOVIDEOS_SAVE_NEW'); ?></button>
            <?php } ?>
            <button class="button" onclick="Miwi.submitbutton('cancel')"><span class="icon-cancel"></span> <?php echo MText::_('COM_MIWOVIDEOS_CANCEL'); ?></button>
        </div>
        <br/>
        <br/>
    <?php } ?>

    <?php echo MHtml::_('tabs.start', 'miwovideos', array('useCookie'=>1)); ?>

        <!-- Details -->
        <?php echo MHtml::_('tabs.panel', MText::_('COM_MIWOVIDEOS_DETAILS'), 'details'); ?>
        <table class="admintable" width="100%">
            <tr>
                <td class="key2">
                    <?php echo MText::_( 'COM_MIWOVIDEOS_TITLE') ; ?>
                </td>
                <td class="value2">
                    <input type="text" name="title" value="<?php echo $this->item->title; ?>" class="inputbox required" style="font-size: 1.364em; width: 50%;" size="65" aria-required="true" required="required" aria-invalid="false"/>
                </td>
            </tr>

            <tr>
                <td class="key2">
                    <?php echo MText::_('COM_MIWOVIDEOS_ALIAS'); ?>
                </td>
                <td class="value2">
                    <input class="text_area" type="text" name="alias" id="alias" size="45" maxlength="250" value="<?php echo $this->item->alias;?>" />
                </td>
            </tr>
            <tr>
                <td class="key2">
                    <?php echo MText::_( 'COM_MIWOVIDEOS_DESCRIPTION'); ?>
                </td>
                <td class="value2">
                    <?php echo $editor->display('description', $this->item->description , '100%', '250', '90', '6'); ?>
                </td>
            </tr>
        </table>

        <!-- Publishing -->
        <?php echo MHtml::_('tabs.panel', MText::_('COM_MIWOVIDEOS_PUBLISHING_OPTIONS'), 'publishing'); ?>
        <table class="admintable" width="100%">
            <?php if ($this->acl->canEditState()) { ?>
            <tr>
                <td class="key2">
                    <?php echo MText::_('COM_MIWOVIDEOS_PUBLISHED'); ?>
                </td>
                <td class="value2">
                    <?php echo $this->lists['published']; ?>
                </td>
            </tr>
            <?php } else { ?>
                <input type="hidden" name="published" value="<?php echo $this->item->published; ?>" />
            <?php } ?>

            <tr>
                <td class="key2">
                    <span><?php echo MText::_('COM_MIWOVIDEOS_ACCESS'); ?></span>
                </td>
                <td class="value2">
                    <?php echo $this->lists['access']; ?>
                </td>
            </tr>

            <tr>
                <td class="key2">
                    <?php echo MText::_('COM_MIWOVIDEOS_LANGUAGE'); ?>
                </td>
                <td class="value2">
                    <?php echo $this->lists['language']; ?>
                </td>
            </tr>

            <tr>
                <td class="key2">
                    <?php echo MText::_('COM_MIWOVIDEOS_ASSOCIATION'); ?>
                </td>
                <td class="value2">
                    <?php echo $this->lists['association']; ?>
                </td>
            </tr>
        </table>

    <?php echo MHtml::_('tabs.end'); ?>

    <div class="clearfix"></div>

	<input type="hidden" name="option" value="com_miwovideos" />
    <input type="hidden" name="view" value="reasons" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="cid[]" value="<?php echo $this->item->id; ?>" />
	<?php echo MHtml::_( 'form.token' ); ?>

    <?php if (MiwoVideos::isDashboard()) { ?>
    <input type="hidden" name="dashboard" value="1" />
    <input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
    <?php } ?>

	<script type="text/javascript">
		Miwi.submitbutton = function(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				Miwi.submitform(pressbutton);
				return;
			} else {
				//Should have some validations rule here
				//Check something here
				if (form.title.value == '') {
					alert("<?php echo MText::_( 'COM_MIWOVIDEOS_PLEASE_ENTER_TITLE'); ?>");
					form.title.focus();
					return ;
				}
				Miwi.submitform(pressbutton);
			}
		}
	</script>
</form>