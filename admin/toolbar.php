<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die('Restricted access');

$view = MRequest::getCmd('view');

MHtml::_('behavior.switcher');

// Load submenus
$views = array( ''							    => MText::_('COM_MIWOVIDEOS_COMMON_PANEL'),
				'&view=fields'				    => MText::_('COM_MIWOVIDEOS_CPANEL_FIELDS'),
				'&view=categories'			    => MText::_('COM_MIWOVIDEOS_CPANEL_CATEGORIES'),
                '&view=channels'			    => MText::_('COM_MIWOVIDEOS_CPANEL_CHANNELS'),
				'&view=playlists'				=> MText::_('COM_MIWOVIDEOS_CPANEL_PLAYLISTS'),
				'&view=videos'			    	=> MText::_('COM_MIWOVIDEOS_CPANEL_VIDEOS'),
                '&view=subscriptions'			=> MText::_('COM_MIWOVIDEOS_CPANEL_SUBSCRIPTIONS'),
				'&view=reports'			    	=> MText::_('COM_MIWOVIDEOS_CPANEL_REPORTS'),
				'&view=files'			    	=> MText::_('COM_MIWOVIDEOS_CPANEL_FILES'),
				'&view=processes'			   	=> MText::_('COM_MIWOVIDEOS_CPANEL_PROCESSES'),
				'&view=upgrade'				    => MText::_('COM_MIWOVIDEOS_CPANEL_UPGRADE'),
				'&view=support&task=support'	=> MText::_('COM_MIWOVIDEOS_CPANEL_SUPPORT'),
				);

if (!class_exists('JSubMenuHelper')) {
    return;
}

foreach($views as $key => $val) {
	if ($key == '') {
		$active	= ($view == $key);
		
		$img = 'icon-16-miwovideos.png';
	}
	else {
	    $a = explode('&', $key);
	  	$c = explode('=', $a[1]);
	
		$active	= ($view == $c[1]);
	
		$img = 'icon-16-miwovideos-'.$c[1].'.png';
	}
	
	JSubMenuHelper::addEntry('<img src="<?php echo MURL_MIWOVIDEOS; ?>/site/assets/images/'.$img.'" style="margin-right: 2px;" align="absmiddle" />&nbsp;'.$val, 'index.php?option=com_miwovideos'.$key, $active);
}