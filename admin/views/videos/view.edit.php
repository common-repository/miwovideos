<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die ;

class MiwovideosViewVideos extends MiwovideosView {

    public function display($tpl = null) {
        $item = $this->get('EditData');

        if (!$this->acl->canEditOwn($item->user_id)) {
            MFactory::getApplication()->redirect('index.php?option=com_miwovideos', MText::_('JERROR_ALERTNOAUTHOR'));
        }

        $task = MRequest::getCmd('task');
		$text = ($task == 'edit') ? MText::_('COM_MIWOVIDEOS_EDIT') : MText::_('COM_MIWOVIDEOS_NEW');

        if ($this->_mainframe->isAdmin()) {
            MToolBarHelper::title(MText::_('COM_MIWOVIDEOS_CPANEL_VIDEOS').': <small><small>[ ' . $text.' ]</small></small>' , 'miwovideos' );
            MToolBarHelper::apply();
            MToolBarHelper::save();
            MToolBarHelper::save2new();
            MToolBarHelper::cancel();
            MToolBarHelper::divider();
            $this->toolbar->appendButton('Popup', 'help1', MText::_('Help'), 'http://miwisoft.com/support/docs/wordpress/miwovideos/user-manual/videos?tmpl=component', 650, 500);
        }

        $categories = $this->get('Categories');
        $null_date 	= MFactory::getDbo()->getNullDate();
        $params 	= new MRegistry($item->params);

		//Get list of location
		$children = array();
		if ($categories) {
			foreach ($categories as $v) {
				$pt = $v->parent;
				$list = @$children[$pt] ? $children[$pt] : array();
				array_push( $list, $v );
				$children[$pt] = $list;
			}
		}
		$list = MHtml::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0);
		$options = array();
		foreach ($list as $listItem) {
			$options[] = MHtml::_('select.option', $listItem->id, '&nbsp;&nbsp;&nbsp;'. $listItem->treename);
		}

		$itemCategories = array() ;
		if ($item->id) {
			$cats = $this->get('VideoCategories');

            $n = count($cats);
			for ($i = 0; $i < $n; $i++) {
				$itemCategories[] = MHtml::_('select.option', $cats[$i], $cats[$i]);
			}	
		}
		$lists['video_categories'] = MHtml::_('select.genericlist', $options, 'video_categories[]', array(
				'option.text.toHtml'=> false ,
				'option.text' 		=> 'text',
				'option.value' 		=> 'value',
				'list.attr' 		=> 'class="inputbox" size="5" multiple="multiple" aria-invalid="false"',
				'list.select' 		=> $itemCategories
		));

        if (MiwoVideos::is31()) {
            MHtml::_('tag.ajaxfield', '#tags', true);

            $item_tags = MiwoVideos::get('videos')->getTags($item->id, false, true);

            $lists['tags'] = MHtml::_('select.genericlist', $item_tags, 'tags[]', array(
                    'option.text.toHtml'=> false ,
                    'option.text'   => 'text',
                    'option.value'   => 'value',
                    'list.attr'   => 'class="inputbox" size="5" multiple="multiple"',
                    'list.select'   => $item_tags
            ));
        }

        
        $lists['published'] = MiwoVideos::get('utility')->getRadioList('published', $item->published);
        $lists['featured'] = MiwoVideos::get('utility')->getRadioList('featured', $item->featured);
        $lists['language']  = MHtml::_('select.genericlist', MHtml::_('contentlanguage.existing', true, true), 'language', ' class="inputbox" ', 'value', 'text', $item->language);

        if($this->getModel('processes')->getProcessing($item->id) > 0) {
            MError::raiseNotice('100', MText::_('COM_MIWOVIDEOS_STILL_PROCESSING'));
        }

        MHtml::_('behavior.tooltip');
        MHtml::_('behavior.modal');

        $this->item            = $item;
        $this->lists           = $lists;
        $this->files           = $this->get('Files');
        $this->null_date       = $null_date;
        $this->fields          = MiwoVideos::get('fields')->getVideoFields($item->id);
        $this->availableFields = MiwoVideos::get('fields')->getAvailableFields();
				
		parent::display($tpl);				
	}

	public function displayPreview() {
		$item = $this->get('EditData');
		echo MiwoVideos::get('videos')->getPlayer($item);
	}
}