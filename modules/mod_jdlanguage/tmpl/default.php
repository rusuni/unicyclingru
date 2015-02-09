<?php
/**
 * @version		$Id$
 * @package		Joomla.Site
 * @subpackage	mod_languages
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$style = explode('_', $params->get('layout_style', 'text'));

if (in_array('flag', $style)):
  $left = (array_search('flag', $style) == 0 ? true : false);
?>
<style>
  .languageswitcher a {
    padding-<?php echo $left ? 'left' : 'right'; ?>: 20px;
    background-position: center <?php echo $left ? 'left' : 'right'; ?>;
    background-repeat: no-repeat;
  }
</style>
<?php endif; ?>
<div class="languageswitcher<?php echo $class_sfx;?>"<?php
$tag = '';
if ($params->get('tag_id') != null) {
  $tag = $params->get('tag_id').'';
  echo ' id="'.$tag.'"';
}
?>>
<?php foreach($languages as $language):?>
	<a href='<?php echo JRoute::_($language->link); ?>' target='_self' class='jDlang lang <?php echo $language->lang_code; ?><?php echo ($language->active ? ' active' : ''); ?>'
     title="<?php echo $language->title_native." - ".$language->menutitle; ?>"
    <?php if (in_array('flag', $style)): ?>
    style="background-image: url(<?php echo JRoute::_('media/com_jdiction/images/flags/'.$language->image.'.png'); ?>);"
    <?php endif; ?>><?php
      if (in_array('text', $style)):
        echo $language->title_native;
      endif;
    ?></a>
<?php endforeach; ?>
</div>
<?php if ($params->get('update_hash', true)): ?>
<script>
  function jdUpdateHash() {
    var nodes = document.querySelectorAll('.jDlang');
    for (var i = 0, n = nodes.length; i < n; i++) {
      var pos = nodes[i].href.indexOf('#');
      if (pos > -1) {
        nodes[i].href = nodes[i].href.substr(0, pos)+window.location.hash;
      } else {
        nodes[i].href = nodes[i].href+window.location.hash;
      }
    }
  }
  if (window.addEventListener) {
    window.addEventListener('hashchange', jdUpdateHash);
  } else {
    window.attachEvent('hashchange', jdUpdateHash);
  }
  jdUpdateHash();
</script>
<?php endif ;?>