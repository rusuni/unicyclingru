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

abstract class jDictionTranslationHelper {
	/**
	 * decode the Translation to a array
	 * @param string $translation The Translation
	 * @return array The decoded Translation
	 */
	public static function decodeTranslation($translation) {
		//try to load the translation as xml string
		$result = simplexml_load_string($translation, 'jDictionXML', LIBXML_NOCDATA);
		if ($result) {
			// @TODO change back to Joomla Array Helper function if empty xml elements are handled correctly
			//$result = JArrayHelper::fromObject($result);
			$result = self::_fromObject($result, true, null);
			if ($result) {
				return $result;
			}
		}
		
		$result = @unserialize($translation);
		if ($result === FALSE) {
			return false;
		}
		
		return $result;
	}
	
	/**
	 * encode the Translation to a string
	 * @param array $translation The Translation
	 * @return string The encoded Translation
	 */
	public static function encodeTranslation($translation) {
		if (JDICTION_TRANSLATION_USEXML) {
			$xml = new jDictionXML("<jDiction/>");
      self::arrayToXML($translation, $xml);
			$result = $xml->asXML();
		} else {
			$result = serialize($translation);
		}
		return $result;
	}
	
	/**
	 * convert a array to xml
	 * @param array $array The array to convert
	 * @param SimpleXMLElement $xml An SimpleXMLElement as container for the array
	 */
	public static function arrayToXml($array, &$xml) {
    foreach($array as $key => $value) {
      if (is_array($value)) {
        $xml->$key = null;
        $xml->$key->addAttribute('type', 'json');
        $xml->$key->addCData(json_encode($value));
        //json_encode($value, JSON_FORCE_OBJECT); // not supported by php < 5.3
        /*
	      if (!is_numeric($key)){
	        $subnode = $xml->addChild($key);
	        self::arrayToXml($value, $subnode);
	      } else {
	      	self::arrayToXml($value, $xml);
	      }
        */
      } else {
        // This way seams to be better then just use addChild
        // https://bugs.php.net/bug.php?id=44478
        //$xml->$key = $value; // we use cdata now

        //problem with loading cdata
        $xml->$key = null;
        $xml->$key->addCData($value);
      }
    }
	}
	
	protected static function _fromObject($item, $recurse, $regex) {

    if (is_object($item)) {
			//+ Modification to JArrayHelper::_fromObject
			if (is_a($item, "SimpleXMLElement")) {
				if (count($item->children()) == 0) {
					return "";
				}
			}
			//- Modification to JArrayHelper::_fromObject
			$result = array();

			foreach (get_object_vars($item) as $k => $v) {
				if (!$regex || preg_match($regex, $k)) {
          //+ Modification to JArrayHelper::_fromObject
          if ($item->{$k}['type'] == 'json') {
            $result[$k] = json_decode($v, true);
          //- Modification to JArrayHelper::_fromObject
          } else if ($recurse) {
						$result[$k] = self::_fromObject($v, $recurse, $regex);
					} else {
						$result[$k] = $v;
					}
				}
			}
		} elseif (is_array($item)) {
			$result = array();
			foreach ($item as $k => $v) {
				$result[$k] = self::_fromObject($v, $recurse, $regex);
			}
		} else {
			$result = $item;
		}
		return $result;
	}
	
	/**
	 * merge 2 or more arrays with overriding numric keys
	 */
	public static function array_merge_recursive() {
		$arrays = func_get_args();
		$base = array_shift($arrays);
		
		foreach ($arrays as $array) {
			reset($base);
			while (list($key, $value) = @each($array)) {
				if (is_array($value) && @is_array($base[$key])) {
					$base[$key] = self::array_merge_recursive($base[$key], $value);
				} else {
					$base[$key] = $value;
				}
			}
		}
		
		return $base;
  }

	/**
	 * workaround for bug: https://bugs.php.net/bug.php?id=43225
	 * function source: http://php.itronic.at/manual/de/function.fputcsv.php#108463
	 * the default for $escape is '\\' in php 
	 */
	public static function fputcsv($handle, $fields, $delimiter = ',', $enclosure = '"', $escape = '"') {
   $first = 1;
   foreach ($fields as $field) {
     if ($first == 0) fwrite($handle, $delimiter);

     $f = str_replace($enclosure, $enclosure.$enclosure, $field);
     if ($enclosure != $escape) {
       $f = str_replace($escape.$enclosure, $escape, $f);
     }
     if (strpbrk($f, " \t\n\r".$delimiter.$enclosure.$escape) || strchr($f, "\000")) {
       fwrite($handle, $enclosure.$f.$enclosure);
     } else {
       fwrite($handle, $f);
     }

     $first = 0;
   }
   fwrite($handle, "\n");
 }
	
}

/**
 * Class jDictionXML
 */
class jDictionXML extends SimpleXMLElement {
  public function addCData($cdata_text) {
    $node = dom_import_simplexml($this);
    $no   = $node->ownerDocument;
    $node->appendChild($no->createCDATASection($cdata_text));
  }
}