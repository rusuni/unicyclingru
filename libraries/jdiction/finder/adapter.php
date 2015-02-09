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

// Register dependent classes.
/*JLoader::register('JdFinderIndexer', dirname(__FILE__) . '/indexer.php');
JLoader::register('JdFinderIndexerHelper', dirname(__FILE__) . '/helper.php');
JLoader::register('JdFinderIndexerResult', dirname(__FILE__) . '/result.php');
JLoader::register('JdFinderIndexerTaxonomy', dirname(__FILE__) . '/taxonomy.php');
*/

jimport('joomla.application.component.helper');

// Load the base adapter.
require_once JPATH_ADMINISTRATOR . '/components/com_finder/helpers/indexer/adapter.php';


/**
 * Prototype adapter class for the Finder indexer package with jDiction support.
 *
 */
abstract class JdFinderIndexerAdapter extends FinderIndexerAdapter {

  /**
   * Affects constructor behavior. If true, language files will be loaded automatically.
   *
   * @var    boolean
   * @since  12.3
   */
  protected $autoloadLanguage = true;


  /**
   * @param object $subject The object to observe
   * @param array $config An array that holds the plugin configuration
   */
  public function __construct(&$subject, $config) {
    parent::__construct($subject, $config);
    //@deprecated used for Joomla 3.1.1 repleaced by autoloadLanguage
    if (version_compare(JVERSION, '3.1.1', '<')) {
      $this->loadLanguage();
    }
  }

  /**
   * Method to index a batch of content items. This method can be called by
   * the indexer many times throughout the indexing process depending on how
   * much content is available for indexing. It is important to track the
   * progress correctly so we can display it to the user.
   *
   * @return  boolean  True on success.
   *
   * @since   2.5
   * @throws  Exception on error.
   */
  public function onBuildIndex()
  {
    return parent::onBuildIndex(); //@todo experimentl more tests on this
    // Get the indexer and adapter state.
    $iState = FinderIndexer::getState();
    $aState = $iState->pluginState[$this->context];

    // Check the progress of the indexer and the adapter.
    if ($iState->batchOffset == $iState->batchSize || $aState['offset'] == $aState['total'])
    {
      return true;
    }

    // Get the batch offset and size.
    $offset = (int) $aState['offset'];
    $limit = (int) ($iState->batchSize - $iState->batchOffset);

    // Get the content items to index.
    $items = $this->getItems($offset, $limit);

    // Iterate through the items and index them.
    for ($i = 0, $n = count($items); $i < $n; $i++)
    {
      // Index the item.
      $this->index($items[$i]);

      $iState->totalItems--;
    }

    // Adjust the offsets.
    if ($limit > count($items)) {
      $limit = count($items);
    }
    $offset += $limit;
    $iState->batchOffset += $limit;

    // Update the indexer state.
    $aState['offset'] = $offset;
    $iState->pluginState[$this->context] = $aState;
    FinderIndexer::setState($iState);

    return true;
  }

  /**
   * Method to get the number of content items available to index.
   *
   * @return  integer  The number of content items available to index.
   *
   * @since   2.5
   * @throws  Exception on database error.
   */
  protected function getContentCount() {

    $jDiction = jDiction::getInstance();
    $languages = $jDiction->getLanguages(true);

    $return = 0;

    // Get the list query.
    $query = $this->getListQuery();

    // Check if the query is valid.
    if (empty($query)) {
      return $return;
    }

    // we don't support non JDatabaseQueries here atm
    if (!$query instanceof JDatabaseQuery) {
      return parent::getContentCount();
    }

    $columns = $this->db->getTableColumns($this->table);

    if (array_key_exists('language', $columns)) {
      $query = clone($query);
      $query->clear('select')
        ->select('COUNT(*)')
        ->clear('order');

      // Get the total number of content items per language to index.
      foreach($languages as $language) {

        $querylang = clone($query);
        $querylang->where('a.language = '.$this->db->q($language->lang_code));

        $this->db->setQuery($querylang);
        $return += (int) $this->db->loadResult();
      }

      // Get the total number of content items translated languages to index.
      $query->where('a.language = '.$this->db->q('*'));
    }
    $this->db->setQuery($query);
    $return += ((int) $this->db->loadResult() * count($languages));

    return $return;
  }

  /**
   * Method to get a list of content items to index.
   *
   * @param   integer         $offset  The list offset.
   * @param   integer         $limit   The list limit.
   * @param   JDatabaseQuery  $sql     A JDatabaseQuery object. [optional]
   *
   * @return  array  An array of FinderIndexerResult objects.
   *
   * @since   2.5
   * @throws  Exception on database error.
   */
  protected function getItems($offset, $limit, $sql = null) {
    JLog::add('FinderIndexerAdapter::getItems', JLog::INFO);

    $items = array();

    $jDiction = jDiction::getInstance();
    $languages = $jDiction->getLanguages(true);
    $db = $this->db;
    $oldTranslation = $db->getTranslate();
    $oldLanguage = $db->getLanguage();

    $columns = $db->getTableColumns($this->table);

    foreach($languages as $language) {
      // Get the content items to index.
      $query = $this->getListQuery();

      $db->setTranslate(true);
      $language = $language->lang_code;
      $db->setLanguage($language);

      if (array_key_exists('language', $columns)) {
        $query->where('a.language in ('.$db->q('*').','.$db->q($language).')');
      }

      if ($sql != '') {
        $query->where($sql);
      }
      // @ todo Workaround because we return too many items, should be solved by changing onBuildIndex
      //$this->db->setQuery($query, $offset, $limit);
      $db->setQuery($query, $offset/count($languages), $limit);

      $rows = $db->loadAssocList();

      // Check for a database error.
      if ($this->db->getErrorNum()) {
        // Throw database error exception.
        throw new Exception($this->db->getErrorMsg(), 500);
      }

      // Convert the items to result objects.
      foreach ($rows as $row) {
        $row['language'] = $language;
        // Convert the item to a result object.
        $item = JArrayHelper::toObject($row, 'FinderIndexerResult');

        // Set the item type.
        $item->type_id = $this->type_id;

        // Set the mime type.
        $item->mime = $this->mime;

        // Set the item layout.
        $item->layout = $this->layout;

        // Set the extension if present
        if (isset($row->extension)) {
          $item->extension = $row->extension;
        }

        // Add the item to the stack.
        $items[] = $item;
      }
    }
    $db->setTranslate($oldTranslation);
    $db->getLanguage($oldLanguage);
    return $items;
  }

  /**
   * Method to get the URL for the item. The URL is how we look up the link
   * in the Finder index.
   *
   * @param   integer  $id         The id of the item.
   * @param   string   $extension  The extension the category is in.
   * @param   string   $view       The view for the URL.
   *
   * @return  string  The URL of the item.
   *
   * @since   2.5
   */
  protected function getURL($id, $extension, $view, $language = '*') {
    if ($language != '*') {
      return 'index.php?option=' . $extension . '&view=' . $view . '&id=' . $id . '&lang='.$language;
    } else {
      return 'index.php?option=' . $extension . '&view=' . $view . '&id=' . $id;
    }

  }

}