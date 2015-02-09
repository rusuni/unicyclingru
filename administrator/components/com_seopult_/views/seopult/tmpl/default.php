<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_search
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
$jinput = JFactory::getApplication()->input;

$proceed = $jinput->get('proceed') ? '&proceed=1' : '';
$stop = $jinput->get('stop') ? '&stop=1' : '';

?>
<iframe src="<?php echo JUri::root().'administrator/index.php?option=com_seopult&task=seopult&tmpl=component'.$proceed.$stop;?>" width="100%" height="700" frameborder="0"></iframe>