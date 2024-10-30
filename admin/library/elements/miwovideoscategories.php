<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die('Restricted access');

mimport('framework.form.formfield');

class MFormFieldMiwovideosCategories extends MFormField {

	protected $type = 'MiwovideosCategories';
	
	function getInput() {    		
		$db = MFactory::getDBO();			
		$db->setQuery("SELECT id, parent, parent AS parent_id, title FROM #__miwovideos_categories WHERE published = 1");
		$rows = $db->loadObjectList();
		
		$children = array();
		if ($rows) {
			// first pass - collect children
			foreach ($rows as $v) {
				$pt 	= $v->parent;
				$list 	= @$children[$pt] ? $children[$pt] : array();
				array_push($list, $v);
				$children[$pt] = $list;
			}
		}
		
		$list = MHtml::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0);
		
		$options = array();
		$options[] = MHtml::_('select.option', '0', MText::_('Top'));
		foreach ($list as $item) {
			$options[] = MHtml::_('select.option', $item->id, '&nbsp;&nbsp;&nbsp;'. $item->treename);
		}
		
		return MHtml::_('select.genericlist', $options, $this->name, array(
			'option.text.toHtml' => false ,
			'option.value' => 'value', 
			'option.text' => 'text', 
			'list.attr' => ' class="inputbox" ',
			'list.select' => $this->value    		        		
		));					    		
	}
}
