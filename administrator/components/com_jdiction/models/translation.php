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

jimport('joomla.application.component.modeladmin');

/**
 * Methods supporting a list of Translation records.
 *
 * @package		jDiction
 */
class jDictionModelTranslation extends JModelAdmin {
	/**
	 * @var		string	The prefix to use with controller messages.
	 */
	protected $text_prefix = 'COM_JDICTION_TRANSLATION';

  /**
   * the untranslated object
   * @var object
   */
  protected $original;

  /**
   * the reference entry
   * @var object
   */
  protected $reference;

	public function getItem($pk = null) {
	
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('idReference as id, referenceOption as `option`, referenceView as view, referenceLayout as layout, sourcehash');
		$query->from('#__jd_store');
		$query->where('idJdStore='.$db->quote($pk));
		
		$db->setQuery($query);
		
		$this->reference = $db->loadObject();
		$this->loadForm($this->reference);

		return $this->reference;
	}
	
	public function getOriginal($pk) {
		$jd = jdiction::getInstance();

    $this->original = $jd->getOriginalById($this->reference->option, $this->reference->view, $pk);

    /*
		$table = $jd->getTableByView($this->reference->option, $this->reference->view);
    $db = JFactory::getDbo();
		$query = $db->getQuery(true);

    $this->fields = $fields = $jd->getFieldsByView($this->reference->option, $this->reference->view);

    foreach($fields as $field) {
      if (isset($field->multifield) && is_array($field->multifield)) {
        foreach($field->multifield as $multifield) {
          $query->select($db->quoteName($multifield));
        }
      } else {
        $query->select($db->quoteName($field->name));
      }
    }
		$query->from($table->name);
		$query->where($db->quoteName($table->key).'='.$db->quote($pk));
		$db->setQuery($query);
		$this->original = $db->loadObject();
    */
    $this->original->jd_sourcehash = $jd->getTranslationHashByView($this->reference->option, $this->reference->view, $this->original);


    /* Transform multifields to one field after sourcehash calulation */
    $this->fields = $jd->getFieldsByView($this->reference->option, $this->reference->view);

    foreach($this->fields as $field) {
      if (isset($field->multifield) && is_array($field->multifield)) {
        $this->original->{$field->name} = '';
        foreach($field->multifield as $k=>$multifield) {
          if (isset($this->original->$multifield) && $this->original->$multifield != '') {
            if ($k > 0) {
              $this->original->{$field->name} .= $field->seperator;
            }
            $this->original->{$field->name} .= $this->original->$multifield;
          }
        }
      }
    }

		return $this->original;
	}
	
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData()	{
		
		$db = JFactory::getDbo();
		
		$query = $db->getQuery(true);
		$query->select('s.*,l.lang_code');
		$query->from('#__jd_store s');
		$query->join('left', '#__languages l on s.idLang = l.lang_id');
		$query->where('idReference='.$db->quote($this->reference->id));
		$query->where('referenceTable='.$db->quote($this->reference->table));
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		$array = Array();
		foreach ($rows as $row) {
			$array[$row->lang_code] = jDictionTranslationHelper::decodeTranslation($row->value);
			$array[$row->lang_code]['idJdStore'] = $row->idJdStore;
		}

		return $array;

		/** @TODO make it more joomla */
		
		// Check the session for previously entered form data.
    /*
		$data = JFactory::getApplication()->getUserState('com_jdiction.edit.translation.data', array());
		if (empty($data)) {
			$data = $this->getItem();
		}
		if (is_object($data)) {
			$data = JArrayHelper::fromObject($data);
		}
		
		return $data;
    */
	}
	
	/**
	 * returns all languages
	 */
	public function getLanguages() {
		$jd = jdiction::getInstance();
		$languages = $jd->getLanguages();
		return $languages;
	}
	
