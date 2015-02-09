<?php
/**
 * jDiction library entry point
 *
 * @package jDiction
 * @link http://joomla.itronic.at
 * @copyright	Copyright (C) 2011 ITronic Harald Leithner. All rights reserved.
 * @license GNU General Public License v3
 * @version 1.6.1 (18)
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

define('JDICTION_TRANSLATION_STATUS_FULL', 1);
define('JDICTION_TRANSLATION_STATUS_OLD', 2);
define('JDICTION_TRANSLATION_STATUS_NONE', 3);
/* unused atm */
define('JDICTION_TRANSLATION_STATUS_PARTLY', 3);
define('JDICTION_TRANSLATION_STATUS_PUBLISHED', 5);
define('JDICTION_TRANSLATION_STATUS_UNPUBLISHED', 6);
define('JDICTION_TRANSLATION_STATUS_UNSET', 7);

//check for XML Support
if (function_exists("simplexml_load_string") && class_exists("SimpleXMLElement")) {
	define('JDICTION_TRANSLATION_USEXML', true);
} else {
	define('JDICTION_TRANSLATION_USEXML', false);
}

jimport('joomla.language.helper');
jimport('jdiction.helpers.translation');
jimport('jdiction.helpers.admin');

class jDiction extends JObject {
	
	/** @var array holds all tables which could be translated jDiction */
	protected $tables = array();
	
	/** @var array holds all component task/view combination we can translate */
	protected $components = Array();
	
	/** @var array holding the cache for translations */
	protected $cache = Array();
	
	/** @var object local JDatabase object */
	protected $db;
	
	/**
	 * Dispatcher object
	 *
	 * @var JDispatcher
	 */
	protected $dispatcher;
	
	/**
	 * defines if jDiction is ready
	 * @var bool
	 */
	protected $ready=false;

	/**
	 * traceLog for debuging
	 */
	protected $traceLog=array();
	
	/**
	 * current trace Id
	 */
	protected $traceId=false;

	/**
	 * Returns the global jDiction object, only creating it
	 * if it doesn't already exist.
	 *
	 * @return jDiction	The global jDiction object
	 */
	public static function getInstance() {
		static $instance;

		if (!isset ($instance)) {
			$instance = new jDiction();
		}
		
		return $instance;
	}
	
	/**
	 * Initialise jDiction
	 */
	public function initialise() {
		$this->WorkaroundDatabaseConfig();
		//load Lanuage file
		$lang = JFactory::getLanguage();
		$lang->load('lib_jdiction', JPATH_LIBRARIES.'/jdiction');
		
		// load dispatcher and jdiction plugins
		JPluginHelper::importPlugin('jdiction');
		$this->dispatcher = JDispatcher::getInstance();
		
		$this->dispatcher->trigger('onjDictionAfterInitialise');
		
		$this->loadTables();
		
		$this->ready = true;

    $db = JFactory::getDbo();
    if (JFactory::getLanguage()->getTag() ==  JComponentHelper::getParams('com_languages')->get('site', 'en-GB')) {
      $db->setTranslate(false);
    }

	}
	
	public function WorkaroundDatabaseConfig() {
    $app = JFactory::getApplication();
		if ($app->input->get('option') == 'com_config') {
			jimport('jdiction.form.fields.databaseconnection');
		}

	}

	/**
	 * returns the jdiction Version
	 * @return string jDiction Version
	 */
	public function getVersion() {
		$xml = simplexml_load_file(JPATH_MANIFESTS.'/libraries/lib_jdiction.xml');
		return (string)$xml->version;
  }
	
	/**
	 * check the status of jdiction
	 * @return bool the status of the library
	 */
	public function getStatus() {
		return $this->ready;
	}
	
	/** 
	 * load all core table definitions and components
	 */
	public function loadTables() {

    $cache = JFactory::getCache('jDiction');
    $subdirs = $cache->get(get_class($this).'::findTables');

    $this->dispatcher->trigger('onjDictionBeforeLoadTables', array( &$subdirs));
		
		foreach ($subdirs as $path) {
			$this->parseTable($path);
		}

		$this->dispatcher->trigger('onjDictionAfterLoadTables', array( &$subdirs));
	}

