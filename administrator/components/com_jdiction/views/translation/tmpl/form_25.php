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


// Load the tooltip behavior.
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (document.formvalidator.isValid(document.id('component-form'))) {
			$$('input[type!=hidden], textarea, select');
			Joomla.submitform(task, document.getElementById('component-form'));
		}
	};
	
	window.addEvent('domready', function() {
		$$('input[type!=hidden], textarea, select').setStyle('border-color', 'green');
		$$('input[type!=hidden], textarea, select').addEvent('change', function() {
			jdiction_checkStatus(this.value, $(this.id+'_status').title, this.id, this);
		});
		//If we have a tinyMCE Editor
		if (typeof tinyMCE != 'undefined') {
	    tinyMCE.onAddEditor.add(function(mgr,ed) {
				ed.onChange.add(function(ed, e) {
					jdiction_checkStatus(ed.getContent(), $(ed.id+'_status').title, ed.id, $(ed.id+'_tbl'));
				});
			});
		}
	});
	
	function jdiction_checkStatus(newvalue, oldvalue, id,  marker) {
		if (newvalue != oldvalue) {
			marker.setStyle('border', '1px solid orange');
			$(id+'_status').value="changed";
		} else {
			marker.setStyle('border', '1px solid green');
			$(id+'_status').value="unchanged";
		}
	}
	function jdiction_removeContent(id, marker) {
		marker.setStyle('border', '1px solid red');
		$(id+'_status').value="remove";
		if ($(id+'_tbl')) { //Editor
			if (typeof tinymce != 'undefined') {
				tinymce.get(id).setContent('');
			}
		} else {
			$(id).value = '';
		}
	}
	function jdiction_copyContent(text, id) {
		
		if ($(id+'_tbl')) { //Editor
			if (typeof tinymce != 'undefined') {
				jdiction_checkStatus(tinymce.get(id).getContent(), text, id, $(id+'_tbl'));
				tinymce.get(id).setContent(text);
			}
		} else {
			jdiction_checkStatus(text, $(id).value, id,  $(id));
			$(id).value = text;
		}
	}

</script>
<style>
.info_img {
	float: left; 
	cursor: pointer;
	padding: 4px 2px 2px 2px;
}
</style>
<form action="<?php echo JRoute::_('index.php?option=com_jdiction');?>" id="component-form" method="post" name="adminForm" autocomplete="off" class="form-validate">

	<?php
	echo JHtml::_('tabs.start','language-tabs', array('useCookie'=>1));
	foreach($this->languages as $language) :
		$this->form->loadLanguage($language->lang_code);
			echo JHtml::_('tabs.panel',JText::_($language->title_native), 'language-tab-'.$language->lang_code);
      foreach($this->form->getFieldsets() as $fieldset) :
        ?>
        <div style="margin: 7px 0 5px 0; font-weight: bold"><?php echo JText::_($fieldset->label); ?></div>

        <ul class="config-option-list">
        <?php foreach($this->form->getFieldset($fieldset->name) as $field) : ?>
          <li>
          <?php if (!$field->hidden) : ?>
          <?php echo $field->label; ?>
          <?php endif; ?>
          <?php if ($field->type == 'Editor') : ?>
            <div class="clr"></div>
          <?php endif; ?>
          <?php echo $field->input; ?>

          <input type="hidden" id="<?php echo $field->id; ?>_status" name="<?php echo str_replace('jform', 'jdiction', $field->name); ?>" value="unchanged" title="<?php echo $this->escape($field->value); ?>" />
          <?php if ($field->type == 'Editor') : ?>
            <img class="info_img" src="<?php echo JRoute::_('components/com_jdiction/assets/icon-16-del.png'); ?>" width="16" onClick="jdiction_removeContent('<?php echo $field->id; ?>', $('<?php echo $field->id; ?>_tbl'));" />
          <?php else: ?>
            <img class="info_img" src="<?php echo JRoute::_('components/com_jdiction/assets/icon-16-del.png'); ?>" width="16" onClick="jdiction_removeContent('<?php echo $field->id; ?>', $('<?php echo $field->id; ?>'));" />
          <?php endif; ?>
          <img class="info_img hasTip" src="<?php echo JRoute::_('components/com_jdiction/assets/icon-16-info.png'); ?>" width="16" title="Original::<?php echo $this->escape($this->original->{$field->fieldname}); ?>" onClick="jdiction_copyContent(this.retrieve('tip:text'),'<?php echo $field->id; ?>');" />
          </li>
        <?php endforeach; ?>
        </ul>
    <div class="clr"></div>
    <?php
    endforeach;
	endforeach;
	echo JHtml::_('tabs.end');
	?>
	<div>
		<input type="hidden" name="jd_id" value="<?php echo $this->component->id;?>" />
		<input type="hidden" name="jd_option" value="<?php echo $this->component->option;?>" />
		<input type="hidden" name="jd_view" value="<?php echo $this->component->view;?>" />
		<input type="hidden" name="jd_layout" value="<?php echo $this->component->layout;?>" />
    <input type="hidden" name="jd_sourcehash" value="<?php echo $this->original->jd_sourcehash;?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="tmpl" value="<?php echo JFactory::getApplication()->input->get('tmpl', null, 'cmd'); ?>" />
		<input type="hidden" name="layout" value="<?php echo JFactory::getApplication()->input->get('layout', null, 'cmd'); ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form> 