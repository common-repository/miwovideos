<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die('Restricted access');

mimport('framework.form.formfield');

class MFormFieldMiwovideosPlaylists extends MFormField {

	protected $type = 'MiwovideosPlaylists';
	
	function getInput() {
		$db = MFactory::getDBO();
		$db->setQuery("SELECT id, title FROM #__miwovideos_playlists WHERE published = 1 AND type = 0 ORDER BY title");
		$rows = $db->loadObjectList();
		
		$options = array();
		$options[] = MHtml::_('select.option', '0', MText::_('Select Playlist'), 'id', 'title');
		$options = array_merge($options, $rows);
		
		return MHtml::_('select.genericlist', $options, $this->name, ' class="inputbox" ', 'id', 'title', $this->value);
	}
}
