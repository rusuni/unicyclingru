<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Platform.
 * Provides a list of available database connections, optionally limiting to
 * a given list.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @see         JDatabase
 * @since       11.3
 */
class JFormFieldJdTables extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.3
	 */
	public $type = 'JdTables';

	/**
	 * Method to get the list of database options.
	 *
	 * This method produces a drop down list of available databases supported
	 * by JDatabase drivers that are also supported by the application.
	 *
	 * @return  array    The field option objects.
	 *
	 * @since   11.3
	 * @see		JDatabase
	 */
	protected function getOptions()
	{
		$jdiction = jDiction::getInstance();
		$components = $jdiction->getComponent();
		
		foreach($components as $com=>$views) {
			foreach($views as $view) {
				$options[$com.'.'.$view->name] = $com.' -> '.$view->name;
			}
		}

		return $options;
	}
}