	/**
	 * Load component
	 */
	public function loadForm($reference, $source=null, $options = array('control'=>'jform'), $clear = false, $xpath = false) {

		if (!$reference->option ||
			!$reference->view ||
			!$reference->layout ||
			!$reference->id) {
			return false;
		}
		$lang = JFactory::getLanguage();

    //some components uses front and backend translation in backend
    $lang->load($reference->option, JPATH_SITE);
    $lang->load($reference->option, JPATH_SITE.'/components/'.$reference->option);
    $lang->load($reference->option);
		$lang->load($reference->option, JPATH_ADMINISTRATOR.'/components/'.$reference->option);

		$this->reference = $reference;
		$jd = jdiction::getInstance();
    $view = $jd->getView($reference->option, $reference->view);
		$reference->table = $view->primarytable;
		jimport('jdiction.form.form');
		$reference->form = new JDForm($reference->option, $options, $reference);

		$langform = $view->form;
		$reference->form->load($langform, true, '');

    $array = $this->loadFormData();
    $reference->data = $array;
    $reference->form->bind($array);

		return $reference;
	}
	 
		
	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	string $type	The table type to instantiate
	 * @param	string $prefix	A prefix for the table class name. Optional.
	 * @param	array	$config Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'store', $prefix = 'jDictionTable', $config = array()) {
		return parent::getTable($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true) {
		$input = JFactory::getApplication()->input;
		$option = $input->get('jd_option', false, 'cmd');
		if ($option) {
			$reference = new stdClass;
			$reference->option = $option;
			$reference->view 	 = $input->get('jd_view', false, 'cmd');
			$reference->layout = $input->get('jd_layout', false, 'cmd');
			$reference->id 	   = $input->get('jd_id', false, 'int');
		} else {
			JError::raiseError(500, 'No Component given');
		}

		// Get the form.
		$this->loadForm($reference, 'translation', array('control' => 'jdiction', 'load_data' => $loadData));
		if (empty($reference->form)) {
			return false;
		}

		$reference->form->bind($data);
		return $reference;
	}
	
	/**
	 * Method to save the form data.
	 *
	 * @param	array	$data	The form data.
	 *
	 * @return	boolean	True on success.
	 * @since	1.6
	 */
	public function save($langdata)	{
		// Initialise variables;
		$dispatcher = JDispatcher::getInstance();
		$table		= $this->getTable();
		$key			= $table->getKeyName();
    $jd       = jDiction::getInstance();
		$pk			= (!empty($data[$key])) ? $data[$key] : (int)$this->getState($this->getName().'.id');
		$status = JFactory::getApplication()->input->get('jdiction', array(), 'array');
    $sourcehash = JFactory::getApplication()->input->get('jd_sourcehash', '', 'BASE64');
    $fields     = $jd->getFieldsByView($this->reference->option, $this->reference->view);

		$db = JFactory::getDbo();

		// Include the content plugins for the on save events.
		//JPluginHelper::importPlugin('content');

		$query = $db->getQuery(true);
		foreach($this->getLanguages() as $language) {
			if (!isset($langdata[$language->lang_code])) {
				continue;
			}
      $isNew		= true;
      $isUpdated = false;
      $data = Array();
      $specialdata = array();

			// Allow an exception to be throw.
			try {
				
				$query->clear();
				$query->select('idJdStore');
				$query->from('#__jd_store');
				$query->where($db->quoteName('idReference').'='.$db->quote($this->reference->id));
				$query->where($db->quoteName('referenceTable').'='.$db->quote($this->reference->table));
				$query->where($db->quoteName('idLang').'='.$db->quote($language->lang_id));
				$db->setQuery($query);
        $pk = (int) $db->loadResult();
				
				// Load the row if saving an existing record.
				if ($pk > 0) {
					$table->load($pk);
					$isNew = false;
				} else {
          $table->reset();
        }

        $data['value'] = jDictionTranslationHelper::decodeTranslation($table->value);
				
				$data['idJdStore'] = $pk;
				$data['idLang'] = $language->lang_id;
				$data['idReference'] = $this->reference->id;
				$data['referenceTable'] = $this->reference->table;
				$data['referenceOption'] = $this->reference->option;
				$data['referenceView'] = $this->reference->view;
				$data['referenceLayout'] = $this->reference->layout;

				foreach($langdata[$language->lang_code] as $k=>$item) {
          if (is_array($item)) {
            foreach($item as $k2=>$item2) {
              switch($status[$language->lang_code][$k][$k2]) {
                case 'unchanged':
                  if (!isset($data['value'][$k][$k2])) {
                    unset($langdata[$language->lang_code][$k][$k2]);
                  }
                  if (empty($langdata[$language->lang_code][$k])) {
                    unset($langdata[$language->lang_code][$k]);
                  }
                  break;
                case 'remove':
                  unset($langdata[$language->lang_code][$k][$k2]);
                  if (empty($langdata[$language->lang_code][$k])) {
                    unset($langdata[$language->lang_code][$k]);
                  }
                  $isUpdated = true;
                  break;
                case 'changed':
                default:
                  $isUpdated = true;
              }
            }

            // used for JDMore
            if (strtolower($fields[$k]->type) == 'jdmore') {
              $specialdata[$k] = $langdata[$language->lang_code][$k];
              unset($langdata[$language->lang_code][$k]);
            }
          } else {
            if (version_compare(JVERSION, '3.2.0', 'ge') && (string)$this->reference->form->getField($k)->getAttribute('type') == 'jdalias') {
              $refField = (string)$this->reference->form->getField($k)->getAttribute('field');
              switch($status[$language->lang_code][$k]) {
                case 'remove':
                  unset($langdata[$language->lang_code][$k]);
                  $isUpdated = true;
                  break;
                case 'unchanged':
                case 'changed':
                default:
                  if ($langdata[$language->lang_code][$k] == '') {
                    $langdata[$language->lang_code][$k] = $langdata[$language->lang_code][$refField];
                  }
              }

              $langdata[$language->lang_code][$k] = JApplicationHelper::stringURLSafe($langdata[$language->lang_code][$k]);
              if (trim(str_replace('-','',$langdata[$language->lang_code][$k])) == '') {
                unset($langdata[$language->lang_code][$k]);
                $isUpdated = true;
              } else {
                if ($data['value'][$k] != $langdata[$language->lang_code][$k]) {
                  $isUpdated = true;
                }
              }
              unset($refField);
            } else {
              switch($status[$language->lang_code][$k]) {
                case 'unchanged':
                  if (!isset($data['value'][$k])) {
                    unset($langdata[$language->lang_code][$k]);
                  }
                  break;
                case 'remove':
                  unset($langdata[$language->lang_code][$k]);
                  $isUpdated = true;
                  break;
                case 'changed':
                default:
                  $isUpdated = true;
              }

            }
          }
				}

        // If the translation is empty delete the row in the database and continue with the next translation
        if (empty($langdata[$language->lang_code])) {
          if (!$isNew) {
            $table->delete();
          }
          if (empty($specialdata)) {

            continue;
          }
        }

				$data['value'] = jDictionTranslationHelper::encodeTranslation($langdata[$language->lang_code]);
				$data['modified'] = JFactory::getDate()->toSql();
				$data['modified_by'] = 0;

        /* no state handling atm */
        /*
        if ($langdata[$language->lang_code]['state']) {
					$data['state'] = $langdata[$language->lang_code]['state'];
				} else {
					$data['state'] = 1;
				}
        */
        if ($isUpdated) {
          $data['sourcehash'] = $sourcehash;
          $currentsourcehash = $jd->getTranslationHashById($data['referenceOption'], $data['referenceView'], $data['idReference']);
          if ($sourcehash == $currentsourcehash) {
            $data['state'] = 1;
          } else {
            $data['state'] = 2;
          }
        } else {
          continue;
        }

				// Bind the data.
				if (!$table->bind($data)) {
					$this->setError($table->getError());
					return false;
				}
	
				// Prepare the row for saving
				$this->prepareTable($table);
	
				// Check the data.
				if (!$table->check()) {
					$this->setError($table->getError());
					return false;
				}
	
				// Trigger the onContentBeforeSave event.
				$result = $dispatcher->trigger($this->event_before_save, array($this->option.'.'.$this->name, &$table, $isNew));
				if (in_array(false, $result, true)) {
					$this->setError($table->getError());
					return false;
				}
	
				// Store the data.
				if (!$table->store()) {
					$this->setError($table->getError());
					return false;
				}

        if (!empty($specialdata)) {
          foreach($specialdata as $k=>$value) {
            $field = $fields[$k];
            $data2 = array();
            $data2['idLang'] = $language->lang_id;
            $data2['referenceTable'] = (string)$field->table;
            $data2['referenceOption'] = $this->reference->option;
            $data2['referenceView'] = $this->reference->view;
            $data2['referenceLayout'] = $this->reference->layout;
            $data2['modified'] = JFactory::getDate()->toSql();
            $data2['modified_by'] = 0;
            $data2['state'] = 1;

            foreach($value as $fkid=>$value2) {
              $data2['idReference'] = $fkid;
              $table->idJdStore = 0;
              $table->load(array('idLang'=>$language->lang_id, 'referenceTable'=>$data2['referenceTable'], 'idReference'=>$data2['idReference']));

              // if a field is empty remove it.
              foreach($value2 as $t1=>$v1) {
                if (empty($v1)) {
                  unset($value2[$t1]);
                }
              }
              // if all fields are empty remove row
              if (empty($value2)) {
                $table->delete();
                continue;
              }


              $data2['value'] = jDictionTranslationHelper::encodeTranslation($value2);
              $table->bind($data2);

              // Bind the data.
              if (!$table->bind($data2)) {
                $this->setError($table->getError());
                return false;
              }

              // Check the data.
              if (!$table->check()) {
                $this->setError($table->getError());
                return false;
              }

              // Trigger the onContentBeforeSave event.
              $result = $dispatcher->trigger($this->event_before_save, array($this->option.'.'.$this->name.'.'.$k, &$table, $isNew));
              if (in_array(false, $result, true)) {
                $this->setError($table->getError());
                return false;
              }

              // Store the data.
              if (!$table->store()) {
                $this->setError($table->getError());
                return false;
              }

            }
          }
        }

	
				// Clean the cache.
				$cache = JFactory::getCache('jDiction');
				$cache->clean();
	
				// Trigger the onContentAfterSave event.
				$dispatcher->trigger($this->event_after_save, array($this->option.'.'.$this->name, &$table, $isNew));
			}
			catch (Exception $e)
			{
				$this->setError($e->getMessage());
	
				return false;
			}
			$pkName = $table->getKeyName();
			if (isset($table->$pkName)) {
				$this->setState($this->getName().'.id', $table->$pkName);
			}
			$this->setState($this->getName().'.new', $isNew);
		}

		return true;
	}

	/**
	 * Method to validate the form data.
	 *
	 * @param	object		$form		The form to validate against.
	 * @param	array		$data		The data to validate.
	 * @return	mixed		Array of filtered data if valid, false otherwise.
	 * @since	1.1
	 */
	function validate($form, $data, $group = null)
	{
    // Filter and validate the form data.
			$data	= $form->form->filter($data);
    $return	= $form->form->validate($data, $group);

			// Check for an error.
			if (JError::isError($return)) {
				$this->setError($return->getMessage());

				return false;
			}
	
			// Check the validation results.
			if ($return === false) {
				// Get the validation messages from the form.
				foreach ($form->form->getErrors() as $message) {
					$this->setError(JText::_($message));
				}
	
				return false;
			}

		return $data;
	}

} 
