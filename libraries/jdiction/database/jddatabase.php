<?php
/**
 * jDiction database class
 *
 * extends the current joomla database class with translation functions
 *
 * @package jDiction
 * @link http://joomla.itronic.at
 * @copyright  Copyright (C) 2011 ITronic Harald Leithner. All rights reserved.
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

jimport('jdiction.jdiction');

/**
 * Database connector class
 *
 * @package  jDiction
 */
class JdDatabase extends JdDatabaseAdapter {

	/** @var array of tables with translations */
	//protected $jd_tables = Array();

	/** @var object JDispatcher with jdiction plugins */
	protected $jd_dispatcher;

	/** @var bool should we translate */
	protected $jd_active = true;

	/** @var int the language we should translate to */
	protected $jd_language;

	/** @var array the fields of a query to translate */
	protected $jd_metadata;

	/** @var object the jDiction object */
	protected $jd;


	/**
	 * Database object constructor
	 *
	 * @param  array $options List of options used to configure the connection
	 * @since  1.5
	 */
	public function __construct($options) {

		$this->jd = jDiction::getInstance();

		//this is not possible because we get initialised in getApplication
		//$app = JFactory::getApplication();
		//$clientid = $app->getClientid();

		//workaround to detect admin
		if (strtolower(substr(JPATH_BASE, -13)) == 'administrator') {
			$clientid = 1;
		} else {
			$clientid = 0;
		}
		if ($clientid == 1) {
			$this->jd_active = false;
		}

		parent::__construct($options);
	}


	public function __sleep() {
		// Workaround for clone in JDatabasequery clone function but maybe a good idea anyway
		$safe = array_keys(get_object_vars($this));
		/* does not work as expected
		if (method_exists(parent, '__sleep')) {
			$parentsafe = parent::__sleep();
			$safe = $safe + $parentsafe;
		};
		*/
		$safe = array_flip($safe);
		unset($safe['jd']);
		$safe = array_flip($safe);

		return $safe;
	}

	public function __wakeup() {
		// Workaround for clone in JDatabasequery clone function but maybe a good idea anyway

		/* does not work as expected
		if (method_exists(parent, '__wakeup')) {
			parent::__wakeup();
		};
		*/
		$this->jd = jDiction::getInstance();

	}

	/**
	 * Get the number of rows returned by the most recent query
	 *
	 * @param  object $cur Database resource
	 * @return  int    The number of rows
	 */
	public function getNumRows($cur = null) {
		$count = parent::getNumRows($cur);
		if (!$this->translateQuery()) {
			return $count;
		}
		// atm we only make 1to1 translation without the possibility to deactivte a item for language
		// this possible with joomla it self by set the item to a specific language
		return $count;
	}

	/**
	 * This method loads the first field of the first row returned by the query.
	 *
	 * @return  mixed  The value returned in the query or null if the query failed.
	 */
	public function loadResult() {
		if (!$this->translateQuery()) {
			$result = parent::loadResult();
			return $result;
		}

		$result = $this->loadTranslation(-1, 'NUM');
		if (!is_null($result)) {
			return $result[0];
		} else {
			return null;
		}

	}

	/**
	 * Method to get an array of values from the <var>$offset</var> field in each row of the result set from
	 * the database query.
	 *
	 * @param   integer $offset The row offset to use to build the result array.
	 *
	 * @return  mixed    The return value or null if the query failed.
	 *
	 * @since   11.1
	 * @throws  JDatabaseException
	 */
	public function loadColumn($offset = 0) {
		if (!$this->translateQuery()) {
			$result = parent::loadColumn($offset);
			return $result;
		}

		// Initialise variables.
		$array = array();

		$result = $this->loadTranslation(null, 'NUM');
		foreach ($result as &$row) {
			$array[] = $row[$offset];
		}

		return $array;
	}

	/**
	 * Fetch a result row as an associative array
	 *
	 * @return array  first result row as associatve array
	 */
	public function loadAssoc() {
		if (!$this->translateQuery()) {
			$result = parent::loadAssoc();
			return $result;
		}

		$result = $this->loadTranslation(-1, 'ASSOC');

		return $result;
	}

