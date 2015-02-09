<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_search
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
/**
 * View class for a list of search terms.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_search
 * @since       1.5
 */
class SeopultViewSeopult extends JViewLegacy
{
    protected $enabled;

    protected $items;

    protected $pagination;

    protected $state;

    /**
     * Display the view
     */
    public function display($tpl = null)
    {
        $this->addToolbar();
        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @since	1.6
     */
    protected function addToolbar()
    {

        JToolbarHelper::title(JText::_('COM_SEOPULT'), 'seopult.png');
    }
}
