<?php
/**
 * @version     1.0.0
 * @package     com_seopult
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Dmitry <mitrich.home@gmail.com> - http://redsoft.ru
 */


// no direct access
defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_search'))
{
    return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}


$controller	= JControllerLegacy::getInstance('Seopult');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
