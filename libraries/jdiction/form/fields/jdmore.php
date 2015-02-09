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
class JFormFieldJDMore extends JFormField
{
  /**
   * The form field type.
   *
   * @var    string
   * @since  11.1
   */
  protected $type = 'JDMore';

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
		$body = array();
		$header = array();
    $attr = '';

    // Initialize some field attributes.
    $attr .= $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';

    // To avoid user's confusion, readonly="true" should imply disabled="true".
    if ((string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true') {
      $attr .= ' disabled="disabled"';
    }

    $options = $this->getOptions();
    $values = $this->getValues($options);
/* removed support for multiple fields per row
    foreach($options as $option) {
      if ((string)$option['type'] != 'hidden') {
        $header[] = '<th></th>';
        $header[] = '<th width="150">'.JText::_($option['label']).'</th>';
        $header[] = '<th>'.JText::_('LIB_JDICTION_TRANSLATION').'</th>';
      }
    }
*/
		$header[] = '<th></th>';
		$header[] = '<th width="150">'.JText::_('COM_JDICTION_TRANSLATION_FIELD_ORIGINAL').'</th>';
		$header[] = '<th>'.JText::_('LIB_JDICTION_TRANSLATION').'</th>';


    foreach($values as $k=>$row) {

      foreach($row as $value) {
        $field = JFormHelper::loadFieldType($value['type'], true);

        if ($field === false) {
          $field = JFormHelper::loadFieldType('text', true);
        }
        $field->setForm($this->form);

        if ($field->setup($value, (string)$value['value'], (string)$this->element['name'].'.'.$k)) {

          if ((string)$value['type'] != 'trenner' && (string)$value['type'] != 'hidden') {
						$body[$k][] = '<td style="padding: 10px; font-weight: bold;" valign="top">';
						$body[$k][] = JText::_((string)$value['label']);

						if ((string)$value['type'] == 'editor') {
							$body[$k][] = '<br><img class="info_img" src="'.JRoute::_('components/com_jdiction/assets/icon-16-del.png').'" width="16" onClick="jdiction_removeContent(\''.$field->id.'\', jQuery(\'#'.$field->id.'\').prev());" />';
						} else {
							$body[$k][] = '<br><img class="info_img" src="'.JRoute::_('components/com_jdiction/assets/icon-16-del.png').'" width="16" onClick="jdiction_removeContent(\''.$field->id.'\', jQuery(\'#'.$field->id.'\'));" />';
						}
						$body[$k][] = '<img class="info_img hasTip" src="'.JRoute::_('components/com_jdiction/assets/icon-16-info.png').'" width="16" title="Original::'.htmlspecialchars((string)$value['original']).'" onClick="jdiction_copyContent(this.retrieve(\'tip:text\'),\''.$field->id.'\');" />';

						$body[$k][] = '</td>';
            $body[$k][] = '<td style="padding: 10px; text-align: left;" valign="top">';
            $body[$k][] = (string)$value['original'];
            $body[$k][] = '</td>';
            $body[$k][] = '<td>';
						$body[$k][] = '<input type="hidden" id="'.$field->id.'_status" name="'.str_replace('jform', 'jdiction', $field->name).'" value="unchanged" title="'.htmlspecialchars($field->value).'" />';
            $body[$k][] = $field->getInput();
            $body[$k][] = '</td>';
          } else {
            $html[] = $field->getInput();
          }
        }
      }
    }

		if (!empty($body)) {
			$html[] = '<table style="float: left; margin-bottom: 15px" cellpadding="0" cellspacing="0">';
			$html[] = '<tr>';
			$html = array_merge($html, $header);
			$html[] = '</tr>';
			foreach ($body as $k=>$v) {
				$html[] = '<tr>';
				$html = array_merge($html, $v);
				$html[] = '</tr>';
			}
			$html[] = '</table>';
		} else {
			$html[] = '<p>'.JText::_('LIB_JDICTION_NO_TRANSLATION_NEEDED').'</p>';
		}

    return implode($html);
  }

  protected function getOptions() {

    $options = array();

    // Get the input fields from XML
    foreach ($this->element->children() as $option) {
      // Only add <option /> elements.
      if ($option->getName() != 'more') {
        continue;
      }

      if ($option->attributes()->notranslate) {
        continue;
      }

      //change field type from more to field so wie can use JformField
      $fieldvalue = str_replace(array('<more', 'more>'), array('<field', 'field>'), $option->asXML());

      $field = new SimpleXmlElement($fieldvalue);

      $options[] = $field;
    }

    return $options;
  }

	protected function getOption($formrefid) {

		$formtable = $this->element['formtable'] ? (string) $this->element['formtable'] : false;
		$formid = $this->element['formid'] ? (string) $this->element['formid'] : false;
		$formfield = $this->element['formfield'] ? (string) $this->element['formfield'] : false;
		$formrowlabel = $this->element['formrowlabel'] ? (string) $this->element['formrowlabel'] : false;

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($formtable);
		$query->where($db->quoteName($formid) . '=' . $db->quote($formrefid));
		$db->setQuery($query);
		$option = $db->loadObject();
		if (!$option) {
			return false;
		}
		$xml = '<field name="value" type="'.$option->$formfield.'" label="'.$option->$formrowlabel.'" />';

		$field = new SimpleXmlElement($xml);

    return $field;
  }

  protected function getValues($options) {
    $jd = jDiction::getInstance();

    $table = $this->element['table'] ? (string) $this->element['table'] : false;
    $ordering = $this->element['ordering'] ? (string) $this->element['ordering'] : false;
    $fkidname = $this->element['fkid'] ? (string) $this->element['fkid'] : false;
		$formrefid = $this->element['formrefid'] ? (string) $this->element['formrefid'] : false;
    $fkid = $this->form->reference->id;
    $pkname = $this->element['key'] ? (string) $this->element['key'] : false;

    $values = array();

    $db = JFactory::getDbo();

    //load original
    $query = $db->getQuery(true);
    $query->select('*');
    $query->from($table);
    $query->where($db->quoteName($fkidname).'='.$fkid);
    if ($ordering) {
      $query->order($ordering);
    }
    $db->setQuery($query);
    $rows = $db->loadAssocList();

    foreach($rows as $row) {

      $translations =  $jd->getTranslation($table, $row[$pkname], $this->form->currentLanguage->lang_id);

			if ($formrefid) {

				$value = $this->getOption($row[$formrefid]);

				if (!$value) {
					continue;
				}

				$value['original'] = (string)$row[(string)$value['name']];
				if (trim($value['original']) == '') {
					continue;
				}

				switch((string)$value['type']) {
					case 'select':
					case 'spacer':
						continue 2;
						break;
					case 'editor':
						$value['height'] = '200';
					  break;
					case 'input':
					case 'textarea':

						break;

				}

				$value['value'] = $translations[(string)$value['name']];


				$values[(int)$row[$pkname]][(string)$value['name']] = $value;
			} else {
				foreach($options as $option) {
					if (is_numeric((string)$row[(string)$option['name']]) || empty($row[(string)$option['name']])) {
						continue;
					}
					$value = clone $option;
					$value['value'] = $translations[(string)$option['name']];

					$value['original'] = (string)$row[(string)$option['name']];
					$values[(int)$row[$pkname]][(string)$option['name']] = $value;
				}
			}
    }

    return $values;
  }
}