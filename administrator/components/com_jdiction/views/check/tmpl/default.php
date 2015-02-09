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
<table class="table table-striped">
  <thead>
    <tr>
      <th width="250"><?php echo JText::_('COM_JDICTION_CHECK_TEST'); ?></th>
      <th><?php echo JText::_('COM_JDICTION_CHECK_RESULT'); ?></th>
    </tr>
  </thead>
  <tfoot>
  <tr>
    <td colspan="2">&#160;
    </td>
  </tr>
  </tfoot>
  <tbody>
  <?php foreach($this->status as $k => $v): ?>
  <tr>
    <td>
      <strong class="hasTip" title="<?php echo JText::_($k.'_DESC'); ?>"><?php echo JText::_($k.'_LABEL'); ?></strong>
    </td>
    <td><?php if (is_array($v)): ?>
      <ul class="unstyled">
        <?php foreach($v as $v2): ?>
        <li><?php echo $v2; ?></li>
        <?php endforeach; ?>
      </ul>
      <?php else: ?>
        <?php echo $v; ?>
      <?php endif; ?>
      </td>
  </tr>
  <?php endforeach; ?>
  </tbody>
</table>
