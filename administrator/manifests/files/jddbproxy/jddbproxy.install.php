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

class jddbproxyInstallerScript {
	/**
	 * Constructor
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 */
	public function __construct(JAdapterInstance $adapter) {

	}
 
	/**
	 * Called before any type of action
	 *
	 * @param   string  $route  Which action is happening (install|uninstall|discover_install)
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function preflight($route, JAdapterInstance $adapter) {
		switch ($route) {
			case 'uninstall':
				echo '<h2>'.JText::_("COM_JDICTION_UNINSTALLED").'</h2>';
				echo '<p>'.JText::_("COM_JDICTION_UNINSTALLED_INFO").'</h2>';

        // remove jdiction database driver from configuration.php
        $fname = JPATH_CONFIGURATION.'/configuration.php';
        $config = JFactory::getConfig();
        $config->set('dbtype', str_replace('jdiction_', '',$config->get('dbtype')));
        jimport('joomla.filesystem.file');
        JFile::write($fname, $config->toString('PHP', array('class' => 'JConfig')));

				break;
		}
	}
 
	/**
	 * Called after any type of action
	 *
	 * @param   string  $route  Which action is happening (install|uninstall|discover_install)
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function postflight($route, JAdapterInstance $adapter) {
		switch ($route) {
			case 'install':
				echo '<h2>'.JText::_("COM_JDICTION_INSTALLED").'</h2>';
				echo '<p>'.JText::_("COM_JDICTION_INSTALLED_INFO").'</h2>';
				break;
			case 'uninstall':
				//can't becalled
				break;
			case 'discover_install':
				echo '<h2>'.JText::_("COM_JDICTION_UNINSTALLED").'</h2>';
				echo '<p>'.JText::_("COM_JDICTION_UNINSTALLED_INFO").'</h2>';
				break;
			case 'update':
				echo '<h2>'.JText::_("COM_JDICTION_UPDATED").'</h2>';
				echo '<p>'.JText::_("COM_JDICTION_UPDATED_INFO").'</h2>';
				break;
		}
	}
 
	/**
	 * Called on uninstallation
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 */
	public function uninstall(JAdapterInstance $adapter) {
		//Fallback because Joomla 2.5.4 doesn't call pre or postfilt on uninstall for type file
		$this->preflight('uninstall', $adapter);
	}
}