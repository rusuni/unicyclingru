<?php
/**
* @package		AlterRepors
* @copyright	Copyright (C) 2009-2013 AlterBrains.com. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*/

defined('_JEXEC') or die('Restricted access'); 

class plgSystemAjaxtogglerInstallerScript
{
	function install($parent)
	{
		JFactory::getApplication()->enqueueMessage(JText::_('Successfully installed "System - AJAX Toggler" plugin!'));
	}

	function uninstall($parent)
	{
		JFactory::getApplication()->enqueueMessage(JText::_('Successfully uninstalled "System - AJAX Toggler" plugin!'));
	}

	function update($parent)
	{
		JFactory::getApplication()->enqueueMessage(JText::_('Successfully updated "System - AJAX Toggler" plugin!'));
	}
	
}