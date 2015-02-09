<?php
/**
 * @package jDiction
 * @link http://joomla.itronic.at
 * @copyright  Copyright (C) 2011 ITronic Harald Leithner. All rights reserved.
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

jimport('joomla.application.component.modeladmin');

/**
 * Methods supporting a list of Translation records.
 *
 * @package    jDiction
 */
class jDictionModelCheck extends JModelLegacy {
	/**
	 * @var    string  The prefix to use with controller messages.
	 */
	protected $text_prefix = 'COM_JDICTION_CHECK';


	/**
	 * check the Installations for possible problems.
	 */
	public function getStatus() {
		$result                                        = array();
		$result[$this->text_prefix . '_TEST_VERSION']  = $this->checkVersion();
		$result[$this->text_prefix . '_TEST_PLUGIN']   = $this->checkPlugin();
		$result[$this->text_prefix . '_TEST_DATABASE'] = $this->checkDatabase();
		$result[$this->text_prefix . '_TEST_LANGUAGE'] = $this->checkLanguage();
		$result[$this->text_prefix . '_TEST_MODULE']   = $this->checkModule();
		$result[$this->text_prefix . '_TEST_COMMON']   = $this->checkCommon();

		return $result;
	}

	public function checkVersion() {

		$result = array();
		$dom    = new DOMDocument();

		if (!$dom->loadXML(file_get_contents(JPATH_MANIFESTS . '/libraries/lib_jdiction.xml'))) {
			$result[] = $this->text(2, "Could not load lib_jdiction.xml File");
			return $result;
		}
		$xpath = new DomXPath($dom);
		$item  = $xpath->query('//version');
		if ($item->length > 0) {
			$result[] = $this->text(-1, 'Library Version: ' . (string)$item->item(0)->textContent);
		}

		switch (substr(JVERSION, 0, 3)) {
			case '2.5':
				if (version_compare(JVERSION, '2.5.6', 'lt')) {
					$result[] = $this->text(2, 'Joomla Version ' . JVERSION . ' is to old.');
				} else if (version_compare(JVERSION, '2.5.20', 'le')) {
					$result[] = $this->text(0, 'Joomla Version ' . JVERSION . ' is ok.');
				} else {
					$result[] = $this->text(1, 'Joomla Version ' . JVERSION . ' is unkown.');
				}
				break;
			case '3.0':
				$result[] = $this->text(2, 'Joomla Version ' . JVERSION . ' is to old.');
				break;
			case '3.1':
				$result[] = $this->text(2, 'Joomla Version ' . JVERSION . ' is untested.');
				break;
			case '3.2':
				if (version_compare(JVERSION, '3.2.4', 'lt')) {
					$result[] = $this->text(0, 'Joomla Version ' . JVERSION . ' is ok.');
				} else {
					$result[] = $this->text(2, 'Joomla Version ' . JVERSION . ' is untested.');
				}
				break;
			case '3.3':
				if (version_compare(JVERSION, '3.3.0', 'le')) {
					$result[] = $this->text(0, 'Joomla Version ' . JVERSION . ' is ok.');
				} else {
					$result[] = $this->text(1, 'Joomla Version ' . JVERSION . ' is untested.');
				}
				break;
			default:
				$result[] = $this->text(2, 'Joomla Version ' . JVERSION . ' is unkown.');
		}
		return $result;
	}

