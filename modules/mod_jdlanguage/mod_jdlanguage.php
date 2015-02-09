<?php
/**
 * @version		$Id$
 * @package		Joomla.Site
 * @subpackage	mod_languages
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__).'/helper.php';

$jdiction = jDiction::getInstance();
$languages = $jdiction->getLanguages(true);
$current = JFactory::getLanguage();
$doc = JFactory::getDocument();
$access = JFactory::getUser()->getAuthorisedViewLevels();

foreach($languages as $k=>$lang) {
  if (!in_array($lang->access, $access)) {
    unset($languages[$k]);
    continue;
  }
	list($lang->link, $lang->menutitle) = modJdLanguageHelper::getLink($lang);
	if ($lang->lang_code == $current->getTag()) {
		if ($params->get('currentlanguage')) {
			$lang->active = true;
		} else {
			unset($languages[$k]);
			continue;
		}
	} else {
		$lang->active = false;
		if (($lang->menutitle != '') && ($params->get('alternatetag'))) {
			$doc->addHeadLink($lang->link, 'alternate', 'rel', array(
				'type' => 'text/html',
				'hreflang' => $lang->lang_code,
				'lang' => $lang->lang_code,
				'title' => $lang->menutitle
			));
		}
	}
}

$class_sfx	= htmlspecialchars($params->get('class_sfx'));

require JModuleHelper::getLayoutPath('mod_jdlanguage', $params->get('layout', 'default'));
