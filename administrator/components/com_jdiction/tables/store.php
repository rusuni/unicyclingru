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

jimport( 'joomla.database.database.table' );

/**
 * Translation Table class
 *
 * @package jDiction
 */
class jDictionTableStore extends jTable {
	
	protected $_tabletitlefield = "title";

  /**
   * Object constructor to set table and key fields.  In most cases this will
   * be overridden by child classes to explicitly set the table and key fields
   * for a particular database table.
   *
   * @param   JDatabaseDriver  $db     JDatabaseDriver object.
   */
	public function __construct($db) {
		parent::__construct('#__jd_store', 'idJdStore', $db);
	}
	
}