  /**
   * Find all jdiction.xml files in components and in the jDiction library path
   * @return array with all jdiction.xml files
   */
  public static function findTables() {

    jimport('joomla.filesystem.folder');
    $dir = dirname(__FILE__).'/tables';
    $subdirs1 = JFolder::files($dir,'.xml', false, true);
    $subdirs2 = JFolder::files(JPATH_ADMINISTRATOR.'/components','jdiction.xml', 1, true);
    $subdirs = array_merge($subdirs1, $subdirs2);

    return $subdirs;
  }
	
	/**
	 * load xml definiation of a table with its component and views
	 */
	public function parseTable($xmlfile) {

		// Try to load the file
    $xml = simplexml_load_file($xmlfile);

    if ($xml === false) {
      return false;
    }

		// Check that it's a metadata file
		if ((string)$xml->getName() != 'jdiction') {
			return false;
		}
		
		$this->dispatcher->trigger('onjDictionParseTable', array(&$xml));

    $component = (string)$xml->component;

		foreach ($xml->sections->children() as $section) {
      $primarytable = '';
      foreach($section->tables->children() as $table) {
        if (!$primarytable) {
          $primarytable = (string)$table->name;
        }
        $this->tables[(string)$table->name] = new stdClass;
        $this->tables[(string)$table->name]->name = (string)$table->name;
        $this->tables[(string)$table->name]->key = (string)$table->key;
        $this->tables[(string)$table->name]->class = (string)$table->class;
        $this->tables[(string)$table->name]->file = (string)$table->file;
        $this->tables[(string)$table->name]->cache = new stdClass;
        $this->tables[(string)$table->name]->cache->fullfetch = (string)$table->cache['fullfetch'];
        $this->tables[(string)$table->name]->exportfilter = (string)$table->exportfilter;
        $this->tables[(string)$table->name]->component = $component;
      }

      foreach($section->views->children() as $view) {
        $this->components[$component][(string)$view['name']] = new stdClass;
        $this->components[$component][(string)$view['name']]->name = (string)$view['name'];
        $this->components[$component][(string)$view['name']]->list = (string)$view['list'];
        $this->components[$component][(string)$view['name']]->layout = (string)$view['layout'];
        $this->components[$component][(string)$view['name']]->key = (string)$view['key'];
        $this->components[$component][(string)$view['name']]->primarytable = $primarytable;
        $this->components[$component][(string)$view['name']]->default = ($view['default'] && strtoupper((string)$view['default']) != 'FALSE');
        $this->components[$component][(string)$view['name']]->native = (string)$view['native'];
        // We need to export and reimport the xml object else we have the complete xml document as form
        $this->components[$component][(string)$view['name']]->form = simplexml_load_string($view->form->asXML());
      }

		}
    return true;
	}

  /**
   * Returns one or all compoents
   * @param string $component
   * @return array All views or all components
   */
  public function getComponent($component=null) {
		if (!$component) {
			return $this->components;
		} else {
			return isset($this->components[$component]) ? $this->components[$component] : null;
		}
	}

  /**
   * Returns the Component properties by table
   * @param string $table
   * @return bool|array returns a view list
   */
  public function getComponentByTable($table) {
    if (isset($this->tables[$table]) && $this->components[$this->tables[$table]->component]) {
      return $this->components[$this->tables[$table]->component];
    }
    return false;
  }

  /**
   * Returns the table based on the component and the view
   * @param string $component
   * @param string $view
   * @return object
   */
  public function getTableByView($component, $view) {
    if (isset($this->components[$component])) {
      foreach($this->components[$component] as $jdview) {
        if ($jdview->name == $view or $jdview->list == $view) {
          return $this->tables[$this->components[$component][$jdview->name]->primarytable];
        }
      }
    }
    return false;
  }

  /**
 * Returns one or all tables
 * @param string $table
 * @return array All views or all components
 */
  public function getTable($table=null) {
    if (!$table) {
      return $this->tables;
    } else {
      return $this->tables[$table];
    }
  }

  /**
   * Returns one or all Views
   * @param string $view
   * @return array All views or all components
   */
  public function getView($component, $view) {
    if (isset($this->components[$component])) {
      foreach($this->components[$component] as $jdview) {
        if ($jdview->name == $view or $jdview->list == $view or ($jdview->default == 'true' and $view=='default')) {
          return $jdview;
        }
      }
    }
    return false;
  }

