<?php
/**
 * @version $Id: showcat.php 4336 2011-01-31 06:05:12Z severdia $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
 * Based on FireBoard Component
 * @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.bestofjoomla.com
 *
 * Based on Joomlaboard Component
 * @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @author TSMF & Jan de Graaff
 **/
// Dont allow direct linking
defined ( '_JEXEC' ) or die ();

?>
<!-- Pathway -->
<?php //$this->displayPathway () ?>
<!-- / Pathway -->

<?php $this->displaySubCategories () ?>

<div class="kblock">
	<div class="kheader">
		<h2><span><?php echo CKunenaLink::GetCategoryLink ( 'listcat', intval($this->objCatInfo->id), $this->escape($this->objCatInfo->name), 'follow' ); ?></span></h2>
	</div>
	
<?php if ($this->objCatInfo->headerdesc) : ?>
	<div class="kcontainer" id="frontstats_tbody">
		<div class="kbody">
			<div class="kfheadercontent">
				<?php echo KunenaParser::parseBBCode ( $this->headerdesc ); ?>
			</div>
		</div>
	</div>
<?php endif; ?>
</div>


<?php
	if ($this->catid) {

		$kunena_cansubscribecat = 0;
		if ($this->config->allowsubscriptions && $this->config->category_subscriptions != 'disabled' && ("" != $this->my->id || 0 != $this->my->id)) {
			if ($this->objCatInfo->subscribeid == '') {
				$kunena_cansubscribecat = 1;
			}
		}
		$subscribed = ($this->my->id != 0 && $this->config->allowsubscriptions && $this->config->category_subscriptions != 'disabled' && $kunena_cansubscribecat == 0);
	}
?>
<?php if ($this->thread_subscribecat) { ?>
<div class="kbutton-subscribe<?php echo ($subscribed)?' kbutton-subscribed':'' ?>"><?php 
	echo $this->thread_subscribecat;
?></div>
<?php } ?>
<?php if ($this->catid) { ?>
<div class="new-topic"><p class="readmore">
	<?php echo CKunenaLink::GetPostNewTopicLink ( $this->catid, CKunenaTools::showButton ( 'newtopic', JText::_('COM_KUNENA_BUTTON_NEW_TOPIC') ), 'nofollow', 'readmore btn-left', JText::_('COM_KUNENA_BUTTON_NEW_TOPIC_LONG') ); ?>
</p></div>
<?php } ?>	
<?php
// pagination top
if (count ( $this->messages ) > 0) {
	$maxpages = 9 - 2; // odd number here (# - 2)
	
	$pagination = $this->getPagination ( $this->catid, $this->page, $this->totalpages, $maxpages );
	
	$pagination = preg_replace('/<li[^>]*class="page"[^>]*>Page:<\/li>/','',$pagination);
	$pagination = preg_replace('/<li[^>]*class="active"[^>]*>(.*?)<\/li>/','<li class="active"><span class="active">$1</span></li>',$pagination);
}
?>
<?php if ($pagination) { ?>
<div class="klist-pages-all">
	<ul class="kpagination">
		<li class="next first-child">
			<?php if ($this->page < $this->totalpages) { ?>
			<?php echo CKunenaLink::GetCategoryPageLink ( 'showcat', $this->catid, $this->page+1, 'Next &gt;', $rel = 'follow' ); ?>
			<?php } else { ?>
			<span>Next &gt;</span>
			<?php } ?>
		</li>
		<li class="last last-child">
			<?php if ($this->page < $this->totalpages) { ?>
			<?php echo CKunenaLink::GetCategoryPageLink ( 'showcat', $this->catid, $this->totalpages, 'Last &gt;&gt;', $rel = 'follow' ); ?>
			<?php } else { ?>
			<span>Last &gt;&gt;</span>
			<?php } ?>
		</li>
	</ul>
	<?php echo $pagination; ?>
	<ul class="kpagination">
		<li class="first first-child">
			<?php if ($this->page > 1) { ?>
			<?php echo CKunenaLink::GetCategoryPageLink ( 'showcat', $this->catid, 1, '&lt;&lt; First', $rel = 'follow' ); ?>
			<?php } else { ?>
			<span>&lt;&lt; First</span>
			<?php } ?>
		</li>
		<li class="previous last-child">
			<?php if ($this->page > 1) { ?>
			<?php echo CKunenaLink::GetCategoryPageLink ( 'showcat', $this->catid, $this->page-1, '&lt; Previous', $rel = 'follow' ); ?>
			<?php } else { ?>
			<span>&lt; Previous</span>
			<?php } ?>
		</li>
	</ul>
	<div style="clear:both"></div>
</div>
<div style="clear:both"></div>
<?php } ?>