	public function checkPlugin() {
		$result = array();
		$db     = JFactory::getDbo();
		$query  = $db->getQuery(true);
		$query->select('*');
		$query->from('#__extensions');

		$query->where($query->quoteName('type') . '=' . $query->quote('plugin'));
		$query->where($query->quoteName('folder') . '=' . $query->quote('system'));
		$query->where($query->quoteName('element') . '=' . $query->quote('jdiction'));

		$db->setQuery($query);
		$plugin = $db->loadObject();
		if (!$plugin) {
			$result[] = $this->text(2, 'JDiction Plugin not installed');
			return $result;
		}

		if ($plugin->enabled < 1) {
			$result[] = $this->text(2, 'JDiction Plugin is not enabled');
		} else {
			$result[] = $this->text(0, 'JDiction Plugin is enabled');
		}

		if ($plugin->ordering > 1) {
			$result[] = $this->text(1, "JDiction Plugin is not the first loaded Plugin");
		}

		if ($plugin->ordering == 0) {
			$result[] = $this->text(1, "JDiction Plugin is loaded at position 0 should be 1");
		}

		$query->Clear();
		$query->select('*');
		$query->from('#__extensions');

		$query->where($query->quoteName('type') . '=' . $query->quote('plugin'));
		$query->where($query->quoteName('folder') . '=' . $query->quote('system'));
		$query->where($query->quoteName('element') . '=' . $query->quote('languagefilter'));

		$db->setQuery($query);
		$plugin = $db->loadObject();
		if (!$plugin) {
			$result[] = $this->text(2, 'Languagefilter Plugin not installed');
		}

		if ($plugin->enabled < 1) {
			$result[] = $this->text(2, 'Languagefilter is not enabled');
		} else {
			$result[] = $this->text(0, 'Languagefilter is enabled');
		}

		$query->clear();
		$query->select('extension_id');
		$query->from('#__extensions');

		$query->where($query->quoteName('type') . '=' . $query->quote('plugin'));
		$query->where($query->quoteName('folder') . '=' . $query->quote('system'));
		$query->where($query->quoteName('ordering') . '=0');

		$db->setQuery($query);
		$list = $db->loadRowList();
		if ($list && count($list) > 1) {
			$result[] = $this->text(1, "Multiple Plugins have the same loading position (0)");
		}

		return $result;

	}

	public function checkDatabase() {
		$result = Array();

		$db = JFactory::getDbo();

		if (!is_a($db, 'JdDatabase')) {
			$result[] = $this->text(2, "jDiction Database Driver is not selected");
			return $result;
		}

		$result[] = $this->text(0, "jDiction Database Driver is selected");
		return $result;
	}

	public function checkLanguage() {
		$result = array();
		$db     = JFactory::getDbo();
		$query  = $db->getQuery(true);
		$query->select('extension_id');
		$query->from('#__extensions');

		$query->where($query->quoteName('type') . '=' . $query->quote('language'));
		$query->where($query->quoteName('client_id') . '=' . $query->quote('1'));
		$query->where($query->quoteName('enabled') . '>' . $query->quote('0'));

		$db->setQuery($query);
		$rows = $db->loadRowList();
		if (count($rows) <= 1) {
			$result[] = $this->text(2, 'Less then 2 Administrator languages are installed and enabled.');
		}

		$query->clear();
		$query->select('extension_id');
		$query->from('#__extensions');
		$query->where($query->quoteName('type') . '=' . $query->quote('language'));
		$query->where($query->quoteName('client_id') . '=' . $query->quote('0'));
		$query->where($query->quoteName('enabled') . '>' . $query->quote('0'));

		$db->setQuery($query);
		$rows = $db->loadRowList();
		if (count($rows) <= 1) {
			$result[] = $this->text(2, 'Less then 2 Frontend translations are installed and enabled.');
		}
		$frontendlanguages = count($rows);

		$query->clear();
		$query->select('lang_id');
		$query->from('#__languages');
		$query->where($query->quoteName('published') . '=' . $query->quote('1'));

		$db->setQuery($query);
		$rows = $db->loadRowList();
		if (count($rows) != $frontendlanguages) {
			$result[] = $this->text(1, 'Translations are not equal to frontend translations.');
		}

		/** Check Accesslevel */
		$query->clear();
		$query->select('lang_id');
		$query->from('#__languages');
		$query->where($query->quoteName('access') . '=' . $query->quote('0'));

		$db->setQuery($query);
		$rows  = $db->loadRowList();
		$count = count($rows);
		if ($count == 1) {
			$result[] = $this->text(2, 'One language has no accesslevel set.');
		} elseif ($count > 1) {
			$result[] = $this->text(2, $count . ' languages having no accesslevel.');
		}

		$query->clear();
		$query->select('lang_id');
		$query->from('#__languages');
		$query->where($query->quoteName('access') . ' != 1');

		$db->setQuery($query);
		$rows  = $db->loadRowList();
		$count = count($rows);
		if ($count == 1) {
			$result[] = $this->text(2, 'One language has not accesslevel public.');
		} elseif ($count > 1) {
			$result[] = $this->text(2, $count . ' languages having not accesslevel public.');
		}

		if (count($result) == 0) {
			$result[] = $this->text(0, 'Seam to be good');
		}

		return $result;
	}

