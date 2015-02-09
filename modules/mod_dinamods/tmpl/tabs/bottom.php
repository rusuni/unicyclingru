<?php
/*------------------------------------------------------------------------
# mod_dinamods - Dinamod Tab Modules
# ------------------------------------------------------------------------
# author    Joomla!Vargas
# copyright Copyright (C) 2010 joomla.vargas.co.cr. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://joomla.vargas.co.cr
# Technical Support:  Forum - http://joomla.vargas.co.cr/forum
-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die; 
?>

<div id="dm_container_<?php echo $dinamods_id; ?>">
  <?php
$k=1;
foreach ( $dinamods as $dinamod ) { ?>
  <div id="dm_tab_<?php echo $dinamods_id; ?>_<?php echo $k; ?>" class="dm_tabcontent">
    <?php echo JModuleHelper::renderModule($dinamod, array('style' => $params->get('chrome', '')) ); ?>
  </div>
  <?php
$k++;
}
?>
</div>
<div id="dm_tabs_<?php echo $dinamods_id; ?>">
  <ul class="dm_menu_<?php echo $dinamods_id; ?>">
    <?php
$k=1;
foreach ( $dinamods as $dinamod ) {
	if ($params->get('tracker', 0)) {
		$href = '/' . $dinamod->id . ':' . JFilterOutput::stringURLSafe( $dinamod->title );
	} else {
		$href = '#';
	}
?>
    <li class="dm_menu_item_<?php echo $dinamods_id; ?> <?php echo JFilterOutput::stringURLSafe( $dinamod->title ); ?>"><a href="<?php echo $href; ?>" rel="dm_tab_<?php echo $dinamods_id; ?>_<?php echo $k; ?>"<?php echo $k == 1 ? ' class="dm_selected"' : ''; ?>><?php echo $dinamod->title; ?></a></li>
    <?php
$k++; 
} ?>
  </ul>
</div>
<br style="clear:left;" />