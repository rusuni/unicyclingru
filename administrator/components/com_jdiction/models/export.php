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

jimport('joomla.application.component.modelform');

/**
 * Methods supporting a list of Translation records.
 *
 * @package		jDiction
 */
class jDictionModelExport extends JModelForm {
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
		// Get the form.
		$form = $this->loadForm('com_jdiction.download', 'download', array('control' => 'jform', 'load_data' => $loadData));
								
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

  public function export() {
    // Prepare variables
    $jdiction = jDiction::getInstance();
    $components = $jdiction->getComponent();
    $jform = JFactory::getApplication()->input->get('jform', null, 'array');

    if (isset($jform['deduplicate'])) {
      $deduplicate = $jform['deduplicate'];
    } else {
      $deduplicate = true;
    }

    if (isset($jform['filetype'])) {
      $filetype = $jform['filetype'];
    } else {
      $filetype = 'CSV';
    }

    if (isset($jform['tables'])) {
      $exporttables = $jform['tables'];
    }

    if (isset($jform['language'])) {
      $sourcelanguage = $jform['language'];
    } else {
      $sourcelanguage = JComponentHelper::getParams('com_languages')->get('site', 'en-GB');
    }

    if (isset($jform['targetlanguage'])) {
      $targetlanguage = $jform['targetlanguage'];
    } else {
      foreach($jdiction->getLanguages(true) as $k=>$lang) {
        if ($lang->lang_code != $sourcelanguage) {
          $targetlanguage = $lang->lang_code;
          break;
        }
      }
    }

    // Do we export the native Language?
    $native = ($sourcelanguage == JComponentHelper::getParams('com_languages')->get('site', 'en-GB'));

    $db = JFactory::getDbo();
    $query = $db->getQuery(true);
    $header = array();
    $data = array();

    // Prepare columns
    foreach($components as $com=>$views) {
      $header[$com] = array();

      foreach($views as $view) {
        if (!is_null($exporttables) && !in_array($com.'.'.$view->name, $exporttables)) {
          continue;
        }

        $query->clear();

        $table = $jdiction->getTable($view->primarytable);
        $query->from($table->name);
        $query->select($db->quoteName($table->key).' as '.$db->quote('JDKEY'));

        if ($table->exportfilter) {
          $query->where((string)$table->exportfilter);
        }

        $header[$com][$view->name] = array();
        $layout = $view->layout;
        unset($form);
        $form = $view->form;

        $fields = $form->xpath('//form/fieldset/field');
        foreach($fields as $field) {
          if (($field->attributes()->export) && (strtoupper((string)$field->attributes()->export) == "FALSE")) {
            continue;
          }
          $name = (string)$field->attributes()->name;
          if (($table->name == '#__content') && ($name == 'articletext')) {
            $name = 'introtext';
            $header[$com.'.'.$view->name.'.'.$layout.'.'.$name] = $name;
            $query->select($db->quoteName($name).' as '.$db->quote($com.'.'.$view->name.'.'.$layout.'.'.$name));
            $name = 'fulltext';
          }
          $header[$com.'.'.$view->name.'.'.$layout.'.'.$name] = $name;
          $query->select($db->quoteName($name).' as '.$db->quote($com.'.'.$view->name.'.'.$layout.'.'.$name));
        }
        $fields = $form->xpath('//fields');

        foreach($fields as $field) {
          $name = (string)$field->attributes()->name;
          $subfields = $field->xpath('//fieldset/field');
          $query->select($db->quoteName($name).' as '.$db->quote($com.'.'.$view->name.'.'.$layout.'.'.$name));
          foreach($subfields as $subfield) {
            $subname = (string)$subfield->attributes()->name;
            $header[$com.'.'.$view->name.'.'.$layout.'.'.$name][$subname] = $subname;
          }
        }

        if (!$native) {
          $db->setTranslate(true);
          $oldlangauge = $db->getLanguage();
          $db->setLanguage($sourcelanguage);
        }
        $db->setQuery($query);
        $tmpdata = (array)$db->loadAssocList();
        if (!$native) {
          $db->setTranslate(false);
          $db->setLanguage($oldlangauge);
        }

        // lookup sourcehash
        foreach($tmpdata as $k=>$row) {
          $sourcehash = $jdiction->getTranslationHashById($com, $view->name, $row['JDKEY']);
          $tmpdata[$k]['JD_SOURCEHASH'] = $sourcehash;
        }
        $data = array_merge((array) $data, $tmpdata);
      }
    }

    switch (strtoupper($filetype)) {
      case 'CSV':
        $this->exportCSV($data, $header, $deduplicate, $sourcelanguage, $targetlanguage);
        break;
      case 'XLIFF':
        $this->exportXLIFF($data, $header, $deduplicate, $sourcelanguage, $targetlanguage);
        break;
    }
  }

