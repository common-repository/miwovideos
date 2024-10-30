<?php
/**
 * @package        MiwoVideos
 * @copyright    Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license        GNU General Public License version 2 or later
 */
# No Permission
defined('_MEXEC') or die;

// Load the tooltip behavior.
MHtml::_('behavior.tooltip');
MHtml::_('behavior.formvalidation');
?>
<script type="text/javascript">
    jQuery(document).ready(function () {
        Miwi.submitbutton = function (task) {
            if (document.formvalidator.isValid(document.id('component-form'))) {
                Miwi.submitform(task, document.getElementById('component-form'));
            }
        }
    });
</script>

<form action="<?php echo MiwoVideos::get('utility')->getActiveUrl(); ?>" id="component-form" method="post" name="adminForm" autocomplete="off" class="form-validate form-horizontal">
    <?php if (MiwoVideos::isDashboard()) { ?>
        <div style="float: left; width: 99%; margin-left: 10px;">
            <button class="button btn-success" onclick="Miwi.submitbutton('apply'); return false;"><span class="icon-apply icon-white"></span> <?php echo MText::_('COM_MIWOVIDEOS_SAVE'); ?></button>
            <button class="button btn-small" onclick="Miwi.submitbutton('save'); return false;"> <?php echo MText::_('COM_MIWOVIDEOS_SAVE_CLOSE'); ?></button>
        </div>
    <?php } ?>

    <div class="tab-content">
        <?php echo MHtml::_('tabs.start', 'miwovideos', array('useCookie'=>1)); ?>

        <?php $fieldSets = $this->form->getFieldsets(); ?>
        <?php foreach ($fieldSets as $name => $fieldSet) { ?>
            <?php
            if ($fieldSet->name == 'permissions') {
                continue;
            }
            ?>
            <!-- Details -->
            <?php echo MHtml::_('tabs.panel', MText::_($fieldSet->label), 'details'); ?>
            <?php foreach ($this->form->getFieldset($name) as $field) { ?>
                <div class="control-group">
                    <?php if (!$field->hidden) { ?>
                        <div class="control-label">
                            <?php echo $field->label; ?>
                        </div>
                    <?php } ?>
                    <div class="controls">
                        <?php echo $field->input; ?>
                    </div>
                </div>
            <?php } ?>

        <?php } ?>

        <?php echo MHtml::_('tabs.end'); ?>
    </div>
    <div>
        <input type="hidden" name="task" value=""/>
        <?php echo MHtml::_('form.token'); ?>
    </div>
</form>
