<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die ;

$editor = MFactory::getEditor();
$utility = MiwoVideos::get('utility');
?>
<script type="text/javascript">
	Miwi.submitbutton = function(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			Miwi.submitform( pressbutton );
			return;				
		} else {
			<?php echo $editor->save('introtext'); ?>
			Miwi.submitform( pressbutton );
		}
	}
</script>

<form action="<?php echo MRoute::getActiveUrl(); ?>" method="post" name="adminForm" enctype="multipart/form-data" id="adminForm">
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
        <table class="admintable">
            <tr>
                <td class="key2">
                    <?php echo MText::_('COM_MIWOVIDEOS_TITLE'); ?>
                </td>
                <td class="value2">
                    <input class="text_area inputbox required" type="text" name="title" id="title" style="font-size: 1.364em; width: 50%;" size="65" maxlength="250" value="<?php echo $this->item->title;?>" />
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
                    <?php echo MText::_('COM_MIWOVIDEOS_PARENT'); ?>
                </td>
                <td class="value2">
                    <?php echo $this->lists['parent']; ?>
                </td>
            </tr>
			<tr>
                <td class="key2"><?php echo MText::_('COM_MIWOVIDEOS_PICTURE'); ?></td>
                <td class="value2">
                    <input type="file" class="inputbox" name="thumb_image" size="32" />
                    <?php if ($this->item->thumb) { ?>
                        <a href="<?php echo $utility->getThumbPath($this->item->id, 'categories', $this->item->thumb); ?>" class="modal"><img src="<?php echo $utility->getThumbPath($this->item->id, 'categories', $this->item->thumb); ?>" class="img_preview" /></a>
                        <input type="checkbox" name="del_thumb" value="1" /><?php echo MText::_('COM_MIWOVIDEOS_DELETE_CURRENT'); ?>
                    <?php } ?>
                </td>
            </tr>
            
            <tr>
                <td class="key2">
                    <?php echo MText::_( 'COM_MIWOVIDEOS_DESCRIPTION'); ?>
                </td>
                <td class="value2">
                	<?php 
		            # Description
		            $pageBreak = "<hr id=\"system-readmore\">";
		            
		            $fulltextLen = strlen($this->item->fulltext);
		            
		            if ($fulltextLen > 0){
		            	$content = "{$this->item->introtext}{$pageBreak}{$this->item->fulltext}";
		            } else {
		            	$content = "{$this->item->introtext}";
		            }
		            
                    echo $editor->display( 'introtext',  $content , '100%', '250', '75', '10') ; ?>
                </td>
            </tr>
        </table>

        <!-- Publishing Options -->
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
            <?php } ?>            <tr>
                <td class="key2">
                    <?php echo MText::_('COM_MIWOVIDEOS_LANGUAGE'); ?>
                </td>
                <td class="value2">
                    <?php echo $this->lists['language'] ; ?>
                </td>
            </tr>
        </table>

        <!-- Meta Settings -->
        <?php echo MHtml::_('tabs.panel', MText::_('COM_MIWOVIDEOS_META_OPTIONS'), 'publishing'); ?>
        <table class="admintable" width="100%">
            <tr>
                <td class="key2">
                    <?php echo MText::_('COM_MIWOVIDEOS_META_DESC'); ?>
                </td>
                <td class="value2">
                    <textarea name="meta_desc" id="meta_desc" cols="40" rows="3" class="" aria-invalid="false"><?php echo $this->item->meta_desc;?></textarea>
                </td>
            </tr>
            <tr>
                <td class="key2">
                    <?php echo MText::_('COM_MIWOVIDEOS_META_KEYWORDS'); ?>
                </td>
                <td class="value2">
                    <textarea name="meta_key" id="meta_key" cols="40" rows="3" class="" aria-invalid="false"><?php echo $this->item->meta_key;?></textarea>
                </td>
            </tr>
            <tr>
                <td class="key2">
                    <?php echo MText::_('COM_MIWOVIDEOS_META_AUTHOR'); ?>
                </td>
                <td class="value2">
                    <input class="text_area" type="text" name="meta_author" id="meta_author" size="40" maxlength="250" value="<?php echo $this->item->meta_author;?>" />
                </td>
            </tr>
        </table>

    <?php echo MHtml::_('tabs.end'); ?>

    <div class="clearfix"></div>

	<input type="hidden" name="option" value="com_miwovideos" />
	<input type="hidden" name="view" value="categories" />
    <input type="hidden" name="task" value="" />
	<input type="hidden" name="cid[]" value="<?php echo $this->item->id; ?>" />

    <?php if (MiwoVideos::isDashboard()) { ?>
    <input type="hidden" name="dashboard" value="1" />
    <input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
    <?php } ?>

    <?php echo MHtml::_('form.token'); ?>
</form>