	/**
	 * Load a assoc list of database rows
	 *
	 * @param  string $key The field name of a primary key
	 * @param  string $column An optional column name. Instead of the whole row, only this column value will be in the return array.
	 * @return  array  If <var>key</var> is empty as sequential list of returned records.
	 */
	public function loadAssocList($key = null, $column = null) {
		if (!$this->translateQuery()) {
			$result = parent::loadAssocList($key, $column);
			return $result;
		}

		$result = $this->loadTranslation($key, 'ASSOC');

		if ($column) {
			$array = array();
			foreach ($result as $k => $row) {
				$array[$k] = $row[$column];
			}
			$result = $array;
		}

		return $result;
	}

	/**
	 * This global function loads the first row of a query into an object
	 *
	 * @param  string $className The name of the class to return (stdClass by default).
	 *
	 * @return  object
	 */
	public function loadObject($className = 'stdClass') {
		if (!$this->translateQuery()) {
			$result = parent::loadObject();
			return $result;
		}

		$result = $this->loadTranslation(-1, 'ASSOC');
		$result = JArrayHelper::toObject($result, $className);
		return $result;

	}


	/**
	 * Load a list of database objects
	 *
	 * If <var>key</var> is not empty then the returned array is indexed by the value
	 * the database key.  Returns <var>null</var> if the query fails.
	 *
	 * @param  string $key The field name of a primary key
	 * @param  string $className The name of the class to return (stdClass by default).
	 *
	 * @return  array  If <var>key</var> is empty as sequential list of returned records.
	 */
	public function loadObjectList($key = null, $className = 'stdClass') {
		if (!$this->translateQuery()) {
			$result = parent::loadObjectList($key);
			return $result;
		}

		$result = $this->loadTranslation($key, 'ASSOC');

		if (is_array($result)) {
			$array = array();
			foreach ($result as $k => $row) {
				$array[$k] = JArrayHelper::toObject($row, $className);
			}
		} else {
			return $result;
		}

		return $array;
	}


	/**
	 * Load the first row returned by the query
	 *
	 * @return  mixed  The first row of the query.
	 */
	public function loadRow() {
		$result = parent::loadRow();
		if (!$this->translateQuery()) {
			return $result;
		}

		$result = $this->loadTranslation(-1, 'NUM');
		return $result;
	}

	/**
	 * Load a list of database rows (numeric column indexing)
	 *
	 * If <var>key</var> is not empty then the returned array is indexed by the value
	 * the database key.  Returns <var>null</var> if the query fails.
	 *
	 * @param  string $key The field name of a primary key
	 * @return  array
	 */
	public function loadRowList($key = null) {
		if (!$this->translateQuery()) {
			$result = parent::loadRowList($key);
			return $result;
		}

		return $this->loadTranslation($key, 'NUM');
	}

	/**
	 * Load the next row returned by the query.
	 *
	 * @return  mixed  The result of the query as an array, false if there are no more rows, or null on an error.
	 *
	 * @since  1.6.0
	 */
	public function loadNextRow() {
		$result = parent::loadNextRow();

		if (!$this->translateQuery()) {
			return $result;
		}

		if ($result === false) {
			return false;
		}
		$result = array($result);
		$this->jd->translate($result, $this->getLanguage(), $this->jd_metadata, 'NUM');

		return $result[0];
	}

	/**
	 * Load the next row returned by the query.
	 *
	 * @param  string $className The name of the class to return (stdClass by default).
	 *
	 * @return  mixed  The result of the query as an object, false if there are no more rows, or null on an error.
	 *
	 * @since  1.6.0
	 */
	public function loadNextObject($className = 'stdClass') {
		if (!$this->translateQuery()) {
			return parent::loadNextObject($className);
		}

		$result = $this->loadNextRow();
		if ($result === false) {
			return false;
		}

		$result = JArrayHelper::toObject($result, $className);

		return $result;
	}

