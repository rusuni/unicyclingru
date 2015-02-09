<?php
/**
 * jDiction library entry point
 *
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

?>
	<fieldset>
		<div class="fltrt">
			<button type="button" onclick="Joomla.submitform('translation.apply', $('component-form'));">
				<?php echo JText::_('JAPPLY');?></button>
			<button type="button" onclick="Joomla.submitform('translation.save', $('component-form'));">
				<?php echo JText::_('JSAVE');?></button>
			<button type="button" onclick="window.parent.SqueezeBox.close();">
				<?php echo JText::_('JCANCEL');?></button>
		</div>
		<div class="configuration" >
			<?php echo JText::_($this->component->option). ' '.JText::_('COM_JDICTION_COMPONENT_TOOLBAR_TITLE'); ?>
		</div>
	</fieldset>
	<?php require dirname(__FILE__).'/form_25.php'; ?>
