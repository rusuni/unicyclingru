<?php
/**
 * jDiction library entry point
 *
 * @package jDiction
 * @link http://joomla.itronic.at
 * @copyright  Copyright (C) 2011 ITronic Harald Leithner. All rights reserved.
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

abstract class jDictionAdminHelper {
	/**
	 * Adds the itrFile toolbar button to the toolbar based on the
	 * given parameters.
	 *
	 * @param string Option parameter
	 * @param string View parameter
	 * @param string Layout parameter or default
	 * @param string Primary key of the table for this item
	 */
	public static function addToolbar($ext = null, $view = null, $layout = null, $id = null) {

		$input = JFactory::getApplication()->input;
		if (is_null($layout)) {
			$layout = $input->get('layout', false, 'cmd');
		}
		if ($layout != 'edit') {
			return;
		}

		$urls = self::getInterfaceUrls($ext, $view, $layout, $id, '');

		if (empty($urls)) {
			return;
		}

		$bar = JToolBar::getInstance('toolbar');

		// @deprecated used for Joomla 2.5

		if (version_compare(JVERSION, '3.0', 'ge')) {
			$bar->addButtonPath(JPATH_LIBRARIES . '/itrlib/toolbar/button/');
			$buttontype = 'itrPopup';
			$width      = '95%';
			$height     = '95%';

		} else {
			$buttontype = 'Popup';
			$width      = 'window.getSize().x-70';
			$height     = 'window.getSize().y-70';
		}

		require_once JPATH_SITE . '/components/com_itrfile/helpers/itrfilehelper.php';

		foreach ($urls as $type => $item) {

			if ($item['id'] == 0) {
				// @todo add a way for Joomla 3.0+
				if (version_compare(JVERSION, '3.0', 'lt')) {
					$bar->appendButton('Link', 'itrfile-' . $type, JText::sprintf('LIB_ITRFILE_TOOLBAR_' . strtoupper($type), 0), "javascript:alert('" . JText::_('LIB_ITRFILE_SAVE_FIRST', true) . "');");
				}
			} else {
				$bar->appendButton($buttontype, 'itrfile-' . $type, JText::sprintf('LIB_ITRFILE_TOOLBAR_' . strtoupper($type), $item['count']), $item['link'], $width, $height);
			}

			//add to global for editor-xtd buttons
			if ($type == 'image') {
				self::$editorXtd[$type] = $item['link'];
			}
		}

		JHTML::_('stylesheet', 'administrator/components/com_itrfile/assets/style.css', array(), false);
	}

	public static function getInterface($option = null, $view = null, $layout = null, $id = null) {

		$item = self::getInterfaceUrl($option, $view, $layout, $id);

		if (empty($item)) {
			return false;
		}

		$result   = array();
		$result[] = '<iframe src="" data-src="' . $item['link'] . '" style="width: 100%; height: 600px; border: 0" id="jdiction-frame"></iframe>';

		$result[] = '<script>';
		$result[] = 'function showjDiction(){';
		$result[] = ' var next=true;';
		$result[] = '	jQuery("#jdiction-frame").each(function(){';
		$result[] = '		var $this = jQuery(this);';
		$result[] = '		if($this.is(":visible")){';
		$result[] = '			$this.attr("src", $this.data("src"))';
		$result[] = '			next=false;';
		$result[] = '		}';
		$result[] = '	});';
		$result[] = '	if (next) {';
		$result[] = '	  window.setTimeout(showjDiction, 1000);';
		$result[] = '	}';
		$result[] = '}';
		$result[] = 'window.setTimeout(showjDiction, 1000);';
		$result[] = '</script>';

		return implode(chr(10), $result);
	}

	public static function getInterfaceUrl($option = null, $view = null, $layout = null, $id = null, $popup = true) {

		$input = JFactory::getApplication()->input;
		$jd    = jDiction::getInstance();

		if (is_null($option)) {
			$option = $input->get('option', false, 'cmd');
		}
		if (is_null($view)) {
			$view = $input->get('view', false, 'cmd');
		}
		if (is_null($layout)) {
			$layout = $input->get('layout', 'default', 'cmd');
		}

		if (!$option || !$view || !$layout) {
			return;
		}

		$table   = $jd->getTableByView($option, $view);
		$viewobj = $jd->getView($option, $view);

		if (is_null($id)) {
			if (is_object($viewobj) && $viewobj->key != '') {
				$id = (array)$input->get($viewobj->key, 0, 'array');
			} elseif (is_object($table) && $table->key != '') {
				$id = (array)$input->get($table->key, 0, 'array');
			} else {
				return;
			}
			$id = (int)current($id);
		}

		$result = array(
			'option' => $option,
			'view'   => $view,
			'layout' => $layout,
			'id'     => $id,
			'link'   => $jd->getTranslationLink($option, $view, $layout, $id, $popup)
		);

		return $result;
	}

	public function getTranslationStatus($id = null, $option = null, $view = null) {

		$jd    = jdiction::getInstance();
		$input = JFactory::getApplication()->input;

		if (is_null($option)) {
			$option = $input->get('option', false, 'cmd');
		}

		if (is_null($view)) {
			$view = $input->get('view', 'default', 'cmd');
		}
		$view = $jd->getView($option, $view);
		if (!$view) {
			return false;
		}
		$table  = $jd->getTableByView($option, $view->name);
		$layout = $view->layout;

		$result = new stdClass;

		$result->link = $jd->getTranslationLink($option, $view->name, $layout, $id, false);
		$result->html = array();

		$languages = $jd->getLanguages();
		$perrow    = count($languages);
		if ($perrow > 3) {
			$perrow = ceil($perrow / 2);
		}

		$result->html[] = '<div class="btn-group" style="margin: 1px 0" >';
		$i              = 0;
		foreach ($languages as $language) {
			$result->status[$language->image] = $jd->getTranslationStatus($table->name, $id, $language->lang_id);
			if (++$i > $perrow) {
				$i              = 0;
				$result->html[] = '</div>';
				$result->html[] = '<div class="btn-group" style="margin: 1px 0" >';

			}
			$result->html[] = '<a href="' . $result->link . '" class="btn btn-micro" rel="tooltip" data-original-title="' . $language->title . '" >';
			$result->html[] = '<span style="background: url(../media/com_jdiction/images/flags/' . $language->image . '.png) no-repeat center center; width: 26px; height: 16px; display: inline-block;"><img src="components/com_jdiction/assets/icon-status-26-' . $result->status[$language->image] . '.png" /></span>';
			$result->html[] = '</a>';
		}
		$result->html[] = '</div>';
		$result->html   = implode('', $result->html);

		return $result;
	}
}
