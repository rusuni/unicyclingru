<?php
/**
 * @version     1.0.0
 * @package     com_seopult
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Dmitry <mitrich.home@gmail.com> - http://redsoft.ru
 */

defined('_JEXEC') or die;

// Include dependancies
jimport('joomla.application.component.controller');

// Execute the task.
$controller	= JController::getInstance('Seopult');
$controller->execute(JRequest::getVar('task',''));
$controller->redirect();
