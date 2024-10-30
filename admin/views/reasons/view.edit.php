<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die ;

class MiwovideosViewReasons extends MiwovideosView {

    public function display($tpl = null) {
        if (!$this->acl->canEdit()) {
            MFactory::getApplication()->redirect('index.php?option=com_miwovideos', MText::_('JERROR_ALERTNOAUTHOR'));
        }

        $task = MRequest::getCmd('task');
		$text = ($task == 'edit') ? MText::_('COM_MIWOVIDEOS_EDIT') : MText::_('COM_MIWOVIDEOS_NEW');

        if ($this->_mainframe->isAdmin()) {
            MToolBarHelper::title(MText::_('COM_MIWOVIDEOS_CPANEL_REASONS').': <small><small>[ ' . $text.' ]</small></small>' , 'miwovideos' );
            MToolBarHelper::apply();
            MToolBarHelper::save();
            MToolBarHelper::save2new();
            MToolBarHelper::cancel();
            MToolBarHelper::divider();
            $this->toolbar->appendButton('Popup', 'help1', MText::_('Help'), 'http://miwisoft.com/support/docs/wordpress/miwovideos/user-manual/reasons?tmpl=component', 650, 500);
        }

        $item = $this->get('EditData');
		
		$associations = $this->get('Association');
        $options = array();
        $options[] = MHtml::_('select.option', '', MText::_('COM_MIWOVIDEOS_SELECT_ASSOCIATION'));
        foreach($associations as $association) {
            $options[] = MHtml::_('select.option',  $association->id, $association->title);
        }

        $lists['association'] = MHtml::_('select.genericlist', $options, 'association', array(
                                                                                            'option.text.toHtml' => false,
                                                                                            'option.text' => 'text',
                                                                                            'option.value' => 'value',
                                                                                            'list.attr' => ' class="inputbox" style="width: 220px;" ',
                                                                                            'list.select' => $item->association));

        
        $lists['published'] = MiwoVideos::get('utility')->getRadioList('published', $item->published);
        $lists['language']  = MHtml::_('select.genericlist', MHtml::_('contentlanguage.existing', true, true), 'language', ' class="inputbox" ', 'value', 'text', $item->language);

        MHtml::_('behavior.tooltip');
        MHtml::_('behavior.modal');
						
		$this->item		    = $item;
		$this->lists	    = $lists;
				
		parent::display($tpl);				
	}
}