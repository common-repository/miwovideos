<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die( 'Restricted access' );

class MiwovideosViewMiwovideos extends MiwovideosView {

	function display($tpl = null) {
        if ($this->_mainframe->isAdmin()) {
            MToolBarHelper::title(MText::_('COM_MIWOVIDEOS_COMMON_PANEL'),'miwovideos');

            if (MFactory::getUser()->authorise('core.admin', 'com_miwovideos')) {
                MToolBarHelper::preferences('com_miwovideos');
                MToolBarHelper::divider();
            }

            $this->toolbar->appendButton('Popup', 'help1', MText::_('Help'), 'http://miwisoft.com/support/docs/wordpress/miwovideos/user-manual/control-panel?tmpl=component', 650, 500);
        }

        $this->info = $this->get('Info');
		$this->stats = $this->get('Stats');
		
		parent::display($tpl);
	}
	
	function quickIconButton($link, $image, $text, $modal = 0, $x = 500, $y = 450, $new_window = false) {
		// Initialise variables
		$lang = MFactory::getLanguage();
		
		$new_window	= ($new_window) ? ' target="_blank"' : '';
  		?>

		<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
			<div class="icon" <?php echo (!$this->acl->canManage()) ? 'style="margin-top:15px;"' : ''; ?>">
				<?php
				if ($modal) {
					MHtml::_('behavior.modal');
					
					if (!strpos($link, 'tmpl=component')) {
						$link .= '&amp;tmpl=component';
					}
				?>
					<a href="<?php echo $link; ?>" style="cursor:pointer" class="modal" rel="{handler: 'iframe', size: {x: <?php echo $x; ?>, y: <?php echo $y; ?>}}"<?php echo $new_window; ?>>
				<?php
				} else {
				?>
					<a href="<?php echo $link; ?>"<?php echo $new_window; ?>>
				<?php
				}
                ?>
                    <img src="<?php echo MURL_MIWOVIDEOS; ?>/admin/assets/images/<?php echo $image; ?>" alt="<?php echo $text; ?>" />
					<span><?php echo $text; ?></span>
				</a>
			</div>
		</div>
		<?php
	}
}