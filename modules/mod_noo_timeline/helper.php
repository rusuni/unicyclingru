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

abstract class modNooTimeLineHelper {

	public static function getTimeLine(&$params){
		$timeline = $params->get('timeline');
		$timeline = json_decode(htmlspecialchars_decode($timeline),true);
		return $timeline;
	}
}
