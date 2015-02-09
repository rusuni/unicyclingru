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

/**
 * View class for a list of shops.
 *
 * @package		jDiction
 */
class jDictionViewTranslations extends JViewLegacy {
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null) {

		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
    $this->jd = jDiction::getInstance();

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
		require_once JPATH_COMPONENT.'/helpers/translations.php';

		$state	= $this->get('State');
		$canDo	= TranslationsHelper::getActions();

    $doc = JFactory::getDocument();
    $doc->addStyleSheet('/administrator/components/com_jdiction/assets/style.css');

    // Set toolbar items for the page
    JToolBarHelper::title(JText::_('COM_JDICTION_MANAGER_TRANSLATIONS'), 'jdiction-translations');

    /* deactivate this till implemented
		if ($canDo->get('core.create')) {
			JToolBarHelper::addNew('translation.add','JTOOLBAR_NEW');
		}
    */
		if ($canDo->get('core.edit')) {
			JToolBarHelper::editList('translation.edit','JTOOLBAR_EDIT');
		}
		if ($canDo->get('core.edit.state')) {

			//JToolBarHelper::divider();
			//JToolBarHelper::custom('translations.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
			//JToolBarHelper::custom('translations.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);

			if ($state->get('filter.state') != -1 ) {
				JToolBarHelper::divider();
			}
		}
		if(JFactory::getUser()->authorise('core.manage','com_checkin')) {
			//JToolBarHelper::custom('translations.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
		}
		if ($canDo->get('core.delete')) {
			JToolBarHelper::deleteList('', 'translations.delete','JTOOLBAR_DELETE');
		}
		if ($canDo->get('core.admin')) {
			JToolBarHelper::divider();
			JToolBarHelper::preferences('com_jdiction');
		}
	}
}
