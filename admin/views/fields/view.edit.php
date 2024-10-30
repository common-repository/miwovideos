<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die ;

class MiwovideosViewFields extends MiwovideosView {

    public function display($tpl = null) {
        if (!$this->acl->canEdit()) {
            MFactory::getApplication()->redirect('index.php?option=com_miwovideos', MText::_('JERROR_ALERTNOAUTHOR'));
        }

        $db	= MFactory::getDBO();

        $item = $this->get('EditData');

		$task = MiwoVideos::getInput()->getCmd('task', '');

		$text = ($task == 'edit') ? MText::_('COM_MIWOVIDEOS_EDIT') : MText::_('COM_MIWOVIDEOS_NEW');

        if ($this->_mainframe->isAdmin()) {
            MToolBarHelper::title(MText::_('Field').': <small><small>[ ' . $text.' ]</small></small>' , 'miwovideos' );
            MToolBarHelper::apply();
            MToolBarHelper::save();
            MToolBarHelper::save2new();
            MToolBarHelper::cancel();
            MToolBarHelper::divider();
            $this->toolbar->appendButton('Popup', 'help1', MText::_('Help'), 'http://miwisoft.com/support/docs/wordpress/miwovideos/user-manual/fields?tmpl=component', 650, 500);
        }

		$options = array();
		$options[] = MHtml::_('select.option', '', 						MText::_('COM_MIWOVIDEOS_SEL_FIELD_TYPE'));
		$options[] = MHtml::_('select.option', 'text', 					MText::_('COM_MIWOVIDEOS_FIELDS_TEXT'));
		$options[] = MHtml::_('select.option', 'textarea', 				MText::_('COM_MIWOVIDEOS_FIELDS_TEXTAREA'));
        $options[] = MHtml::_('select.option', 'radio',	 				MText::_('COM_MIWOVIDEOS_FIELDS_RADIO'));
		$options[] = MHtml::_('select.option', 'list', 					MText::_('COM_MIWOVIDEOS_FIELDS_LIST'));
		$options[] = MHtml::_('select.option', 'multilist', 			MText::_('COM_MIWOVIDEOS_FIELDS_MULTILIST'));
		$options[] = MHtml::_('select.option', 'checkbox', 				MText::_('COM_MIWOVIDEOS_FIELDS_CHECKBOX'));
		$options[] = MHtml::_('select.option', 'calendar', 				MText::_('COM_MIWOVIDEOS_FIELDS_CALENDAR'));
        $options[] = MHtml::_('select.option', 'miwovideoscountries',	MText::_('COM_MIWOVIDEOS_FIELDS_MIWOVIDEOSCOUNTRIES'));
		$options[] = MHtml::_('select.option', 'email', 				MText::_('COM_MIWOVIDEOS_FIELDS_EMAIL'));
		$options[] = MHtml::_('select.option', 'language', 				MText::_('COM_MIWOVIDEOS_FIELDS_LANGUAGE'));
		$options[] = MHtml::_('select.option', 'timezone', 				MText::_('COM_MIWOVIDEOS_FIELDS_TIMEZONE'));
		$lists['field_type'] = MHtml::_('select.genericlist', $options, 'field_type',' class="inputbox" ', 'value', 'text', $item->field_type);

		if ($this->config->get('cb_integration')) {
			if ($this->config->get('cb_integration') == 1) {
				$sql = 'SELECT name AS `value`, name AS `text` FROM #__comprofiler_fields WHERE `table` = "#__comprofiler"';
			}
            elseif ($this->config->get('cb_integration') == 2) {
				$sql = 'SELECT fieldcode AS `value`, fieldcode AS `text` FROM #__community_fields WHERE published = 1 AND fieldcode != ""' ;
			}

			$db->setQuery($sql);
			$options = array();

			$options[] = MHtml::_('select.option', '', MText::_('COM_MIWOVIDEOS_SEL_FIELD'));
			$options = array_merge($options, $db->loadObjectList());

			$lists['field_mapping'] = MHtml::_('select.genericlist', $options, 'field_mapping', ' class="inputbox" ', 'value', 'text', $item->field_mapping);
		}
					
        $lists['published'] = MiwoVideos::get('utility')->getRadioList('published', $item->published);
		$lists['language'] = MHtml::_('select.genericlist', MHtml::_('contentlanguage.existing', true, true), 'language', ' class="inputbox" ', 'value', 'text', $item->language);

        MHtml::_('behavior.tooltip');

		$this->item		= $item;
		$this->lists	= $lists;

		parent::display($tpl);				
	}
}