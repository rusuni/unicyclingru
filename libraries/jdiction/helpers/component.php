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

abstract class componentHelperjDiction {

  var $originalLanguage;

  var $originalTranslateStatus;

  var $targetLanguage;


  public function startTranslation() {
    $db = JFactory::getDbo();

    //replace current language with target language
    $this->originalLanguage = $db->setLanguage($this->targetLanguage->lang_code);
    $this->originalTranslateStatus = $db->setTranslate(true);

  }

  public function stopTranslation() {
    $db = JFactory::getDbo();

    //restore current language
    $db->setTranslate($this->originalTranslateStatus);
    $db->setLanguage($this->originalLanguage);
  }

  /**
   *
   *
   */
  public function getRoute($path, $input, $query, $menuItem, $target) {

    return $path;
  }
}