  /**
   * Returns one or all Views
   * @param string $view
   * @return array All views or all components
   */
  public function getViewByTask($component, $task) {
    if (isset($this->components[$component])) {
      foreach($this->components[$component] as $jdview) {
        if ($jdview->name == $task or $jdview->list == $task or ($jdview->default == 'true' and $task=='default')) {
          return $jdview;
        }
      }
    }
    return false;
  }

  /**
   * Returns all Fields used in this View
   * @param string $view
   * @return array All views or all components
   */
  public function getFieldsByView($component, $view) {
    if ($this->components[$component]) {
      foreach($this->components[$component] as $jdview) {
        if ($jdview->name == $view or $jdview->list == $view or ($jdview->default == 'true' and $view=='default')) {
          $fields = $jdview->form->xpath('//fieldset/field');
          $columns = array();
          foreach($fields as $field) {
            $column = new stdClass;

            foreach($field->attributes() as $k=>$attribute) {
              if ((string)$k == 'multifield') {
                $column->$k = ($attribute != '' ? explode(',', (string)$attribute) : false );
              } else {
                $column->$k  = (string)$attribute;
              }
            }

            $attrs = $field->xpath('ancestor::fields[@name]/@name');
            $groups = array_map('strval', $attrs ? $attrs : array());
            $group = implode('.', $groups);
            $column->group = $group;

            $columns[$column->name] = $column;
          }
          return $columns;
        }
      }
    }
    return false;
  }

  /**
   * Return the hash for a original or a translation based on the $translation used
   * @param string $component
   * @param string $view
   * @param object $translation
   * @return string md5 hash
   */

  public function getTranslationHashByView($component, $view, $translation) {
    $fields = $this->getFieldsByView($component, $view);

    $hashbase = '';
    // @todo may handle subfields change detection better
    foreach($fields as $field) {
      if (isset($field->multifield) && is_array($field->multifield)) {
        foreach($field->multifield as $multifield) {
          if (property_exists($translation, $multifield)) {
            $hashbase .= $translation->{$multifield};
          }
        }
      } else {
        if (property_exists($translation, $field->name)) {
          $hashbase .= $translation->{$field->name};
        }
      }
    }
    return md5($hashbase);
  }

  /**
   * Return the hash for a original
   * @param string $component
   * @param string $view
   * @param object $translation
   * @return string md5 hash
   */

  public function getTranslationHashById($component, $view, $id) {

    $base = $this->getOriginalById($component, $view, $id);
    if ($base) {
      return $this->getTranslationHashByView($component, $view, $base);
    }

    return false;
  }

  /**
   * Return the original by id
   * @param string $component
   * @param string $view
   * @param object $translation
   * @return string md5 hash
   */

  public function getOriginalById($component, $view, $id) {
    $fields = $this->getFieldsByView($component, $view);
    $table = $this->getTableByView($component, $view);
    $db = JFactory::getDbo();
    $query = $db->getQuery(true);

    foreach($fields as $field) {
      if (isset($field->multifield) && is_array($field->multifield)) {
        foreach($field->multifield as $multifield) {
          $query->select($db->quoteName($multifield));
        }
      } else {
        if ($field->group != '') {
          $query->select($db->quoteName($field->group));
        } else {
          $query->select($db->quoteName($field->name));
        }
      }
    }

    $query->from($table->name);
    $query->where($db->quoteName($table->key) . '=' . $db->quote($id));

    $db->setQuery($query);
    $result = $db->loadObject();

    return $result;
  }




  /**
	 * Debug function to start and stop trace
	 */
	public function trace($start=true, $id=1) {
		$trace = true;

		if ($start) {
			$this->traceId = $id;
			if (array_key_exists($id, $this->traceLog)) {
				$this->traceLog[$id] = array();
			} else {
				$trace = $this->traceLog[$id];
			}
		} else {
			$trace = $this->traceLog[$id];
			unset($this->traceLog[$id]);
			$this->traceId = false;
		}
		return $trace;
	}
	
	public function addLog($msg) {
		if ($this->traceId) {
			$this->traceLog[$this->traceId] = array(
				'message' => $msg
			);
		}
	}
	
