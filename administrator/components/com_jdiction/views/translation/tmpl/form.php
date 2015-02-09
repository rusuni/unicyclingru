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
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (document.formvalidator.isValid(document.id('component-form'))) {
			Joomla.submitform(task, document.getElementById('component-form'));
		}
	};

  jQuery(function() {
		jQuery('input[type!=hidden], textarea, select').css('border-color', 'green');
    jQuery('input[type!=hidden], textarea, select').on('change', function() {
			jdiction_checkStatus(this.value, jQuery('#'+this.id+'_status').prop('title'), this.id, this);
		});
		//If we have a tinyMCE Editor
		if (typeof tinyMCE != 'undefined') {
      if (tinyMCE.majorVersion == "4") {
        tinyMCE.on('addEditor', function(args) {
          args.editor.on('init', function(args) {
            var ed = args.target;
            jdiction_checkStatus(ed.getContent(), jQuery('#'+ed.id+'_status').prop('title'), ed.id, ed.getContainer());
          });
          args.editor.on('blur', function(args) {
            var ed = args.target;
            jdiction_checkStatus(ed.getContent(), jQuery('#'+ed.id+'_status').prop('title'), ed.id, ed.getContainer());
          });
        });
      } else {
        tinyMCE.onAddEditor.add(function(mgr,ed) {
          ed.onChange.add(function(ed, e) {
            jdiction_checkStatus(ed.getContent(), jQuery('#'+ed.id+'_status').prop('title'), ed.id, '#'+ed.id+'_tbl');
          });
        });
      }
		}
	});
	
	function jdiction_checkStatus(newvalue, oldvalue, id,  marker) {
		if (newvalue != oldvalue) {
			jQuery(marker).css('border', '1px solid orange');
			jQuery('#'+id+'_status').val("changed");
		} else {
      jQuery(marker).css('border', '1px solid green');
      jQuery('#'+id+'_status').val("unchanged");
		}
	}

	function jdiction_removeContent(id, marker) {
    var ed;
		marker.css('border', '1px solid red');
    jQuery('#'+id+'_status').val("remove");

    //If we have a tinyMCE Editor
		if (typeof tinyMCE != 'undefined') {
			if (tinyMCE.majorVersion == "4") {
				ed = tinyMCE.get(id);
				if (ed) {
					ed.setContent('');
					return;
				}
			}
		}

		if (jQuery('#'+id+'_tbl').length > 0) { //Editor
			if (typeof tinymce != 'undefined') {
				tinymce.get(id).setContent('');
			}
		} else {
      if (!ed) {
        jQuery('#'+id).val('');
      }
		}
	}
	function jdiction_copyContent(text, id) {
    var ed;

		if (typeof tinyMCE != 'undefined') {
			if (tinyMCE.majorVersion == "4") {
				ed = tinyMCE.get(id);
				if (ed) {
					ed.setContent(text);
					jdiction_checkStatus(ed.getContent(), jQuery('#'+ed.id+'_status').prop('title'), id, ed.getContainer());
					return;
				}
			}
		}

		if (jQuery('#'+id+'_tbl').length > 0) { //Editor
			if (typeof tinymce != 'undefined') {
				tinymce.get(id).setContent(text);
				jdiction_checkStatus(tinymce.get(id).getContent(), jQuery('#'+ed.id+'_status').prop('title'), id, '#'+id+'_tbl');
			}
		} else {
			jdiction_checkStatus(text, jQuery('#'+id).val(), id,  '#'+id);
      jQuery('#'+id).val(text);
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
  echo JHtml::_('bootstrap.startTabSet', 'language-tabs', array('active' => 'language-tab-'.current($this->languages)->lang_code));
	foreach($this->languages as $language) :
		$this->form->loadLanguage($language->lang_code);
    echo JHtml::_('bootstrap.addTab', 'language-tabs', 'language-tab-'.$language->lang_code, JText::_($language->title_native, true));
    ?>
        <div class="row-fluid form-horizontal">
          <div class="span8">
        <?php
				$fieldsets = $this->form->getFieldsets();
				$tabs = (count($fieldsets) > 1);
        if ($tabs) {
					$fieldset = current($fieldsets);
					echo JHtml::_('bootstrap.startTabSet', 'language-'.$language->lang_code.'-fieldset-tabs', array('active' => 'language-'.$language->lang_code.'-tab-'.$fieldset->name));
				}
        foreach($fieldsets as $fieldset) :
          if ($tabs) {
						echo JHtml::_('bootstrap.addTab', 'language-'.$language->lang_code.'-fieldset-tabs', 'language-'.$language->lang_code.'-tab-'.$fieldset->name, JText::_($fieldset->label, true));
					}

          foreach($this->form->getFieldset($fieldset->name) as $field) : ?>
						<?php if (!$field->hidden) : ?>
							<div class="control-group" style="margin-bottom: 5px">
								<div class="control-label" style="padding-top: 0">
									<?php echo $field->label; ?>
									<div style="clear:both"></div>
									<?php switch ($field->type) {
										case 'Editor': ?>
										<img class="info_img" src="<?php echo JRoute::_('components/com_jdiction/assets/icon-16-del.png'); ?>" width="16" onClick="jdiction_removeContent('<?php echo $field->id; ?>', jQuery('#<?php echo $field->id; ?>').prev());" />
										<img class="info_img hasTooltip" src="<?php echo JRoute::_('components/com_jdiction/assets/icon-16-info.png'); ?>" width="16" title="<?php echo $this->escape($this->original->{$field->fieldname}); ?>" onClick="jdiction_copyContent(jQuery(this).data('original-title'),'<?php echo $field->id; ?>');" />
										<input type="hidden" id="<?php echo $field->id; ?>_status" name="<?php echo str_replace('jform', 'jdiction', $field->name); ?>" value="unchanged" title="<?php echo $this->escape($field->value); ?>" />
									<?php break;
										case 'JDMore':
											break;
										default: ?>
										<img class="info_img" src="<?php echo JRoute::_('components/com_jdiction/assets/icon-16-del.png'); ?>" width="16" onClick="jdiction_removeContent('<?php echo $field->id; ?>', jQuery('#<?php echo $field->id; ?>'));" />
										<img class="info_img hasTooltip" src="<?php echo JRoute::_('components/com_jdiction/assets/icon-16-info.png'); ?>" width="16" title="<?php echo $this->escape($this->original->{$field->fieldname}); ?>" onClick="jdiction_copyContent(jQuery(this).data('original-title'),'<?php echo $field->id; ?>');" />
										<input type="hidden" id="<?php echo $field->id; ?>_status" name="<?php echo str_replace('jform', 'jdiction', $field->name); ?>" value="unchanged" title="<?php echo $this->escape($field->value); ?>" />
									<?php } ?>
								</div>
            		<div class="controls">
          				<?php echo $field->input; ?>
          		</div>
            </div>
						<?php else: ?>
							<?php echo $field->input; ?>
							<input type="hidden" id="<?php echo $field->id; ?>_status" name="<?php echo str_replace('jform', 'jdiction', $field->name); ?>" value="unchanged" title="<?php echo $this->escape($field->value); ?>" />
						<?php endif; ?>
        <?php
          endforeach;

					if ($tabs) {  echo JHtml::_('bootstrap.endTab'); }
        endforeach;
				if ($tabs) { echo JHtml::_('bootstrap.endTabSet'); }
        ?>
            </div>
        </div>
    <?php
    echo JHtml::_('bootstrap.endTab');
  endforeach;
  echo JHtml::_('bootstrap.endTabSet');
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