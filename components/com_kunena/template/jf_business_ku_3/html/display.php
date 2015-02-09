<?php
/**
 * Kunena Component
 * @package Kunena.Template.Blue_Eagle
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div id="Kunena" class="layout container-fluid">
<?php
if ($this->ktemplate->params->get('displayMenu', 1)) {
	$this->displayMenu ();
}
$this->displayLoginBox ();
$this->displayBreadcrumb ();

// Display current view/layout
$this->displayLayout();

$this->displayBreadcrumb ();
$this->displayFooter ();
?>
<?php if ($this->ktemplate->params->get('jf_ku_JFlogo')==1) { ?>
	<div class="jf_ku_jflogo">
		<a href="<?php echo $this->ktemplate->params->get('jf_ku_JFlogoLink'); ?>" target="_blank" data-toggle="tooltip" data-placement="bottom" title="<?php echo $this->ktemplate->params->get('jf_ku_JFlogoTitle'); ?>"></a>
	</div>
<?php } ?>
</div>
