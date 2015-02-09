<?php
/*------------------------------------------------------------------------
# mod_dinamods - Dinamod Tab Modules
# ------------------------------------------------------------------------
# author    Joomla!Vargas
# copyright Copyright (C) 2010 joomla.vargas.co.cr. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://joomla.vargas.co.cr
# Technical Support:  Forum - http://joomla.vargas.co.cr/forum
-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
//require_once __DIR__  . '/helper.php';
require_once dirname(__FILE__) . '/helper.php';

$dinamods = JModuleHelper::getModules( trim( $params->get('position', 'dinamod') ) );

if ( !$dinamods ) :  return; endif;

global $dinamods_id;

if ( !$dinamods_id ) : $dinamods_id = 1; endif;

$doc = JFactory::getDocument();
$doc->addStyleDeclaration(modDinamodsHelper::buildCSS( $params, $dinamods_id ));

$doc->addScript('modules/mod_dinamods/js/dinamods.js');

require( JModuleHelper::getLayoutPath('mod_dinamods') );

$dinamods_id++;