	/**
	 * Load a list of database rows and translate it
	 *
	 * If <var>key</var> is not empty then the returned array is indexed by the value
	 * the database key.  Returns <var>null</var> if the query fails.
	 *
	 * @param  string|int $key The field name or the field positon of a primary key or -1
	 * for a single row
	 * @param string $resulttype The result type of the array (ASSOC, NUM, BOTH)
	 * @return  array
	 */
	public function loadTranslation($key = null, $resulttype = 'BOTH') {
		if (is_numeric($key) && $key < 0) {
			$result = parent::loadRow();
		} else {
			//we handle $key at jDiction::translate
			$result = parent::loadRowList(null);
		}

		if (!$this->translateQuery()) {
			//this will horrible fail because the calling function maybe aspact another result so we JError here
			JError::raiseError(500, 'Fatal translation error, we can not translation now');
			return $result;
		}

		$this->jd->translate($result, $this->getLanguage(), $this->jd_metadata, $resulttype, $key);

		return $result;
	}

	/**
	 * Returns if we should translate now or not
	 * @return bool translate this query
	 */
	protected function translateQuery() {
		$translate = false;
		if ($this->jd_active and $this->jd->getStatus()
			/* We only translate SELECT queries atm */ and (strtoupper(substr(ltrim($this->sql), 0, 6)) == 'SELECT')
			/* We don't translate jDiction tables */ and (strpos($this->sql, '#__jd_') === false)
			/* We don't translate queries with / * NOTRANSLATION * / in it */ and (strpos($this->sql, '/* NOTRANSLATION */') === false)
		) {

			//search for a supported table
			$tables = $this->jd->getTable();
			foreach ($tables as $table) {
				if (strpos($this->sql, $table->name) !== false) {
					$translate = true;
				}
			}

			if ($this->sql instanceof JDatabaseQuery) {
				/* We temporary enable group support, if we have no problems we could remove this code
				 * we add the JD_KEY_* to group clause to this should not make problems with normal queries
				if ($this->sql->group != '') {
					$translate = false;
				}
				*/

				// TODO Check if count is not breaking any query, we need it for some com_content quries
				// Don't translate queries where we only have a count function
				$selects = $this->sql->select->getElements();
				if (count($selects) == 1 && stripos($selects[0], 'COUNT(') !== false && stripos($selects[0], ',') === false) {
					$translate = false;
				}

			} else {
				if (strpos($this->sql, 'GROUP BY', strrpos($this->sql, ')')) !== false) {
					//$translate = false;
				}
				// TODO Check if count is not breaking any query, we need it for some com_content quries
				// Don't translate queries where we only have a count function
				$selects = substr($this->sql, 0, stripos($this->sql, 'FROM'));
				if (stripos($selects, 'COUNT(') !== false && stripos($selects, ',') === false) {
					$translate = false;
				}
			}

			JLog::add('Query translated: ' . str_replace("\n", '\n', $this->sql), JLog::DEBUG, 'jdiction');
		} else {
			JLog::add('Query not translated: ' . str_replace("\n", '\n', $this->sql), JLog::DEBUG, 'jdiction');
			//JLog::add('Backtrace: ' . @json_encode(debug_backtrace()), JLog::DEBUG, 'jdiction');
		}
		return $translate;
	}

	/**
	 * Execute the query
	 *
	 * @return  mixed  A database resource if successful, FALSE if not.
	 */
	public function execute() {

		// Check if we want and are ready to translate this query
		if ($this->translateQuery()) {
			$origsql      = $this->sql;
			$origlimit    = $this->limit;
			$origoffset   = $this->offset;
			$sql          = $this->addJoinKeys($origsql);
			$this->limit  = $origlimit;
			$this->offset = $origoffset;
			$this->sql    = $sql;
			try {
				parent::execute();
				//restore original query
				$this->sql = $origsql;
			} catch (JDatabaseException $e) {
				// there was a Problem with the query so we fallback to the unmodified
				// version and try again
				$this->sql = $origsql;
				parent::execute();
			}
			$this->collectTranslationTables();
		} else {
			parent::execute();
		}

		return $this->cursor;
	}