  public function exportCSV($data, $header, $deduplicate, $sourcelanguage, $targetlanguage) {

    // Allocate 12 MB for content and 12 MB for dedublication
    $file = fopen('php://temp/maxmemory:'. (12*1024*1024), 'w+');
    $defile = fopen('php://temp/maxmemory:'. (12*1024*1024), 'w+');
    jDictionTranslationHelper::fputcsv($file, array('Key', 'Context', 'Original ('.$sourcelanguage.')', 'Target ('.$targetlanguage.')'),';','"','"');

    $dedup = array();
    foreach ($data as $row) {
      $jdkey = $row['JDKEY'];
      $sourcehash = $row['JD_SOURCEHASH'];
      $sourcehash_key = '';
      foreach ($row as $key=>$value) {
        if ($key == 'JDKEY' || $key == 'JD_SOURCEHASH') {
          continue;
        }
        if (is_array($header[$key])) {
          $tmp = json_decode($value, true);
          foreach($header[$key] as $subkey=>$subvalue) {
            if (trim($tmp[$subkey]) == '') {
              continue;
            }
            if ($deduplicate && array_key_exists((string)$tmp[$subkey], $dedup)) {
              jDictionTranslationHelper::fputcsv($defile, array($jdkey, $key.'.'.$subkey, $dedup[(string)$tmp[$subkey]]),';','"','"');
            } else {
              $dedup[(string)$tmp[$subkey]] = "REF:".$jdkey.'.'.$key.'.'.$subkey;
              jDictionTranslationHelper::fputcsv($file, array($jdkey, $key.'.'.$subkey, $tmp[$subkey]),';','"','"');
            }
          }
        } else {
          if (trim($value) == '') {
            continue;
          }
          if ($deduplicate && array_key_exists((string)$value, $dedup)) {
            jDictionTranslationHelper::fputcsv($defile, array($jdkey, $key, $dedup[(string)$value]),';','"','"');
          } else {
            $dedup[(string)$value] = "REF:".$jdkey.'.'.$key;
            jDictionTranslationHelper::fputcsv($file, array($jdkey, $key, $value),';','"','"');
          }
        }
        $sourcehash_key = substr($key, 0, strrpos($key, '.')).'.JD_SOURCEHASH';
      }

      if (!empty($sourcehash_key)) {
        jDictionTranslationHelper::fputcsv($defile, array($jdkey, $sourcehash_key, $sourcehash),';','"','"');
      }
    }

    // Finish output and send it to the browser as Download
    rewind($file);
    rewind($defile);
    $output = stream_get_contents($file);
    $output .= stream_get_contents($defile);
    fclose($file);
    fclose($defile);
    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=\"jdiction-".$sourcelanguage.".csv\"");
    echo $output;
    Jexit();
  }
  public function exportXLIFF($data, $header, $deduplicate, $sourcelanguage, $targetlanguage) {

    $jd = jDiction::getInstance();
    $jdversion = $jd->getVersion();

    $output = array();
    $output[] = "<xliff version='1.2' xmlns='urn:oasis:names:tc:xliff:document:1.2'>";
    $deoutput = array();

    $dedup = array();
    foreach ($data as $row) {
      $jdkey = $row['JDKEY'];
      $sourcehash = $row['JD_SOURCEHASH'];
      $output[] = "<file original='".$sourcehash."' source-language='".$sourcelanguage."' target-language='".$targetlanguage."' datatype='htmlbody' product-name='jDiction' product-version='".$jdversion."'>";
      $output[] = "<body>";
      foreach ($row as $key=>$value) {
        if ($key == 'JDKEY' || $key == 'JD_SOURCEHASH') {
          continue;
        }
        if (is_array($header[$key])) {
          $tmp = json_decode($value, true);
          foreach($header[$key] as $subkey=>$subvalue) {
            if (trim($tmp[$subkey]) == '') {
              continue;
            }
            if ($deduplicate && array_key_exists((string)$tmp[$subkey], $dedup)) {
              $deoutput[] = "<trans-unit id='".$jdkey.'.'.$key.'.'.$subkey."' translate='no' approved='yes' extradata='".$sourcehash."'>";
              $deoutput[] = "<source><![CDATA[".$dedup[(string)$tmp[$subkey]]."]]></source>";
              $deoutput[] = "<target state='translated' equiv-trans='yes' state-qualifier='id-match'>REF:".$dedup[(string)$tmp[$subkey]]."</target>";
              $deoutput[] = "</trans-unit>";
            } else {
              $dedup[(string)$tmp[$subkey]] = $jdkey.'.'.$key.'.'.$subkey;
              $output[] = "<trans-unit id='".$jdkey.'.'.$key.'.'.$subkey."'>";
              $output[] = "<source><![CDATA[".$tmp[$subkey]."]]></source>";
              $output[] = "<target state='needs-translation'><![CDATA[".$tmp[$subkey]."]]></target>";
              $output[] = "</trans-unit>";
            }
          }
        } else {
          if (trim($value) == '') {
            continue;
          }
          if ($deduplicate && array_key_exists((string)$value, $dedup)) {
            $deoutput[] = "<trans-unit id='".$jdkey.'.'.$key."' translate='no' approved='yes' extradata='".$sourcehash."'>";
            $deoutput[] = "<source><![CDATA[".$dedup[(string)$value]."]]></source>";
            $deoutput[] = "<target state='translated' equiv-trans='yes' state-qualifier='id-match'>REF:".$dedup[(string)$value]."</target>";
            $deoutput[] = "</trans-unit>";
          } else {
            $dedup[(string)$value] = $jdkey.'.'.$key;
            $output[] = "<trans-unit id='".$jdkey.'.'.$key."'>";
            $output[] = "<source><![CDATA[".$value."]]></source>";
            $output[] = "<target state='needs-translation'><![CDATA[".$value."]]></target>";
            $output[] = "</trans-unit>";
          }
        }
      }
      $output[] = "</body>";
      $output[] = "</file>";
    }

    if (!empty($deoutput)) {
      $output[] = "<file original='references' source-language='".$sourcelanguage."' target-language='".$targetlanguage."' datatype='htmlbody' product-name='jDiction' product-version='".$jdversion."'>";
      $output[] = "<body>";
      $output = array_merge($output, $deoutput);
      $output[] = "</body>";
      $output[] = "</file>";
    }

    $output[] = "</xliff>";
    //printpredie($output);
    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=\"jdiction-".$sourcelanguage.".xlf\"");
    echo implode(chr(10),$output);
    Jexit();
  }

} 