	/**
	 * translate content
	 */
	public function translate(&$rows, $language, &$metadata, $resulttype='BOTH', $key=null) {

		if (!$this->ready) {
			return false;
		}

		/* If we have no result we exit here */
		if (empty($rows)) {
			return false;
		}

		$aliaskey = array();
		$newrows = array();

		//find primary key for each table alias
		foreach($metadata['tables'] as $tablealias=>$table) {
			foreach($table['fields'] as $fieldid=>$field) {
				if (isset($field->orgname)) {
					$fieldName = $field->orgname;
				} else {
					$fieldName = $field->name;
				}
				if (isset($this->tables[$metadata['tablealias'][$tablealias]]) && $fieldName == $this->tables[$metadata['tablealias'][$tablealias]]->key) {
					$aliaskey[$tablealias] = $fieldid;
				}
			}
		}
		
		if (is_numeric($key) && $key < 0) {
			$rows = array($rows);
		}

		//translate all rows
		if (is_array($rows) && count($rows)) {

			// PRELOAD CACHE
			foreach($metadata['tables'] as $tablealias=>$table) {
				if (array_key_exists($metadata['tablealias'][$tablealias], $this->tables)) {
					$ids = array();
					foreach($rows as $row) {
						if (!empty($row[$aliaskey[$tablealias]])) {
							$ids[] = $row[$aliaskey[$tablealias]];
						}
					}
					$this->getTranslation($metadata['tablealias'][$tablealias], $ids, $language);
				}
			}



			foreach($rows as $rowkey=>&$row) {
				$targetkey = $rowkey;
				foreach($metadata['tables'] as $tablealias=>$table) {
					if (array_key_exists($metadata['tablealias'][$tablealias], $this->tables)) {
						$translation = $this->getTranslation($metadata['tablealias'][$tablealias], $row[$aliaskey[$tablealias]], $language);
					} else {
						$translation = false;
					}

					/* Unset this row if it has state -3 (hide) */
					if ($translation) {
						if ($translation['__metadata']->state == -3) {
							unset($rows[$rowkey]);
							continue 2;
						}
					}
					
					foreach($table['fields'] as $fieldid=>$field) {
						if (isset($field->orgname)) {
							$fieldName = $field->orgname;
						} else {
							$fieldName = $field->name;
						}
						
						// Remove JD_MAGIC_KEY fields used to get primary key in table
						if (strpos($field->name, 'JD_MAGIC_KEY') !== false) {
							unset($row[$fieldid]);
							continue;
						}
						
						if ($translation) {
							if (array_key_exists($fieldName, $translation)) {
								//used for params
								// @todo rework with recrusive version
								if (is_array($translation[$fieldName])) {
									//works only for json atm
									$jsontmp = json_decode($row[$fieldid], true);
									foreach($translation[$fieldName] as $subkey=>$subvalue) {
										$jsontmp[$subkey] = $subvalue;
									}
									$row[$fieldid] = json_encode($jsontmp);
								} else {
									$row[$fieldid] = $translation[$fieldName];
								}
							}
						}
						
						if ($key) {
							if (is_numeric($key)) {
								if ($key==$fieldid) {
									$targetkey = $row[$fieldid];
								}
							} elseif (is_string($key)) {
								if ($key==$field->name) {
									$targetkey = $row[$fieldid];
								}
							}
						}
					}
					
					//second turn to copy table to correct key and fieldnames
					foreach($table['fields'] as $fieldid=>$field) {
						// Remove JD_MAGIC_KEY fields used to get primary key in table
						if (strpos($field->name, 'JD_MAGIC_KEY') !== false) {
							continue;
						}
						switch(strtoupper($resulttype)) {
							case 'BOTH':
									$newrows[$targetkey][$fieldid] = $row[$fieldid];
									$newrows[$targetkey][$field->name] = $row[$fieldid];
								break;
							case 'NUM':
									$newrows[$targetkey][$fieldid] = $row[$fieldid];
								break;
							case 'ASSOC':
									$newrows[$targetkey][$field->name] = $row[$fieldid];
								break;
						}
					}
				}
			}
		}
		unset($row);

		if (is_numeric($key) && $key < 0) {
			$rows = current($newrows);
		} else {
			$rows = $newrows;
		}
    return true;
	}
	
