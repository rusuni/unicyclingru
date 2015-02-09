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

/**
 * jDiction component helper.
 *
 * @package jDiction
 */
class jDictionHelper {

	public static $extension = 'com_jdiction';

	/**
	 * Configure the Linkbar.
	 *
	 * @param	string	$vName	The name of the active view.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public static function addSubmenu($vName) {
		JSubMenuHelper::addEntry(
			JText::_('COM_JDICTION_SUBMENU_TRANSLATIONS'),
			'index.php?option=com_jdiction&view=translations',
			($vName == 'translations')
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_JDICTION_SUBMENU_TOOLS'),
			'index.php?option=com_jdiction&view=tools',
			($vName == 'tools')
		);
    JSubMenuHelper::addEntry(
      JText::_('COM_JDICTION_SUBMENU_CHECK'),
      'index.php?option=com_jdiction&view=check',
      ($vName == 'check')
    );
  }
}
