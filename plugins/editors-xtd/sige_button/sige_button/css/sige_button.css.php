<?php
/**
 *  @Copyright
 *  @package    Editor Button - SIGE Parameter Button - Plugin for Joomla! 3
 *  @author     Viktor Vogel {@link http://www.kubik-rubik.de}
 *  @version    3-1 - 2013-10-05
 *  @link       Project Site {@link http://joomla-extensions.kubik-rubik.de/sige-simple-image-gallery-extended}
 *
 *  @license GNU/GPL
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
defined('_JEXEC') or die('Restricted access');

if (extension_loaded('zlib') && !ini_get('zlib.output_compression'))
@ob_start('ob_gzhandler');
header('Content-type: text/css; charset: UTF-8');
header('Cache-Control: must-revalidate');
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 86400) . ' GMT');

define('DS', DIRECTORY_SEPARATOR);
$dir = dirname(__FILE__);
$filename = $dir . DS . 'sige_button.css';

include($filename);

?>