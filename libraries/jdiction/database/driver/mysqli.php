<?php
/**
 * jDiction database class
 *
 * extends the current joomla database class with translation functions
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

require_once(JPATH_LIBRARIES.'/joomla/database/driver/mysqli.php');
/**
 * MySQLi database driver
 *
 * @package		jDiction
 */
class JdDatabaseAdapter extends JDatabaseDriverMySQLi   {

	/**
	 * returns the field count of the current query
	 */
	public function getFieldCount() {
		if ($this->cursor) {
			return $this->cursor->field_count;
		}
		return 0;
	}	
	
	/**
	 * load the field metadata from the current query
	 * @return object
	 */
	public function getFieldMetaData(){
		return $this->cursor->fetch_field();
	}

  /**
   * Workaround for Joomla older then 2.5.5. Starting with 2.5.5 we use execute() to execute queries
   *
   * @return  mixed  A database cursor resource on success, boolean false on failure.
   *
   * @since   11.1
   * @throws  JDatabaseException
   */
  public function query() {
    return $this->execute();
  }

  /**
   * Workaround for Joomla older then 2.5.5. Starting with 2.5.5 we use execute() to execute queries
   *
   * @return  mixed  A database cursor resource on success, boolean false on failure.
   *
   * @since   11.1
   * @throws  JDatabaseException
   */
  public function execute() {
    if ( version_compare( JVERSION, '2.5.4', '>' ) == 1) {
      return parent::execute();
    } else {
      return parent::query();
    }
  }
	
	protected function collectTranslationTables() {
		if (!($this->cursor instanceof MySQLi_Result)) {
			return false;
		}

		/* check if this is a select query, we can't handle any other query type atm */
		if (strpos(strtoupper($this->sql),"SELECT") === FALSE) {
			return false;
		}

		// get column metadata
		$fields = $this->cursor->fetch_fields();
		if (!count($fields)) {
			return false;
		}
		
		$this->jd_metadata = array();
		
		foreach($fields as $fieldid=>$field) {
			//detect table alias
			$tablealias = $field->table;
			if (isset($field->orgtable)) {
				$table = $field->orgtable;
			} else {
				$table = $field->table;
			}
			if (isset($this->tablePrefix) && strlen($this->tablePrefix)>0 && strpos($table,$this->tablePrefix)===0) {
        $table = str_replace($this->tablePrefix, '#__', $table);
			}
			$this->jd_metadata['tablealias'][$tablealias] = $table;
			$this->jd_metadata['tables'][$tablealias]['fields'][$fieldid] = $field;
		}
	}
	
	public function getParser() {
    static $parser;

    if (!$parser) {
      require_once dirname(__DIR__).'/parser/mysql-parser.php';
      $parser = new \PHPSQLParser\PHPSQLParser();
    }
		return $parser;
	}

	public function getCreator() {
    static $parser;

    if (!$parser) {
      require_once dirname(__DIR__).'/parser/mysql-creator.php';
      $parser = new \PHPSQLParser\PHPSQLCreator();
    }
		return $parser;
	}

  /** Add some caching to static queries */

  /**
   * Retrieves field information about a given table.
   *
   * @param   string   $table     The name of the database table.
   * @param   boolean  $typeOnly  True to only return field types.
   *
   * @return  array  An array of fields for the database table.
   *
   * @since   12.2
   * @throws  RuntimeException
   */
  public function getTableColumns($table, $typeOnly = true)
  {
    static $cache;

    if (isset($cache[$table][$typeOnly])) {
      return $cache[$table][$typeOnly];
    }
    $this->connect();

    $result = array();

    // Set the query to get the table fields statement.
    $this->setQuery('SHOW FULL COLUMNS FROM ' . $this->quoteName($this->escape($table)));
    $fields = $this->loadObjectList();

    // If we only want the type as the value add just that to the list.
    if ($typeOnly)
    {
      foreach ($fields as $field)
      {
        $result[$field->Field] = preg_replace("/[(0-9)]/", '', $field->Type);
      }
    }
    // If we want the whole field data object add that to the list.
    else
    {
      foreach ($fields as $field)
      {
        $result[$field->Field] = $field;
      }
    }
    $cache[$table][$typeOnly] = $result;

    return $result;
  }
  /**
   * Method to get an array of all tables in the database.
   *
   * @return  array  An array of all the tables in the database.
   *
   * @since   12.2
   * @throws  RuntimeException
   */
  public function getTableList()
  {
    static $tables;
    if (!$tables) {
      $this->connect();

      // Set the query to get the tables statement.
      $this->setQuery('SHOW TABLES');
      $tables = $this->loadColumn();
    }

    return $tables;
  }


}