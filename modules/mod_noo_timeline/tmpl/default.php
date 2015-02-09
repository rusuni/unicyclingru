<?php
/**
 * @version		$Id$
 * @author		NooTheme
 * @package		Joomla.Site
 * @subpackage	mod_noo_timeline
 * @copyright	Copyright (C) 2013 NooTheme. All rights reserved.
 * @license		License GNU General Public License version 2 or later; see LICENSE.txt, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die('Restricted access');
$display = $params->get('display');
?>
<div id="noo_tl<?php echo $module->id ?>" class="noo-tl<?php echo $params->get('moduleclass_sfx');?>">
	<div class="noo-tl-wrap clearfix">
		<?php $i = 0;?>
		<?php foreach ($lists as $list){?>
			<div class="noo-tl-item <?php if ((!$display && $i==0 ) || ($display == 1)){?> selected<?php }?><?php if ($i++ % 2 == 0 ){?> right<?php }else{?> left<?php }?>">
				<div class="noo-tl-control"></div>
				<div class="noo-tl-time"><?php echo $list['frame']?></div>
				<div class="noo-tl-info">
					<div class="arrow"></div>
					<h2 class="noo-tl-title"><a href="#" title="<?php echo $list['title']?>"><?php echo $list['title']?></a></h2>
					<div class="noo-tl-desc" <?php if ((!$display && $i==1 ) || ($display == 1)){?> style="display:block"<?php }?>><?php echo $list['description']?></div>
				</div>
			</div>
			<div class="clearfix"></div>
		<?php }?>
	</div>
</div>