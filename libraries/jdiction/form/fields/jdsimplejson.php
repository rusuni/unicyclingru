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
defined('_JEXEC') or die;

jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('multi');


/**
 * Form Field class for the Joomla Platform.
 * Supports a generic Multivalue fieldlist.
 *
 * @package     itrLib
 */
class JFormFieldJDSimpleJSON extends JFormField
{
  /**
   * The form field type.
   *
   * @var    string
   * @since  11.1
   */
  protected $type = 'JDSimpleJSON';

  /**
   * Method to get the field input markup for a generic list.
   * Use the multiple attribute to enable multiselect.
   *
   * @return  string  The field input markup.
   *
   * @since   11.1
   */
  protected function getInput()	{


    // Initialize variables.
    $html = array();
    $attr = '';
    $size = $this->element['size'] ? (int) $this->element['size'] : 10;
    $name = $this->element['name'];

    // Initialize some field attributes.
    $attr .= $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';

    // To avoid user's confusion, readonly="true" should imply disabled="true".
    if ((string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true')
    {
      $attr .= ' disabled="disabled"';
    }

    // Initialize JavaScript field attributes.
    $attr .= $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : '';
    // Get the input fields.
    $html[] = '<table style="float: left; margin-bottom: 15px" cellpadding="0" cellspacing="0">';

    for($i=0; $i<$size; $i++) {
      if (count($this->element->children())) {
        $this->value[$i] = (array) $this->value[$i];
      }
      if ($this->element['field_value']) {
        if (is_array($this->value[$i])) {
          $value = $this->value[$i][(string)$this->element['field_value']];
          $name = $this->name . '['.$i.']['.(string)$this->element['field_value'].']';
        } elseif(is_object($this->value[$i])) {
          $value = $this->value[$i]->{(string)$this->element['field_value']};
          $name = $this->name . '['.$i.']['.(string)$this->element['field_value'].']';
        } else {
          $value = '';
          $name = $this->name . '['.$i.']['.(string)$this->element['field_value'].']';
        }
      } else {
        $value = $this->value[$i];
        $name = $this->name . '['.$i.']';
      }

      $html[] = '<tr><td>';
      $html[] = '<input class="inputbox" type="text" name="'.$name.'" id="' . $this->id . $i . '" size="60" maxlength="255" value="'.$value.'" '.$attr.' />';
      $html[] = '</td>';

      foreach ($this->element->children() as $option) {
        // Only add <option /> elements.
        if ($option->getName() != 'option') {
          continue;
        }
        $html[] = '<td>';
        switch ((string)$option['type']) {
          case 'checkbox':
            $html[] = '<input class="checkbox" type="checkbox" name="'.$this->name.'['.$i.']['.(string)$option['name'].']" id="' . (string)$option['name'] . $i . '" size="60" maxlength="255" value="'.(string)$option['value'].'" '.($this->value[$i][(string)$option['name']] ?'checked="checked"' :'').' />';
            $html[] = '</td><td>&nbsp;';
            $html[] = JText::_((string)$option['label']);
            break;
          case 'input':
          default:
            $html[] = '<input class="inputbox" type="text" name="'.$this->name.'['.$i.']['.(string)$option['name'].']" id="' . (string)$option['name'] . $i . '" size="60" maxlength="255" value="'.$this->value[$i][(string)$option['name']].'" />';
        }
        $html[] = '</td>';
      }
      $html[] = '</tr>';
    }
    $html[] = '</table>';

    return implode($html);
  }

}