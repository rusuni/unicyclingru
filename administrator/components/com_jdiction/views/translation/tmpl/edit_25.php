<?php
/**
 * @package jDiction
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
	function submitbutton(task)	{
		if (task == 'translation.cancel' || document.formvalidator.isValid(document.id('translation-form'))) {
			submitform(task);
		}
	}
// -->
</script>

<?php require dirname(__FILE__).'/form_25.php'; ?>