	public function getTranslation($reftable, $refids, $language) {


    if (array_key_exists($reftable, $this->cache)) {
      if (array_key_exists($language, $this->cache[$reftable])) {
				if (is_array($refids)) {
					foreach($refids as $key=>$refid) {
						if (array_key_exists($refid, $this->cache[$reftable][$language])) {
							unset($refids[$key]);
						} else {
							if ($this->tables[$reftable]->cache->fullfetch) {
								unset($refids[$key]);
							}
						}
					}
				} else {
					if (array_key_exists($refids, $this->cache[$reftable][$language])) {
						return $this->cache[$reftable][$language][$refids];
					} else {
						// We don't do a full fetch if we don't translate the current language (mainly used for #__menu)
						if ($this->tables[$reftable]->cache->fullfetch && $language == $this->getCurrentLanguage()->lang_id) {
							return false;
						}
					}
				}
			}
		}

		// check if we should precache some translations and return if no ids are given
		if (is_array($refids) && empty($refids)) {
			return false;
		}


		$db = JFactory::getDbo();
		//save current state
		$oldstate = $db->setTranslate(false);
		$oldquery = $db->getQuery();

		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__jd_store');
		$query->where('idLang='.$db->quote($language));
		$query->where('referenceTable='.$db->quote($reftable));

		// We don't do a full fetch if we don't translate the current language (mainly used for #__menu)
    if (!$this->tables[$reftable]->cache->fullfetch || $language != $this->getCurrentLanguage()->lang_id) {
			if (is_array($refids)) {
				$query->where('idReference in ('.implode(',', $refids).')');
			} else {
      	$query->where('idReference='.$db->quote($refids));
			}
    }

		$db->setQuery($query);
		$translations = $db->loadObjectList();

		//restore old state
		$db->setTranslate($oldstate);
		$db->setQuery($oldquery);

    if (is_array($translations)) {
      foreach($translations as $translation) {
        $tmp = jDictionTranslationHelper::decodeTranslation($translation->value);
        if (empty($tmp)) {
          continue;
        }
        $this->cache[$reftable][$language][$translation->idReference] = $tmp;
        unset($translation->value);

        // Workaround for articletext
        if ($reftable == '#__content') {
          // @todo convert to multifield support
          if (array_key_exists('articletext', $this->cache[$reftable][$language][$translation->idReference])) {
            $pattern = '#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i';
            $tagPos = preg_match($pattern, $this->cache[$reftable][$language][$translation->idReference]['articletext']);

            if ($tagPos == 0) {
              $this->cache[$reftable][$language][$translation->idReference]['introtext'] = $this->cache[$reftable][$language][$translation->idReference]['articletext'];
              $this->cache[$reftable][$language][$translation->idReference]['fulltext'] = '';
            } else {
              list($this->cache[$reftable][$language][$translation->idReference]['introtext'],
                $this->cache[$reftable][$language][$translation->idReference]['fulltext']
                ) = preg_split($pattern, $this->cache[$reftable][$language][$translation->idReference]['articletext'], 2);
            }

          }
        }
        // Workaround for articletext
        $this->cache[$reftable][$language][$translation->idReference]['__metadata'] = $translation;
      }
		}

		// Array requests are for caching only
		if (is_array($refids)) {
			foreach($refids as $refid) {
				if (!isset($this->cache[$reftable][$language][$refid])) {
					$this->cache[$reftable][$language][$refid] = false;
				}
			}
			return false;
		} else {
			if (!isset($this->cache[$reftable][$language][$refids])) {
				$this->cache[$reftable][$language][$refids] = false;
			}
			return $this->cache[$reftable][$language][$refids];
		}
	}

	/**
	 * returns the status of the translation
	 * @param string $reftable table name
	 * @param int $refid table primary key
	 * @param mixed $language language as lang_code or lang_id or object
   * @return int the status of the translation
	 */
	public function getTranslationStatus($reftable, $refid, $language=null) {
		$status = 0;
    $languages = $this->getLanguage($language);

		foreach($languages as $language) {
			$translation = $this->getTranslation($reftable, $refid, $language->lang_id);
			if ($translation) {
				if ($translation['__metadata']->state == 2) {
					$status = JDICTION_TRANSLATION_STATUS_OLD;
				} else {
					$status = JDICTION_TRANSLATION_STATUS_FULL;
				}
			} else {
				$status = JDICTION_TRANSLATION_STATUS_NONE;
			}
		}
		return $status;
	}

  /**
   * Normalize language code
   * @param null|string|int|object $language The language as language Tag or ID or Object
   * @return array all $languages or just the given one
   */