	public function checkModule() {
		$result = array();
		$db     = JFactory::getDbo();
		$query  = $db->getQuery(true);
		$query->select('*');
		$query->from('#__extensions');

		$query->where($query->quoteName('type') . '=' . $query->quote('module'));
		$query->where($query->quoteName('element') . '=' . $query->quote('mod_jdlanguage'));

		$db->setQuery($query);
		$plugin = $db->loadObject();
		if (!$plugin) {
			$result[] = $this->text(2, 'Module not installed');
			return $result;
		}

		if ($plugin->enabled < 1) {
			$result[] = $this->text(2, 'Module is not enabled');
		} else {
			$result[] = $this->text(0, 'Module is enabled');
		}

		return $result;

	}

	public function checkCommon() {
		$result = array();
		$db     = JFactory::getDbo();
		$query  = $db->getQuery(true);

		// Check menuitems
		$query->select('id');
		$query->from('#__menu');
		$query->where('language != ' . $db->quote('*'));
		$query->where('published > 0');
		$query->where('client_id = 0');
		$db->setQuery($query);

		$rows  = $db->loadRowList();
		$count = count($rows);
		if ($count > 0) {
			$result[] = $this->text(0, 'At least one menu item has the language set not to ALL.');
		}

		// Check articles
		$query->clear();
		$query->select('id');
		$query->from('#__content');
		$query->where('language != ' . $db->quote('*'));
		$query->where('state > 0');
		$db->setQuery($query);

		$rows  = $db->loadRowList();
		$count = count($rows);
		if ($count > 0) {
			$result[] = $this->text(0, 'At least one content item has the language set not to ALL.');
		}

		// Check categories
		$query->clear();
		$query->select('id');
		$query->from('#__categories');
		$query->where('language != ' . $db->quote('*'));
		$query->where('published > 0');
		$db->setQuery($query);

		$rows  = $db->loadRowList();
		$count = count($rows);
		if ($count > 0) {
			$result[] = $this->text(0, 'At least one category has the language set not to ALL.');
		}

		// Check modules
		$query->clear();
		$query->select('id');
		$query->from('#__modules');
		$query->where('language != ' . $db->quote('*'));
		$query->where('published > 0');
		$db->setQuery($query);

		$rows  = $db->loadRowList();
		$count = count($rows);
		if ($count > 0) {
			$result[] = $this->text(0, 'At least one module has the language set not to ALL.');
		}

		// Check editor
		$config = JFactory::getConfig();
		if ($config->get('editor') != 'tinymce') {
			$result[] = $this->text(2, 'The default editor is not tinymce, jDiction does only support tinymce at the moment.');
		}

		//language url tag must be the first part of the language tag

		//image tag 2 characters

		//language tag first 2 lower last 2 upper case

		//check if all images exists

		// Check the backend template
		if ((version_compare(JVERSION, '3.0.0', 'lt') && JFactory::getApplication()->getTemplate() != 'bluestork') || (version_compare(JVERSION, '3.0.0', 'ge') && JFactory::getApplication()->getTemplate() != 'isis')) {
			$result[] = $this->text(1, 'You are not using the default backend template.');
		}

		if (count($result) == 0) {
			$result[] = $this->text(0, 'It seems that there are no common mistakes.');
		}

		return $result;
	}

	protected function text($status, $text) {
		switch ($status) {
			case 0:
				$class = 'jdiction-ok';
				break;
			case 1:
				$class = 'jdiction-warning';
				break;
			case 2:
				$class = 'jdiction-error';
				break;
			default:
				$class = '';
		}
		return '<span class="' . $class . '">' . $text . '</span>';
	}
} 
