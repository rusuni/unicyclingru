<?php
/**
 * @package jDiction
 * @link http://joomla.itronic.at
 * @copyright	Copyright (C) 2011 ITronic Harald Leithner. All rights reserved.
 * @license GNU General Public License v3
 *
 * This file is part of jDiction.
 *
 * jDiction is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3 of the License.
 *
 * jDiction is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with jDiction.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.view');
require_once JPATH_COMPONENT.'/helpers/translations.php'; 

/**
 * View to edit a Translation.
 *
 * @package		jDiction
 */
class jDictionViewTranslation extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null) {

    // Initialiase variables.
    $input = JFactory::getApplication()->input;
		$model = $this->getModel();
		
		$component = $input->get('jd_option', false, 'cmd');
		if ($component) {
			$this->component = new stdClass;
			$this->component->option = $component;
			$this->component->view 	 = $input->get('jd_view', 0, 'cmd');
			$this->component->layout = $input->get('jd_layout', 0, 'cmd');
			$this->component->id 	   = $input->get('jd_id', 0, 'int');
			$reference = $model->loadForm($this->component);
		} else {
			$id = $input->get('idJdStore', 0, 'int');
			$this->component = $model->getItem($id);
		}
		$this->original = $model->getOriginal($this->component->id);
		
		$this->languages = $this->get('Languages');
		$this->form = $this->component->form;
    $this->fields = $model->fields;

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
    // @deprecated used for Joomla 2.5
    $tpl = (version_compare(JVERSION, '3.0', 'ge') ? $tpl : '25');
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
    JFactory::getApplication()->input->set('hidemainmenu', true);

		$user		= JFactory::getUser();
    if (isset($this->item)) {
      $isNew		= ($this->item->idJdStore == 0);
      $checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
    } else {
      $isNew = true;
      $checkedOut = false;
    }
		$canDo		= TranslationsHelper::getActions();

		JToolBarHelper::title($isNew ? JText::_('COM_JDICTION_TRANSLATION_NEW') : JText::_('COM_JDICTION_TRANSLATION_EDIT'), 'banners.png');

		// If not checked out, can save the item.
		if (!$checkedOut && $canDo->get('core.edit')) {
			JToolBarHelper::apply('translation.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('translation.save', 'JTOOLBAR_SAVE');
		}
		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create')) {
			JToolBarHelper::custom('translation.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
		}
		if (empty($this->item->__ITRL_TABLEPK__))  {
			JToolBarHelper::cancel('translation.cancel','JTOOLBAR_CANCEL');
		} else {
			JToolBarHelper::cancel('translation.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
