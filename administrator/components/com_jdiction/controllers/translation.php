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

jimport('joomla.application.component.controllerform');

/**
 * Translation Controller
 *
 * @package jDiction
 */
class jDictionControllerTranslation extends JControllerForm {
	/**
	 * @var		string	The prefix to use with controller messages.
	 */
	protected $text_prefix = 'COM_JDICTION_TRANSLATION'; 
	
	/**
	 * Method to save a record.
	 *
	 * @param	string	$key	The name of the primary key of the URL variable.
	 * @param	string	$urlVar	The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
	 *
	 * @return	Boolean	True if successful, false otherwise.
	 * @since	1.6
	 */
	public function save($key = null, $urlVar = null) {
    $input = JFactory::getApplication()->input;
		$result = parent::save($key, $urlVar);
		if ($this->getTask() == 'save') {
			$tmpl = $input->get('tmpl', null, 'cmd');
			if ($tmpl == 'component') {
        // @deprecated used for Joomla 2.5
        if (version_compare(JVERSION, '3.0.0', 'lt')) {
          die('<script>window.parent.SqueezeBox.close();</script>');
        } else {
          die("<script>window.parent.jQuery('#modal-jdiction-component').parent().click();</script>");
        }
			} else {
				$option = $input->get('jd_option', null, 'cmd');
				$view = $input->get('jd_view', null, 'cmd');

        $view = jdiction::getInstance()->getView($option, $view);

        $this->setRedirect(JRoute::_('index.php?option='.$option.'&view='.$view->list, false));
			}
		}
		return $result;
	}
	
	/**
	 * Method to cancel an edit.
	 *
	 * @param	string	$key	The name of the primary key of the URL variable.
	 *
	 * @return	Boolean	True if access level checks pass, false otherwise.
	 * @since	1.6
	 */
	public function cancel($key = null) {
    $input = JFactory::getApplication()->input;
		$result = parent::cancel($key);
		if ($result) {
			$tmpl = $input->get('tmpl', null, 'cmd');
			if ($tmpl == 'component') {
        // @deprecated used for Joomla 2.5
        if (version_compare(JVERSION, '3.0.0', 'lt')) {
          die('<script>window.parent.SqueezeBox.close();</script>');
        } else {
          die("<script>window.parent.jQuery('#modal-jdiction-component').parent().click();</script>");
        }
			} else {
        $option = $input->get('jd_option', null, 'cmd');
        $view = $input->get('jd_view', null, 'cmd');

        $view = jdiction::getInstance()->getView($option, $view);

				$this->setRedirect(JRoute::_('index.php?option='.$option.'&view='.$view->list, false));
			}
		}
		
	}
	
	/**
	 * Gets the URL arguments to append to an item redirect.
	 *
	 * @param	int		$recordId	The primary key id for the item.
	 * @param	string	$urlVar		The name of the URL variable for the id.
	 *
	 * @return	string	The arguments to append to the redirect URL.
	 * @since	1.6
	 */
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id') {
    $input = JFactory::getApplication()->input;
		$append = parent::getRedirectToItemAppend($recordId, $urlVar);
		
		$id	= $input->get('jd_id', null, 'int');
		$component = $input->get('jd_option', null, 'cmd');
		$view	= $input->get('jd_view', null, 'cmd');
		$layout	= $input->get('jd_layout', null, 'cmd');
		
		if ($id) {
			$append		.= '&jd_id='.$id;
		}

		if ($component) {
			$append		.= '&jd_option='.$component;
		}

		if ($view) {
			$append		.= '&jd_view='.$view;
		}

		if ($layout) {
			$append		.= '&jd_layout='.$layout;
		}
		
		return $append;
	}	
}
