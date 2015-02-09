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

jimport('joomla.form.form');

/**
 * Extends the JForm class to add multi language options
 */
class jDForm extends JForm {
	
	/**
	 * @var array the private language data store
	 */
	private $data_store = array();

  /**
   * @var object the current language id
   */
  public $currentLanguage;

  /**
   * @var jDiction the global jdiction object
   */
  protected $jd;
	
	
	/**
	 * Method to instantiate the form object.
	 *
	 * @param	string	$name		The name of the form.
	 * @param	array	$options	An array of form options.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public function __construct($name, array $options = array(), $reference = null)
	{
		parent::__construct($name, $options);
		self::addRulePath(JPATH_LIBRARIES.'/jdiction/form/rules');
    self::addFieldPath(JPATH_LIBRARIES.'/jdiction/form/fields');
		$this->reference = $reference;
    $this->jd = jDiction::getInstance();
	}	

	/**
	 * Method to apply an input filter to a value based on field data.
	 *
	 * @param	string	$element	The XML element object representation of the form field.
	 * @param	mixed	$value		The value to filter for the field.
	 *
	 * @return	mixed	The filtered value.
	 * @since	1.6
	 */
	protected function filterField($element, $value) {

		$value = parent::filterField($element, $value);
		
		// Make sure there is a valid JXMLElement.
		if (!($element instanceof SimpleXMLElement)) {
			return false;
		}
		
		//TODO: Bad Hack
		static $values;
		if (!is_array($values[$this->_currentLanguage])) {
			$values[$this->_currentLanguage] = array();
		}

		$values[$this->_currentLanguage][(string)$element['name']] = $value;

		// Get the field filter type.
		$filter = (string) $element['filter'];

		switch (strtoupper($filter))
		{
			// Access Control Rules.
			case 'JDALIAS':
				// Initialize variables.
				$field	= (string) $element['field'];
		
				// Check that a validation field is set.
				if (!$field) {
					throw new Exception(JText::sprintf('JLIB_FORM_INVALID_FORM_RULE', get_class($this)));
				}
		
				if (trim($value) == '') {
					// Check if source Field is empty
					$value = $values[$this->_currentLanguage][$field];
				}
		
				$value = JApplication::stringURLSafe($value);
				$value = str_replace('--','-',$value);
				
				if (trim(str_replace('-','',$value)) == '') {
					$value = JFactory::getDate()->format('Y-m-d-H-i-s');
				}
				$values[$this->_currentLanguage][(string)$element['name']] = $value;
			break;
			case  'JDPATH':
				//Hack for menu
				require_once(JPATH_LIBRARIES.'/joomla/database/table/menu.php');
				$table = JTable::getInstance('menu');
				$table->load($this->reference->id);
				$segments = $table->getPath();
				// Make sure to remove the root path if it exists in the list.
				if ($segments[0]->alias == 'root') {
					array_shift($segments);
				}

				array_pop($segments);
				$path = "";
				// Build the path.
				foreach($segments as $segment) {
					$path .= $segment->alias.'/';
				}
				$path = trim($path, ' /\\');
				$path .= '/'.$values[$this->_currentLanguage]['alias'];
				
				$value = $path;
			break;
		}

		return $value;
	}			
	
	/**
	 * Method to filter the form data.
	 *
	 * @param	array	$data	An array of field values to filter.
	 * @param	string	$group	The dot-separated form group path on which to filter the fields.
	 *
	 * @return	mixed	boolean	True on sucess.
	 * @since	1.6
	 */
	public function filter($langdata, $group = null) {
		$result = array();

		require_once(JPATH_ADMINISTRATOR.'/components/com_content/helpers/content.php');
		foreach($langdata as $lang=>$data) {
			$this->_currentLanguage = $lang;
			$result[$lang] = parent::filter($data, $group);
		}

		return $result;
	}

