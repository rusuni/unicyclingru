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

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.formvalidation');
?>
<script>
  jQuery(function() {
    setHeight();
    setInterval(setHeight, 5000);
  });
  function setHeight() {
    var iframe = parent.document.getElementById('jdiction-frame');
    if (iframe) {
      iframe.style.height = (jQuery(document).height()+30) + 'px';
    }
  }
</script>
<header style="height: 27px; margin-bottom: 10px;">
  <div class="btn-group pull-left">
    <button type="button" onclick="Joomla.submitform('translation.apply', document.getElementById('component-form'));" class="btn btn-small btn-success"><?php echo JText::_('JAPPLY');?></button>
  </div>
</header>

<?php require dirname(__FILE__).'/form.php'; ?>
