<?php
/**
 * jDiction library entry point
 *
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
defined('JPATH_BASE') or die;

jimport('joomla.form.formrule');

/**
 * @todo add alias translation
 * Form Rule class for the jDiction Framework.
 */
class JFormRuleJdAlias extends JFormRule {
	/**
	 * Method to test if two values are equal. To use this rule, the form
	 * XML needs a validate attribute of equals and a field attribute
	 * that is equal to the field to test against.
	 *
	 * @param	object	$element	The JXMLElement object representing the <field /> tag for the
	 * 								form field object.
	 * @param	mixed	$value		The form field value to validate.
	 * @param	string	$group		The field name group control value. This acts as as an array
	 * 								container for the field. For example if the field has name="foo"
	 * 								and the group value is set to "bar" then the full field name
	 * 								would end up being "bar[foo]".
	 * @param	object	$input		An optional JRegistry object with the entire data set to validate
	 * 								against the entire form.
	 * @param	object	$form		The form object for which the field is being tested.
	 *
	 * @return	boolean	True if the value is valid, false otherwise.
	 * @since	1.6
	 * @throws	JException on invalid rule.
	 */
	public function test(& $element, &$value, $group = null, & $input = null, & $form = null)
	{
		
		// Initialize variables.
		$field	= (string) $element['field'];

		// Check that a validation field is set.
		if (!$field) {
			throw new Exception(JText::sprintf('JLIB_FORM_INVALID_FORM_RULE', get_class($this)));
		}

		// Check that a valid JForm object is given for retrieving the validation field value.
		if (!($form instanceof JForm)) {
			throw new Exception(JText::sprintf('JLIB_FORM_INVALID_FORM_OBJECT', get_class($this)));
		}
		
		// Check if source Field is empty
		$title = $input->get($field);
		if (empty($title)) {
			return true;
		}

		if (trim($value) == '') {
			$value = $input->get($field);
		}

		$value = JApplication::stringURLSafe($value);

		if (trim(str_replace('-','',$value)) == '') {
			$value = JFactory::getDate()->format('Y-m-d-H-i-s');
		}
		printpredie($element);
		return true;
	}
}