  public function load($data, $replace = true, $xpath = false) {
    // If the data to load isn't already an XML element or string return false.
    if ((!($data instanceof SimpleXMLElement)) && (!is_string($data))) {
      return false;
    }

    // Attempt to load the XML if a string.
    if (is_string($data)) {
      try {
        $data = new SimpleXMLElement($data);
      } catch (Exception $e) {
        return false;
      }

      // Make sure the XML loaded correctly.
      if (!$data) {
        return false;
      }
    }
    if ((string)$data['import']) {
      $imports = explode(',',(string)$data['import']);
      foreach($imports as $import) {
        if (file_exists(JPATH_ADMINISTRATOR.'/components/'.$this->reference->option.'/models/forms/'.$import)) {
          $newxml = new DomDocument;
          $newxml->loadXML(file_get_contents(JPATH_ADMINISTRATOR.'/components/'.$this->reference->option.'/models/forms/'.$import));
          $newxpath = new DomXPath($newxml);
          $olddom = new DomDocument;
          $olddom->loadXML($data->asXML());
          $oldxpath = new DomXPath($olddom);

          $fieldsets = $oldxpath->query('//fieldset');
          foreach($fieldsets as $fieldset) {
            if ((string)$fieldset->getAttribute('reffield')) {
              $newfieldset = $newxpath->query("//fieldset[@name='".(string)$fieldset->getAttribute('reffield')."']");
            } else {
              $newfieldset = $newxpath->query("//fieldset[@name='".(string)$fieldset->getAttribute('name')."']");
            }
            if ($newfieldset->length) {
              $fieldsetAttributes = $fieldset->attributes;
              foreach($newfieldset->item(0)->attributes as $k=>$attribute) {
                $add = true;
                foreach($fieldsetAttributes as $k2=>$attribute2) {
                  if ((string)$k == (string)$k2) {
                    $add = false;
                  }
                }
                if ($add) {
                  $attrib = $olddom->createAttribute((string)$k);
                  $attrib->value = (string)$attribute->value;
                  $fieldset->appendChild($attrib);
                }
              }
            }
          }


          $fields = $oldxpath->query('//field');
          foreach($fields as $field) {
            if ((string)$field->getAttribute('reffield')) {
              $newfield = $newxpath->query("//field[@name='".(string)$field->getAttribute('reffield')."']");
            } else {
              $newfield = $newxpath->query("//field[@name='".(string)$field->getAttribute('name')."']");
            }
            if ($newfield->length) {
              $newfield = $olddom->importNode($newfield->item(0)->cloneNode(true), true);

              /* Unable to merge children atm
              if ($field->hasChildNodes()) {
                foreach($field->childNodes as $child) {
                  if ($child->nodeType == 3) {
                    continue;
                  }
                  if ((string)$child->getAttribute('reffield')) {
                    $newchild = $newxpath->query("//field[@name='".(string)$newfield->getAttribute('name')."']/*[@name='".(string)$child->getAttribute('reffield')."']");
                  } else {
                    $newchild = $newxpath->query("//field[@name='".(string)$newfield->getAttribute('name')."']/*[@name='".(string)$child->getAttribute('name')."']");
                  }
                  printpre("//field[@name='".(string)$newfield->getAttribute('name')."']/*[@name='".(string)$child->getAttribute('name')."']");
                  if ($newchild->length) {
                    $newchild = $olddom->importNode($newchild->item(0)->cloneNode(true), true);
                    if ($child->hasAttributes()) {
                      foreach($child->attributes as $childattribute) {
                        $newchild->setAttribute($childattribute->name, $childattribute->value);
                      }
                    }

                    $newchild->parentNode->replaceChild($newchild, $newchild);
                  }
                }
              }
              */

              //we don't required field by default
              $newfield->removeAttribute('required');
              if ($field->hasAttributes()) {
                foreach($field->attributes as $attribute) {
                  $newfield->setAttribute($attribute->name, $attribute->value);
                }
              }

              $field->parentNode->replaceChild($newfield, $field);
            }
          }
          $data = simplexml_load_string($olddom->saveXML());

        }
      }
    }

    return parent::load($data, $replace, $xpath);

  }
	
	/**
	 * Method to validate form data.
	 *
	 * Validation warnings will be pushed into JForm::errors and should be
	 * retrieved with JForm::getErrors() when validate returns boolean false.
	 *
	 * @param	array	$data	An array of field values to validate.
	 * @param	string	$group	The optional dot-separated form group path on which to filter the
	 * 							fields to be validated.
	 *
	 * @return	mixed	boolean	True on sucess.
	 * @since	1.6
	 */
	public function validate($langdata, $group = null) {
    $result = true;
		foreach($langdata as $lang=>$data) {
			$this->_currentLanguage = $lang;
			$result = parent::validate($data, $group);
		}
		
		return $result;
	}
		
	/**
	 * getFields with a position attribute
	 */
	public function getPosition($position) {
		// Initialise variables.
		$fields = array();
		// Get all of the field elements in the field group.
		$elements = $this->xml->xpath('//position[@name="'.(string) $position.'"]');

		// If no field elements were found return empty.
		if (empty($elements)) {
			return $fields;
		}

		// Build the result array from the found field elements.
		foreach ($elements as $element) {
			$fields[] = $element->fieldset;
		}

		return $fields;
	}
	
	public function loadLanguage($language) {
		static $control;
		
		if (!isset($control)) {
			$control = $this->options['control'];
		}

    $this->currentLanguage = current($this->jd->getLanguage($language));
		$this->options['control'] = $control.'['.$language.']';
    $this->reset(false);
    if (isset($this->data_store[$language])) {
      parent::bind($this->data_store[$language]);
    } else {
      parent::bind(array());
    }

	}
	
	/**
	 * intercept bind function and store value to array for later loading
	 */
	public function bind($data)	{
		$this->data_store = $data;
	}
}
