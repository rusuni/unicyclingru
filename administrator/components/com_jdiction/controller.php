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


jimport('joomla.application.component.controller');

/**
 * Component Controller
 *
 * @package jDiction
 */
class jDictionController extends JControllerLegacy {
	/**
	 * @var		string	The default view.
	 * @since	1.6
	 */
	protected $default_view = 'translations';

	/**
	 * Method to display a view.
	 *
	 * @param	boolean			$cachable If true, the view output will be cached
	 * @param	array			$urlparams An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object to support chaining.
	 * @since	1.5
	 */
	public function display($cachable = false, $urlparams = false) {
		
		require_once JPATH_COMPONENT.'/helpers/translation.php';

		// Load the submenu.
		jDictionHelper::addSubmenu(JFactory::getApplication()->input->get('view', 'translations', 'word'));

    $input = JFactory::getApplication()->input;

		$view		= $input->get('view', 'translations', 'word');
		$layout = $input->get('layout', 'default', 'word');
		$id			= $input->get('id', null, 'int');

		// Check for edit form.
		if ($view == 'translation' && $layout == 'edit' && !$this->checkEditId('com_jdiction.edit.translation', $id)) {
			// Somehow the person just went to the form - we don't allow that.
			$this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_jdiction&view=translations', false));

			return false;
		}

		parent::display();

		return $this;
	}
} 
