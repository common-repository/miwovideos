<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die('Restricted access');
?>

<script language="javascript" type="text/javascript">
	function upgrade() {	    
	    document.adminForm.view.value = 'upgrade';
		document.adminForm.submit();
	}
</script>

<form name="adminForm" id="adminForm" action="<?php echo MRoute::getActiveUrl(); ?>" method="post">
	<table cellspacing="0" cellpadding="0" border="0" width="100%">
		<tr>
							</tr>
		<tr>
			<th>
				<?php
                $jusersync = $this->config->get('jusersync');
					if(empty($jusersync)){
						MError::raiseWarning('100', MText::sprintf('COM_MIWOVIDEOS_ACCOUNT_SYNC_WARN', '<a href="#" onclick="javascript : submitform(\'jusersync\')">', '</a>'));
					}
				?>
			</th>
		</tr>
		<tr>
            <?php
            $layout = 'user';
            if ($this->acl->canManage()) {
                $layout = 'admin';
            }

            echo $this->loadTemplate($layout);
            ?>
		</tr>
	</table>
	
	<input type="hidden" name="option" value="com_miwovideos" />
	<input type="hidden" name="view" value="miwovideos"/>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo MHtml::_('form.token'); ?>
</form>