  public function getLanguage($language=null) {

    $languages = $this->getLanguages(true);

    if ($language) {
      if (is_object($language)) {
        if (property_exists($language, 'lang_id')) {
          $language = $language->lang_id;
        } else {
          $language = $language->lang_code;
        }
      }
      if (is_numeric($language)) {
        foreach($languages as $k=>$lang) {
          if ($lang->lang_id !== $language) {
            unset($languages[$k]);
          }
        }
      } else {
        foreach($languages as $k=>$lang) {
          if ($lang->lang_code !== $language) {
            unset($languages[$k]);
          }
        }
      }
    }

    return $languages;
  }
  /**
   * Returns all languages except the primary language
   *
   * @param bool $all If true include primary language
   * @return array languages
   */
  public function getLanguages($all=false) {
		$languages = JLanguageHelper::getLanguages();
		if (!$all) {
      $defaultLanguage = JComponentHelper::getParams('com_languages')->get('site', 'en-GB');
			foreach($languages as $k=>$language) {
				if ($language->lang_code == $defaultLanguage) {
					unset($languages[$k]);
				}
			}
		}
		
		return $languages;
	}

  /**
   * @return mixed the current Language
   */
  public function getCurrentLanguage() {
    $lang = $this->getLanguage(JFactory::getLanguage()->getTag());
    return current($lang);
  }
	
	/**
	 * Adds the translation toolbar button to the toolbar based on the
	 * given parameters.
	 *
	 */
	public function addToolbar() {
		//check if we are in backend
		$app = JFactory::getApplication();
		if (!$app->isAdmin()) {
			return;
		}

    $input = JFactory::getApplication()->input;

    $option = $input->get('option', false, 'cmd');
		$view 	= $input->get('view', false, 'cmd');
    $task = $input->get('task', false, 'cmd');
		$layout = $input->get('layout', 'default', 'string');

		if (!$option || (!$view && !$task) || !$layout) {
			return;
		}

    if ($view || !$task) {
      if (!$view) {
        $view = 'default';
      }
      $viewobj = $this->getView($option, $view);
    } elseif ($task) {
      $viewobj = $this->getViewByTask($option, $task);
    } else {
      return;
    }

    $view = $viewobj->name;

    $table = $this->getTableByView($option, $view);

    if (is_object($viewobj) && $viewobj->key != '') {
      $keys = explode(',',$viewobj->key);
    } elseif (is_object($table) && $table->key != '') {
      $keys = explode(',',$table->key);
    } else {
      return false;
    }

    foreach($keys as $key) {
      $id = (array)$input->get($key, array(), 'array');
      if (!empty($id)) {
        break;
      }
    }

    if (empty($id)) {
      return;
    }

    $id = (int) current($id);

		//Load ToolBar
		$bar = JToolBar::getInstance('toolbar');

    // @deprecated used for Joomla 2.5

    if (version_compare(JVERSION, '3.0', 'ge')) {
      $bar->addButtonPath(JPATH_LIBRARIES.'/jdiction/toolbar/button/');
      $buttontype = 'itrPopup';
      $width = '95%';
      $height = '95%';
    } else {
      $buttontype = 'Popup';
      $width = 'window.getSize().x-70';
      $height = 'window.getSize().y-70';
    }

		//Add Stylesheet for button icons
		JHTML::_('stylesheet','administrator/components/com_jdiction/assets/style.css', array(), false);

    if ($this->components[$option][$view]->layout == $layout) {
      if ($id == 0) {
        $bar->prependButton('Link', 'jdiction-component', 'LIB_JDICTION_TOOLBAR_TRANSLATE', "javascript:alert('".JText::_('LIB_JDICTION_SAVE_FIRST', true)."');");
      } else {
        $bar->prependButton($buttontype, 'jdiction-component', 'LIB_JDICTION_TOOLBAR_TRANSLATE', 'index.php?option=com_jdiction&view=translation&tmpl=component&layout=popup&jd_option='.$option.'&jd_view='.$view.'&jd_layout='.$layout.'&jd_id='.$id, $width, $height);
      }
    }
	}
	
	public function getTranslationLink($component, $view, $layout, $id, $popup=true) {
		
		$link = 'index.php?option=com_jdiction&view=translation';
		if ($popup) {
			$link .= '&tmpl=component&layout=popup';
		} else {
			$link .= '&layout=edit';
		}
		$link .= '&jd_option='.$component.'&jd_view='.$view.'&jd_layout='.$layout.'&jd_id='.$id;
		
		return $link;
	}
}