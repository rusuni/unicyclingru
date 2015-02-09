<?php
/**
 * @package itrMail
 * @link http://joomla.itronic.at
 * @copyright	 Harald Leithner
*/
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
?>
<script type="text/javascript">
<!--
	Joomla.submitbutton = function(task) {
		if (task == 'tools.cancel' || document.formvalidator.isValid(document.id('upload-form'))) {
			Joomla.submitform(task, document.getElementById('upload-form')); 
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>'); 
		}
	};
// -->
</script>

<form action="<?php JRoute::_('index.php?option=com_jdiction'); ?>" method="post" name="adminForm" id="upload-form" class="form-validate"  enctype="multipart/form-data">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php echo JText::_("COM_JDICTION_TRANSLATION_UPLOAD"); ?></legend>
			<ul class="adminformlist">
				<?php 
				
					$fields = $this->form->getFieldset('details');
					foreach($fields as $field) {
						echo '<li>'.$field->label.$field->input.'</li>';
					}
				
					?>
			</ul>
			<div class="clr"> </div>
		</fieldset>
	</div>

<div class="clr"></div>
<input type="hidden" name="task" value="" />
<?php echo JHtml::_('form.token'); ?>
</form>