<!-- B: List Actions -->
<?php /*<table class="klist-actions">
	<tr>
		<td class="klist-actions-goto">
			<a name="forumtop"> </a>
			<?php echo CKunenaLink::GetSamePageAnkerLink ( 'forumbottom', CKunenaTools::showIcon ( 'kforumbottom', JText::_('COM_KUNENA_GEN_GOTOBOTTOM') ), 'nofollow', 'kbuttongoto') ?>
		</td>
		<td class="klist-actions-forum">
			<?php
			if (isset ( $this->forum_new ) || isset ( $this->forum_markread ) || isset ( $this->thread_subscribecat )) {
				echo '<div class="kmessage-buttons-row">';
				if (isset ( $this->forum_new ))
					echo $this->forum_new;
				if (isset ( $this->forum_markread ))
					echo ' ' . $this->forum_markread;
				if (isset ( $this->thread_subscribecat ))
					echo ' ' . $this->thread_subscribecat;
				echo '</div>';
			}
			?>
		</td>
		<td class="klist-pages-all">
			<?php
			// pagination 1
			if (count ( $this->messages ) > 0) {
				$maxpages = 9 - 2; // odd number here (# - 2)
				echo $pagination = $this->getPagination ( $this->catid, $this->page, $this->totalpages, $maxpages );
			}
			?>
		</td>
	</tr>
</table> */  ?>
<!-- F: List Actions -->

<?php $this->displayFlat (); ?>

<?php 
	$showcat = $this; 
	if (count ( $this->messages ) > 0) {
		$maxpages = 9 - 2; // odd number here (# - 2)
		$pagination = $this->getPagination ( $this->catid, $this->page, $showcat->totalpages, $maxpages );
		
		$pagination = preg_replace('/<li[^>]*class="page"[^>]*>Page:<\/li>/','',$pagination);
		$pagination = preg_replace('/<li[^>]*class="active"[^>]*>(.*?)<\/li>/','<li class="active"><span class="active">$1</span></li>',$pagination);
	}
	
?>
	<?php if ($pagination) {?>
	<div class="klist-pages-all">
		<ul class="kpagination">
			<li class="next first-child">
				<?php if ($showcat->page < $showcat->totalpages) { ?>
				<?php echo CKunenaLink::GetCategoryPageLink ( 'showcat', $this->catid, $showcat->page+1, 'Next &gt;', $rel = 'follow' ); ?>
				<?php } else { ?>
				<span>Next &gt;</span>
				<?php } ?>
			</li>
			<li class="last last-child">
				<?php if ($showcat->page < $showcat->totalpages) { ?>
				<?php echo CKunenaLink::GetCategoryPageLink ( 'showcat', $this->catid, $showcat->totalpages, 'Last &gt;&gt;', $rel = 'follow' ); ?>
				<?php } else { ?>
				<span>Last &gt;&gt;</span>
				<?php } ?>
			</li>
		</ul>
		<?php echo $pagination; ?>
		<ul class="kpagination">
			<li class="first first-child">
				<?php if ($showcat->page > 1) { ?>
				<?php echo CKunenaLink::GetCategoryPageLink ( 'showcat', $this->catid, 1, '&lt;&lt; First', $rel = 'follow' ); ?>
				<?php } else { ?>
				<span>&lt;&lt; First</span>
				<?php } ?>
			</li>
			<li class="previous last-child">
				<?php if ($showcat->page > 1) { ?>
				<?php echo CKunenaLink::GetCategoryPageLink ( 'showcat', $this->catid, $showcat->page-1, '&lt; Previous', $rel = 'follow' ); ?>
				<?php } else { ?>
				<span>&lt; Previous</span>
				<?php } ?>
			</li>
		</ul>
		<div style="clear:both"></div>
	</div>
	<div style="clear:both"></div>
	<?php } ?>

<?php /*
<!-- B: List Actions Bottom -->
<table class="klist-actions-bottom" >
	<tr>
		<td class="klist-actions-goto">
			<a name="forumbottom"> </a>
			<?php echo CKunenaLink::GetSamePageAnkerLink ( 'forumtop', CKunenaTools::showIcon ( 'kforumtop', JText::_('COM_KUNENA_GEN_GOTOBOTTOM') ), 'nofollow', 'kbuttongoto') ?>
		</td>
		<td class="klist-actions-forum">
			<?php
			if (isset ( $this->forum_new ) || isset ( $this->forum_markread ) || isset ( $this->thread_subscribecat )) {
				echo '<div class="kmessage-buttons-row">';
				if (isset ( $this->forum_new ))
					echo $this->forum_new;
				if (isset ( $this->forum_markread ))
					echo ' ' . $this->forum_markread;
				if (isset ( $this->thread_subscribecat ))
					echo ' ' . $this->thread_subscribecat;
			echo '</div>';
			}
			?>
		</td>
		<td class="klist-pages-all">
			<?php
			// pagination 2
			if (count ( $this->messages ) > 0) {
				echo $pagination;
			}
			?>
		</td>
	</tr>
</table>
<?php
echo '<div class = "kforum-pathway-bottom">';
echo $this->kunena_pathway1;
echo '</div>';
?>
<!-- B: List Actions Bottom -->
<div class="kcontainer klist-bottom">
	<div class="kbody">
		<div class="kmoderatorslist-jump fltrt">
				<?php $this->displayForumJump (); ?>
		</div>
		<?php if (!empty ( $this->modslist ) ) : ?>
		<div class="klist-moderators">
			<?php
			echo '' . JText::_('COM_KUNENA_GEN_MODERATORS') . ": ";
			foreach ( $this->modslist as $mod ) {
				echo CKunenaLink::GetProfileLink ( intval($mod->userid) ) . '&nbsp; ';
			}
			?>
		</div>
		<?php endif; ?>
	</div>
</div>
<!-- F: List Actions Bottom -->

*/ ?>