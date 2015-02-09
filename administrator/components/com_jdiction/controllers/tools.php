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

jimport('joomla.application.component.controllerform');

/**
 * Translation Controller
 *
 * @package jDiction
 */
class jDictionControllerTools extends JControllerForm {
	/**
	 * @var		string	The prefix to use with controller messages.
	 */
	protected $text_prefix = 'COM_JDICTION_TRANSLATION';

  /**
   * exports the selected Language to csv
   */
  public function export() {
		// Set output format to raw
    JFactory::getApplication()->input->set('format', 'raw');

    $model = $this->getModel('export', 'jDictionModel');
    $model->export();

		Jexit();
	}

  /**
   * import File to Database
   * @return bool
   */
  public function import() {

    $model = $this->getModel('import', 'jDictionModel');
    $model->import();

		$this->setRedirect(
			JRoute::_(
				'index.php?option=' . $this->option . '&view=import', false
			)
		);
	}
	
	public function cancel() {
		$this->setRedirect(
			JRoute::_(
				'index.php?option=' . $this->option . '&view=' . $this->view_list
				. $this->getRedirectToListAppend(), false
			)
		);
	}
}