	/**
	 * Get primary keys for JOIN queries
	 * @params string the query
	 * @return string the new query
	 */
	public function addJoinKeys($sql) {

		// Load profiler
		$profiler = JProfiler::getInstance('jDiction');

		// Initialize variables.
		$jdkey     = 0;
		$tableList = $this->getTableList();

		//test for non jdatabasequery
//    $sql = (string) $sql;

		if ($sql instanceof JDatabaseQuery) {
				$profiler->mark('Start AddJoinKey to JDatabaseQuery, ' . $sql);

				$tables = array();
				$select = $sql->select->getElements();
				// TODO Check if count is not breaking any query, we need it for some com_content quries
				// Don't translate queries with COUNT functions
			if (count($select) > 0 /*|| substr($select[0], 0, 5) != 'COUNT' */) {
				if ($sql->join && count($sql->join)) {
					foreach ($sql->join as $join) {
						foreach ($join->getElements() as $table) {
							$tables[] = $table;
						}
					}
				}

				if ($sql->from) {
					foreach ($sql->from->getElements() as $table) {
						if (strpos($table, ',') !== false) {
							$tables = array_merge($tables, explode(',', $table));
						} else {
							$tables[] = $table;
						}
					}
				}

				foreach ($tables as $table) {
					$tableclean = str_ireplace(' AS ', ' ', trim($table));

					list($tablename, $alias,) = explode(' ', $tableclean . ' ', 3); // space prevents a notice warning
					if ($alias == '') {
						$alias = $tablename;
					}

					//clean name quote from table and alias
					$tablename = trim($tablename, $this->nameQuote);
					$alias = trim($alias, $this->nameQuote);

					$tableprefixed = str_replace('#__', $this->getPrefix(), $tablename);

					// We only process tables that exists
					if (in_array($tableprefixed, $tableList)) {
						// Add the primary key to the query
						if ($primarykey = $this->getPrimaryKey($tableprefixed)) {
							$jdkey++;
							$sql->select($sql->quoteName($alias) . '.' . $sql->quoteName($primarykey) . ' AS '.$sql->quoteName('JD_MAGIC_KEY_' . $jdkey));
							if ($sql->group != '') {
								$sql->group($sql->quoteName('JD_MAGIC_KEY_' . $jdkey));
							}
						}
					}
				}
			}
			$return = $sql;
		} else {
			$profiler->mark('Start AddJoinKey to string based, ' . $sql);
			$return = $sql;
			// todo Atm the parser has a problem with the USE Key word
			if (stripos($sql, 'USE KEY') === false && stripos($sql, 'USE INDEX') === false) {
				$parser      = $this->getParser();
				$creator     = $this->getCreator();
				$prefixedsql = $this->replacePrefix($sql);

				$parsed = $parser->parse($prefixedsql, false);
				// TODO Check if count is not breaking any query, we need it for some com_content quries
				// Don't translate queries with COUNT functions
				if (count($parsed['SELECT']) > 0 /*|| $parsed['SELECT'][0]['base_expr'] != 'COUNT'*/) {
					foreach ($parsed['FROM'] as $table) {
						if ($table['expr_type'] == 'table') {

							$alias = (is_array($table['alias']) ? $table['alias']['name'] : $table['table']);

							// We only process tables that exists
							if (in_array($table['table'], $tableList)) {
								// Add the primary key to the query
								if ($primarykey = $this->getPrimaryKey($table['table'])) {
									$jdkey++;
									$spec = array(
										'expr_type' => 'colref',
										'alias'     => array(
											'as'   => true,
											'name' => 'JD_MAGIC_KEY_' . $jdkey
										),
										'base_expr' => $alias . '.' . $primarykey,
										'no_quotes' => array(
											'delim' => ".",
											'parts' => array(
												$alias,
												$primarykey
											)
										),
										'sub_tree'  => false,
										'delim'     => false
									);

									$parsed['SELECT'][count($parsed['SELECT']) - 1]['delim'] = ',';

									$parsed['SELECT'][] = $spec;

									if (!empty($parsed['GROUP'])) {
										$spec              = array(
											'expr_type' => 'colref',
											'base_expr' => 'JD_MAGIC_KEY_' . $jdkey,
											'no_quotes' => array(
												'delim' => false,
												'parts' => array(
													'JD_MAGIC_KEY_' . $jdkey
												)
											),
											'sub_tree'  => false
										);
										$parsed['GROUP'][] = $spec;
									}
								}
							}
						}
					}

					$return = $creator->create($parsed);
				}
			}

		}

		$profiler->mark('End AddJoinKey');

		return $return;
	}

/**
 * get Primary key of table
 * @param string $table Table with or without quote and . notation
 * @return mixed Fieldname or false
 */
	public function getPrimaryKey($table) {
		$split = explode('.', $table);
		for ($c = 0; $c < count($split); $c++) {
			if (substr($split[$c], 0, 1) == substr($this->nameQuote, 0, 1)) {
				$split[$c] = substr($split[$c], 1);
			}
			if (substr($split[$c], -1) == substr($this->nameQuote, -1)) {
				$split[$c] = substr($split[$c], 0, -1);
			}
		}
		$table = implode('.', $split);

		$columns = $this->getTableColumns($table, false);
		foreach ($columns as $column) {
			if ($column->Key == 'PRI') {
				return $column->Field;
			}
		}

		return false;
	}

