<?php

/**
 * @version		$Id$
 * @author		NooTheme
 * @package		Joomla.Site
 * @subpackage	mod_noo_timeline
 * @copyright	Copyright (C) 2013 NooTheme. All rights reserved.
 * @license		License GNU General Public License version 2 or later; see LICENSE.txt, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access'); 
require_once __DIR__ . '/helper.php';
$document = JFactory::getDocument();
//include css

$document->addStyleSheet('modules/' . $module->module . '/assets/css/style.css');
//Include js
$document->addScript('modules/' . $module->module . '/assets/js/script.js');
$document->addScriptDeclaration('
	jQuery(document).ready(function($){
		$("#noo_tl'.$module->id.'").nootimeline();
	});
');

$lists = modNooTimeLineHelper::getTimeLine($params);

require (JModuleHelper::getLayoutPath('mod_noo_timeline',$params->get('layout', 'default')));