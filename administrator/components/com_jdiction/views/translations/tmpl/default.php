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

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHTML::_('script','system/multiselect.js',false,true);
$user	= JFactory::getUser();
$userId	= $user->get('id');
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
?>

<form action="<?php echo JRoute::_('index.php?option=com_jdiction&view=translations'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="clearfix"></div>

	<table class="table table-striped">
		<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)" />
				</th>
				<th class="title">
					<?php echo JHtml::_('grid.sort',  'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
				</th>
        <th class="title">
          <?php echo JHtml::_('grid.sort',  'JGRID_HEADING_LANGUAGE', 'a.idLang', $listDirn, $listOrder); ?>
        </th>
				<th width="1%" class="nowrap">
					<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ID', 'a.idJdStore', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($this->items as $i => $item) :
			$ordering	= ($listOrder == 'a.ordering');
			$canCreate	= $user->authorise('core.create',		'com_jdiction.translation');
			$canEdit	= $user->authorise('core.edit',			'com_jdiction.translation');
			$canCheckin	= $user->authorise('core.manage',		'com_checkin') || $item->checked_out==$user->get('id') || $item->checked_out==0;
			$canChange	= $user->authorise('core.edit.state',	'com_jdiction.translation') && $canCheckin;
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>
				<td>
					<?php if (($canCreate || $canEdit)) : ?>
						<a href="<?php echo JRoute::_($this->jd->getTranslationLink($item->referenceOption, $item->referenceView, $item->referenceLayout, $item->idReference, false)); ?>">
							<?php echo $this->escape($item->referenceOption.' - '.$item->referenceView.' - '.$item->idReference); ?></a>
					<?php else : ?>
							<?php echo $this->escape($item->referenceOption.' - '.$item->referenceView.' - '.$item->idReference); ?>
					<?php endif; ?>
				</td>
        <td>
          <?php echo current($this->jd->getLanguage($item->idLang))->title; ?>
        </td>
				<td class="center">
					<?php echo (int) $item->id; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
  <?php echo $this->pagination->getListFooter(); ?>
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
