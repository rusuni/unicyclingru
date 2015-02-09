<?php
/**
 * @package jDiction
 * @link http://joomla.itronic.at
 * @copyright	 Harald Leithner
*/
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.formvalidation');
?>
<script type="text/javascript">

	Joomla.submitbutton = function(task) {
		if (task == 'translation.cancel' || document.formvalidator.isValid(document.id('translation-form'))) {
			/*
			 if (tinyMCE.get("jform_articletext").isHidden()) {
			 tinyMCE.get("jform_articletext").show()
			 }
			 tinyMCE.get("jform_articletext").save();
			 */
			Joomla.submitform(task, document.getElementById('translation-form'));
		}
	};

  setTimeout(function(){
    jQuery('.alert').fadeOut();
  }, 5000);
</script>

<?php require dirname(__FILE__).'/form.php'; ?>