	/**
	 * Assumes database collation in use by sampling one text field in one table
	 *
	 * @return  string  Collation in use
	 */
	public function getCollation() {
		//proxy without translation
		$old    = $this->setTranslate(false);
		$result = parent::getCollation();
		$this->setTranslate($old);
		return $result;
	}

	/**
	 * Description
	 *
	 * @return  array  A list of all the tables in the database
	 */
	public function getTableList() {
		//proxy without translation
		$old    = $this->setTranslate(false);
		$result = parent::getTableList();
		$this->setTranslate($old);
		return $result;
	}

	/**
	 * Shows the CREATE TABLE statement that creates the given tables
	 *
	 * @param  array|string A table name or a list of table names
	 * @return  array A list the create SQL for the tables
	 */
	public function getTableCreate($tables) {
		//proxy without translation
		$old    = $this->setTranslate(false);
		$result = parent::getTableCreate($tables);
		$this->setTranslate($old);
		return $result;
	}

	/**
	 * Retrieves field information about the given tables.
	 *
	 * @param   string $table The name of the database table.
	 * @param   boolean $typeOnly True (default) to only return field types.
	 *
	 * @return  array  An array of fields by table.
	 *
	 * @since   11.1
	 * @throws  JDatabaseException
	 */
	public function getTableColumns($tables, $typeonly = true) {
		//proxy without translation
		$old    = $this->setTranslate(false);
		$result = parent::getTableColumns($tables, $typeonly);
		$this->setTranslate($old);
		return $result;
	}

	/**
	 * Set the target language to translate to
	 *
	 * @param int $language Language id to translate, null for default
	 */
	public function setLanguage($language) {
		if ($this->jd_language) {
			$return = $this->jd_language;
		} else {
			$return = JFactory::getLanguage()->getTag();
		}

		if ((int)$language > 0) {
			$this->jd_language = $language;
			return $return;
		}

		static $languagecache;

		//load languag id from #__languages table
		if (!is_array($languagecache)) {
			$query = $this->getQuery(true);
			$query->select('lang_id, lang_code');
			$query->from('#__languages');
			$this->setQuery($query);
			$old           = $this->setTranslate(false);
			$languagecache = $this->loadAssocList('lang_code', 'lang_id');
			$this->setTranslate($old);
		}

		if (array_key_exists($language, $languagecache)) {
			$this->jd_language = $languagecache[$language];
		} else {
			JError::raiseError(500, 'Could not find language. (' . $language . ')');
		}
		return $return;
	}

	/**
	 * Returns the language we translate to
	 *
	 * @return bool the language id
	 */
	public function getLanguage() {
		if (!$this->jd_language) {
			$lang = JFactory::getLanguage();
			$this->setLanguage($lang->getTag());
		}
		return $this->jd_language;
	}

	/**
	 * Returns the language tag we translate to
	 *
	 * @return bool the language id
	 */
	/* public function getLanguageTag() {
		return $this->jd_language;
	}
	*/

	/**
	 * Activate or deactive the translation module
	 *
	 * @param bool $status Should we translate
	 */
	public function setTranslate($status) {
		$old             = $this->jd_active;
		$this->jd_active = $status;
		return $old;
	}

	/**
	 * Returns the translation state
	 *
	 * @return bool the state of the translation module
	 */
	public function getTranslate() {
		return $this->jd_active;
	}
}