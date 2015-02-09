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
class jDictionViewTools extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null) {
		
		$doc = JFactory::getDocument();
		$doc->addStyleSheet('/administrator/components/com_jdiction/assets/style.css');
		
		// Set toolbar items for the page
		JToolBarHelper::title(JText::_('COM_JDICTION_TOOLS'), 'jdiction-tools');

		$this->buttons = array(
			array(
				"link"=>'index.php?option=com_jdiction&view=export',
				"image"=> 'components/com_jdiction/assets/icon-48-export.png',
				"text"=> 'Export'
			),
			array(
				"link"=>'index.php?option=com_jdiction&view=import',
				"image"=> 'components/com_jdiction/assets/icon-48-import.png',
				"text"=> 'Import'
			),
      array(
        "link"=>'index.php?option=com_jdiction&view=check',
        "image"=> 'components/com_jdiction/assets/icon-48-check.png',
        "text"=> JText::_('COM_JDICTION_CHECK')
      )
    );

    // @deprecated used for Joomla 2.5
    $tpl = (version_compare(JVERSION, '3.0', 'ge') ? $tpl : '25');
		parent::display($tpl);

	}

}
