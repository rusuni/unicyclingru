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
class jDictionModelImport extends JModelAdmin {
	/**
	 * @var		string	The prefix to use with controller messages.
	 */
	protected $text_prefix = 'COM_JDICTION_UPLOAD';

	/**
	 * returns all languages
	 */
	public function getLanguages() {
		$jd = jdiction::getInstance();
		$languages = $jd->getLanguages();
		return $languages;
	}
	
	 
		
	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	string	$type The table type to instantiate
	 * @param	string	$prefix A prefix for the table class name. Optional.
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
		// Get the form.
		$form = $this->loadForm('com_jdiction.upload', 'upload', array('control' => 'jform', 'load_data' => $loadData));
								
		if (empty($form)) {
			return false;
		}
		return $form;
	}
	
	/**
	 * returns the url to the list
	 */
  public function getListUrl($option, $view) {

    $jd = jdiction::getInstance();
    $component = $jd->getComponent($option);
    $view = $component[$view]->list;

    return $view;
  }

  public function import() {
    // Prepare variables
    $input = JFactory::getApplication()->input;
    $data = $input->get('jform', null, 'array');
    $targetlanguage = $data['language'];
    $sourcehash = array();
    $stats_items = 0;

    // Should we override native language
    $native = ($targetlanguage == JComponentHelper::getParams('com_languages')->get('site', 'en-GB'));
    $dedup = array();

    $files = new JInput($_FILES, array());
    $file = $files->get('jform', null, 'array');

    $db = JFactory::getDbo();
    $query = $db->getQuery(true);

    if (empty($file) or empty($file['name']['translation'])) {
      $this->setMessage(JText::_("COM_JDICTION_YOU_NEED_TO_SUPPLY_A_TRANSLATION_FILE"));
      $this->setRedirect(
        JRoute::_(
          'index.php?option=' . $this->option . '&view=import'
            . $this->getRedirectToItemAppend(), false
        )
      );
      return false;
    }


    $inputfile = $file['tmp_name']['translation'];
    jimport('joomla.filesystem.file');

    $filetype = JFile::getExt($file['name']['translation']);
    switch (strtoupper($filetype)) {
      case 'CSV':
        $translation = $this->importCSV($inputfile, $dedup);
        break;
      case 'XLF':
      case 'XLIFF':
        $translation = $this->importXLIFF($inputfile, $dedup);
        break;
      default:
        throw new runtimeException('Format not supported');
    }

    $jdiction = jDiction::getInstance();
    $components = $jdiction->getComponent();

    $header = array();
    foreach($components as $com=>$views) {
      foreach($views as $view) {
        $query->clear();
        $layout = $view->layout;
        $form = $view->form;

        $fields = $form->xpath('/form/fieldset/field');
        foreach($fields as $field) {
          if (($field->attributes()->export) && (strtoupper((string)$field->attributes()->export) == "FALSE")) {
            continue;
          }
          $name = (string)$field->attributes()->name;
          if (($com == 'com_content') && ($name == 'articletext')) {
            $name = 'introtext';
            $header[$com][$view->name][$layout][$name] = $name;
            $name = 'fulltext';
          }
          $header[$com][$view->name][$layout][$name] = $name;
        }
        $fields = $form->xpath('/fields/fieldset');
        foreach($fields as $field) {
          $name = (string)$field->attributes()->name;
          $subfields = $field->xpath('//fieldset/field');
          foreach($subfields as $subfield) {
            $subname = (string)$subfield->attributes()->name;
            $header[$com][$view->name][$layout][$name][$subname] = $subname;
          }
        }
      }
    }

    $model = JModelLegacy::getInstance('translation', 'jdictionModel');
    foreach($header as $com=>$views) {
      foreach($views as $view=>$layouts) {
        foreach($layouts as $layout=>$fields) {
          $store = array();
          foreach($fields as $field=>$data) {
            if (isset($translation[$com][$view][$layout][$field])) {
              foreach($translation[$com][$view][$layout][$field] as $id=>$item) {
                if (is_array($item)) {
                  foreach($item as $id2=>$item2) {
                    if (substr($item2, 0, 4) == 'REF:') {
                      list(, $dedupkey) = explode(':',$item2, 2);
                      if (array_key_exists($dedupkey, $dedup)) {
                        $item2 = $dedup[$dedupkey];
                      } else {
                        // We don't have a translation
                        continue;
                      }
                    }
                    $store[$id2][$targetlanguage][$field][$id] = $item2;
                  }
                } else {
                  if (substr($item, 0, 4) == 'REF:') {
                    list(, $dedupkey) = explode(':',$item, 2);
                    if (array_key_exists($dedupkey, $dedup)) {
                      $item = $dedup[$dedupkey];
                    } else {
                      // We don't have a translation
                      continue;
                    }
                  }
                  // @todo update this for other multicolumn fields
                  if ($com == 'com_content' && $field == 'introtext') {
                    // Workaround for #__content
                    if (isset($translation[$com][$view][$layout]['fulltext'][$id])) {
                      $item2 = $translation[$com][$view][$layout]['fulltext'][$id];
                      if (substr($item2, 0, 4) == 'REF:') {
                        list(, $dedupkey) = explode(':',$item2, 2);
                        if (array_key_exists($dedupkey, $dedup)) {
                          $item2 = $dedup[$dedupkey];
                        } else {
                          // We don't have a translation
                          continue;
                        }
                      }

                      $store[$id][$targetlanguage]['articletext'] = $item . '<hr id="system-readmore" />' . $item2;
                    } else {
                      $store[$id][$targetlanguage]['articletext'] = $item;
                    }
                  }

                  $store[$id][$targetlanguage][$field] = $item;
                }
                if (isset($translation[$com][$view][$layout]['JD_SOURCEHASH'][$id])) {
                  $sourcehash[$id] = $translation[$com][$view][$layout]['JD_SOURCEHASH'][$id];
                } else {
                  $sourcehash[$id] = '';
                }
              }
            }
          }

          $input->set('jd_option', $com);
          $input->set('jd_view', $view);
          $input->set('jd_layout', $layout);
          foreach($store as $id=>$data) {
            $stats_items++;
            //we override the native language
            if ($native) {

              $table = $jdiction->getTableByView($com, $view);
              $query->clear();
              $query->update($table->name);
              $query->where($db->qn($table->key).' = '.$db->q($id));
              foreach($data[$targetlanguage] as $field=>$value) {
                if (is_array($value)) {
                  $query->set($db->qn($field).' = '.$db->q(json_encode($value)));
                } else {
                  $query->set($db->qn($field).' = '.$db->q($value));
                }
              }

              $db->setQuery($query);
              if (!$db->query()) {
                die('query, db error');
              }

              //we update a translation
            } else {
              $input->set('jd_id', $id);
              $input->set('jd_sourcehash', $sourcehash[$id]);
              $form = $model->getForm($data, false);
              if ($form) {
                // Test whether the data is valid.
                if ($validData = $model->validate($form, $data)) {
                  $result = $model->save($validData);
                } else {
                  // Error
                }
              } else {
                //printpre($form, $data, $input);
              }
            }
          }

        }
      }
    }
    JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_JDICTION_IMPORT_SUCCESS', $stats_items));
  }

  /**
   * Loads the CSV File into an array
   *
   * @param string $inputfile
   * @return the translated content as array
   */
  protected function importCSV($inputfile, &$dedup) {

    $translation = array();

    // Read CSV File
    if (($handle = fopen($inputfile, "r")) !== FALSE) {
      //skip the first line
      $skipline = true;
      while (($data = fgetcsv($handle, null, ";", '"')) !== FALSE) {
        if ($skipline) {
          $skipline = false;
          continue;
        }
        $dedup[$data[0].'.'.$data[1]] = $data[3];

        $path = explode('.',$data[1]);
        $root = array();
        $target = & $root;
        foreach($path as $item) {
          $target[$item] = array();
          $target = & $target[$item];
        }

        $target[$data[0]] = $data[3];

        $translation = jDictionTranslationHelper::array_merge_recursive($translation, $root);
      }
      fclose($handle);
    }

    return $translation;
  }

  /**
   * Reads an XLIFF file into an array for the import function
   *
   * @param string $inputfile Path to the file
   * @return array|mixed translated array
   */

  protected function importXLIFF($inputfile, &$dedup) {
    $translation = array();

    // Read file and detect and remove bom
    $content = file_get_contents($inputfile);
    if (strncmp($content, "\xef\xbb\xbf", 3) == 0) {
      $content = substr($content, 3);
    }

    // Read XLIFF File
    $xml = simplexml_load_string( str_replace('xmlns=', 'ns=', $content));
    if ($xml !== FALSE) {
      $items = $xml->xpath('//trans-unit/target[contains("final signed-off translated", @state)]/..');
      foreach($items as $item) {
        $attribs = $item->attributes();
        $dedup[(string)$attribs['id']] = (string)$item[0]->target;;

        $path = explode('.', (string)$attribs['id']);
        $id = array_shift($path);

        $root = array();
        $target = & $root;
        $i = 0;
        foreach($path as $obj) {
          $i++;
          $target[$obj] = array();
          $target = & $target[$obj];
          if ($i == 3) {
            if ((string)$attribs['extradata'] != '') {
              $target['JD_SOURCEHASH'] = array($id=>(string)$attribs['extradata']);
            } else {
              $file = $item[0]->xpath('../..');
              $fileattribs = $file[0]->attributes();
              $target['JD_SOURCEHASH'] = array($id=>(string)$fileattribs['original']);
            }
          }
        }

        $target[$id] = (string)$item[0]->target;
        $translation = jDictionTranslationHelper::array_merge_recursive($translation, $root);
      }
    }

    return $translation;
  }

  /**
	 * Method to save the form data.
	 *
	 * @param	array	$data	The form data.
	 *
	 * @return	boolean	True on success.
	 * @since	1.6
	 */
	public function save($langdata)
	{
		// Initialise variables;
		$dispatcher = JDispatcher::getInstance();
		$table		= $this->getTable();
		$key			= $table->getKeyName();
		$pk			= (!empty($data[$key])) ? $data[$key] : (int)$this->getState($this->getName().'.id');
		$isNew		= true;
		$status = (array)JFactory::getApplication()->input->getVar('jdiction');

		$db = JFactory::getDbo();

		// Include the content plugins for the on save events.
		//JPluginHelper::importPlugin('content');

		$query = $db->getQuery(true);
		foreach($this->getLanguages() as $language) {
			
			// Allow an exception to be throw.
			try {
				
				$query->clear();
				$query->select('s.idJdStore');
				$query->from('#__jd_store s');
				$query->where('idReference='.$db->quote($this->reference->id));
				$query->where('referenceTable='.$db->quote($this->reference->table));
				$query->where('idLang='.$db->quote($language->lang_id));
				$db->setQuery($query);
				$row = $db->loadObject();
				
				$pk = (int) $row->idJdStore;
				
				// Load the row if saving an existing record.
				if ($pk > 0) {
					$table->load($pk);
					$isNew = false;
				}
				$data = Array();
				$data['value'] = jDictionTranslationHelper::decodeTranslation($table->value);
				
				$data['idJdStore'] = $pk;
				$data['idLang'] = $language->lang_id;
				$data['idReference'] = $this->reference->id;
				$data['referenceTable'] = $this->reference->table;
				$data['referenceOption'] = $this->reference->option;
				$data['referenceView'] = $this->reference->view;
				$data['referenceLayout'] = $this->reference->layout;

				foreach($langdata[$language->lang_code] as $k=>$item) {
					switch($status[$language->lang_code][$k]) {
						case 'remove':
							unset($langdata[$language->lang_code][$k]);
							break;
						case 'unchanged':
							if ($data['referenceTable'] == 'content' && $k == 'articletext') {
								if (!isset($data['value']['introtext'])) {
									unset($langdata[$language->lang_code][$k]);
								}
							} else {
								if (!isset($data['value'][$k])) {
									unset($langdata[$language->lang_code][$k]);
								}
							}
							break;
						case 'changed':
						default:
					}
				}

				if ($data['referenceTable'] == 'content' && isset($langdata[$language->lang_code]['articletext'])) {
					$pattern = '#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i';
					$tagPos	= preg_match($pattern, $langdata[$language->lang_code]['articletext']);
		
					if ($tagPos == 0) {
						$langdata[$language->lang_code]['introtext'] = $langdata[$language->lang_code]['articletext'];
						$langdata[$language->lang_code]['fulltext'] = '';
					} else {
						list($langdata[$language->lang_code]['introtext'], $langdata[$language->lang_code]['fulltext']) = preg_split($pattern, $langdata[$language->lang_code]['articletext'], 2);
					}
				}

				$data['value'] = jDictionTranslationHelper::encodeTranslation($langdata[$language->lang_code]);
				jimport('joomla.utilities.date');
				$data['modified'] = JFactory::getDate()->toSQL();
				$data['modified_by'] = 0;
				if ($langdata[$language->lang_code]['state']) {
					$data['state'] = $langdata[$language->lang_code]['state'];
				} else {
					$data['state'] = 1;
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
	function validate($form, $data)
	{
		// Filter and validate the form data.
			$data	= $form->form->filter($data);
			$return	= $form->form->validate($data);

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
