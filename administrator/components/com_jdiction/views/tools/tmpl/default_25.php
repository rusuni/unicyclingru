<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_cpanel
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>

<div class="adminform">
	<div class="cpanel-left">
		<div class="cpanel">
			<?php foreach($this->buttons as $this->button): ?>
			<?php echo $this->loadTemplate('button_25'); ?>
			<?php endforeach; ?>
		</div>
	</div>
</div>
