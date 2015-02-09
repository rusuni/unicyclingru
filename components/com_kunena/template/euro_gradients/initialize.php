<?php
/**
* Kunena Component
* @package Kunena.Template.Blue_Eagle
*
* @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.org
**/
defined( '_JEXEC' ) or die();

$app = JFactory::getApplication();
$document = JFactory::getDocument();
$template = KunenaFactory::getTemplate();

// Template requires Mootools 1.2 framework
$template->loadMootools();

// We load mediaxboxadvanced library only if configuration setting allow it
if ( KunenaFactory::getConfig()->lightbox == 1 ) {
	$template->addStyleSheet ( 'css/mediaboxAdv.css');
	$template->addScript( 'js/mediaboxAdv.js' );
}

// New Kunena JS for default template
$template->addScript ( 'js/default.js' );

$rtl = JFactory::getLanguage()->isRTL();
$skinner = $template->params->get('enableSkinner', 0);

if (file_exists ( JPATH_ROOT . "/templates/{$app->getTemplate()}/css/kunena.forum.css" )) {
	// Load css from Joomla template
	CKunenaTools::addStyleSheet ( JUri::root(true). "/templates/{$app->getTemplate()}/css/kunena.forum.css" );
	if ($skinner && file_exists ( JPATH_ROOT. "/templates/{$app->getTemplate()}/css/kunena.skinner.css" )){
		CKunenaTools::addStyleSheet ( JUri::root(true). "/templates/{$app->getTemplate()}/css/kunena.skinner.css" );
	} elseif (!$skinner && file_exists ( JPATH_ROOT. "/templates/{$app->getTemplate()}/css/kunena.default.css" )) {
		CKunenaTools::addStyleSheet ( JUri::root(true). "/templates/{$app->getTemplate()}/css/kunena.default.css" );
	}
} else {
	$loadResponsiveCSS = $template->params->get('loadResponsiveCSS', 1);
	// Load css from default template
	$template->addStyleSheet ( 'css/kunena.forum.css' );
	if ($loadResponsiveCSS) $template->addStyleSheet ( 'css/kunena.responsive.css' );
	if ($skinner) {
		$template->addStyleSheet ( 'css/kunena.skinner.css' );
	} else {
		$template->addStyleSheet ( 'css/kunena.default.css' );
	}
}
$cssurl = JUri::root(true) . '/components/com_kunena/template/euro_gradients/css';
?>
<!--[if lte IE 7]>
<link rel="stylesheet" href="<?php echo $cssurl; ?>/kunena.forum.ie7.css" type="text/css" />
<![endif]-->
<?php
$mediaurl = JUri::root(true) . "/components/com_kunena/template/euro_gradients/media";

$styles = <<<EOF
	/* Kunena Custom CSS */
EOF;

$forumLink = $template->params->get('forumLinkcolor', $skinner ? '' : '#CC9933');

if ($forumLink) {
	$styles .= <<<EOF
	#Kunena a:link,
	#Kunena a:visited,
	#Kunena a:active {color: {$forumLink} !important;}
	#Kunena a:focus {outline: none;}
EOF;
}

$announcementHeader = $template->params->get('announcementHeadercolor', $skinner ? '' : '#5388B4');

if ($announcementHeader) {
	$styles .= <<<EOF
	#Kunena div.kannouncement div.kheader { background: {$announcementHeader} !important; }
EOF;
}

$announcementBox = $template->params->get('announcementBoxbgcolor', $skinner ? '' : '#FFFF73');

if ($announcementBox) {
	$styles .= <<<EOF
	#Kunena div#kannouncement .kanndesc { background: {$announcementBox}; }
EOF;
}


$inactiveTab = $template->params->get('inactiveTabcolor', $skinner ? '' : '#CC9933');

if ($inactiveTab) {
	$styles .= <<<EOF
	#Kunena #ktab a { background-color: {$inactiveTab} !important; }
EOF;
}

$activeTab = $template->params->get('activeTabcolor', $skinner ? '' : '#996633');

if ($activeTab) {
	$styles .= <<<EOF
	#Kunena #ktab ul.menu li.active a,#Kunena #ktab li#current.selected a { background-color: {$activeTab} !important; }
EOF;
}

$hoverTab = $template->params->get('hoverTabcolor', $skinner ? '' : '#996633');

if ($hoverTab) {
	$styles .= <<<EOF
	#Kunena #ktab a:hover { background-color: {$hoverTab} !important; }
EOF;
}

$topBorder = $template->params->get('topBordercolor', $skinner ? '' : '#996633');

if ($topBorder) {
	$styles .= <<<EOF
	#Kunena #ktop { border-color: {$topBorder} !important; }
EOF;
}

$inactiveFont = $template->params->get('inactiveFontcolor', $skinner ? '' : '#FFFFFF');

if ($inactiveFont) {
	$styles .= <<<EOF
	#Kunena #ktab a span { color: {$inactiveFont} !important; }
EOF;
}
$activeFont = $template->params->get('activeFontcolor', $skinner ? '' : '#FFFFFF');

if ($activeFont) {
	$styles .= <<<EOF
	#Kunena #ktab #current a span { color: {$activeFont} !important; }
EOF;
}

$toggleButton = $template->params->get('toggleButtoncolor', $skinner ? '' : '#996633');

if ($toggleButton) {
	$styles .= <<<EOF
	#Kunena #ktop span.ktoggler { background-color: {$toggleButton} !important; }
EOF;
}

$borderLightColor = $template->params->get('borderLightColor', $skinner ? '' : '#CC9933');

if ($borderLightColor) {
	$styles .= <<<EOF
	#Kunena td.kcol-mid { border-bottom-color: {$borderLightColor}; border-left-color: {$borderLightColor};}

	#Kunena td.kprofile-left {border-right-color: {$borderLightColor};}

	#Kunena td.kcol-first,
	#Kunena td.kprofile-left,
	#Kunena td.kprofile-right,
	#Kunena td.kbuttonbar-left,
	#Kunena td.kbuttonbar-right,
	#Kunena div.kmessage-editmarkup-cover,
	#Kunena div.kmsg-header
	{ border-bottom-color: {$borderLightColor};}

	#Kunena div.kblock div.kbody,
	#Kunena .klist-markallcatsread,
	#Kunena .klist-actions,
	#Kunena .klist-bottom
	{ border-color: {$borderLightColor};}

	#Kunena .kforum-pathway {border-right-color: {$borderLightColor}; border-left-color: {$borderLightColor};}
	#Kunena .klist-actions-forum,
	#Kunena .klist-times-all,
	#Kunena .klist-jump-all,
	#Kunena .klist-pages-all
	{border-left-color: {$borderLightColor};}

	.kunena-profile-bottom { border-bottom: 1px solid {$borderLightColor};}
EOF;
}

$borderDarkColor = $template->params->get('borderDarkColor', $skinner ? '' : '#996633');

if ($borderDarkColor) {
	$styles .= <<<EOF
	#Kunena div.kblock { border-bottom-color: {$borderDarkColor};}
EOF;
}

$profileIcons = $template->getFile("media/iconsets/profile/{$template->params->get('profileIconset', 'default')}/default.png", true);
$buttonIcons = $template->getFile("media/iconsets/buttons/{$template->params->get('buttonIconset', 'default')}/default.png", true);
$editorIcons = $template->getFile("media/iconsets/editor/{$template->params->get('editorIconset', 'default')}/default.png", true);
$forumHeaderImg = $template->getFile("images/gradients/{$template->params->get('forumHeaderImg', 'default')}", true);

$frontstatsHeaderImg = $template->getFile("images/gradients/{$template->params->get('frontstatsHeaderImg', 'default')}", true);

$whoisonlineHeaderImg = $template->getFile("images/gradients/{$template->params->get('whoisonlineHeaderImg', 'default')}", true);

$styles .= <<<EOF
	#Kunena .kicon-profile { background-image: url("{$profileIcons}"); }
	#Kunena .kicon-button { background-image: url("{$buttonIcons}") !important; }
	#Kunena #kbbcode-toolbar li a,#Kunena #kattachments a { background-image:url("{$editorIcons}"); }
	#Kunena #Kunena div.kblock > div.kheader, #Kunena .kblock div.kheader {background:url("{$forumHeaderImg}") repeat-x scroll 0 center transparent !important;}
	#Kunena div.kfrontstats div.kheader {background:url("{$frontstatsHeaderImg}") repeat-x scroll 0 center transparent !important;}
	#Kunena div.kwhoisonline div.kheader {background:url("{$whoisonlineHeaderImg}") repeat-x scroll 0 center transparent !important;}
	/* End of Kunena Custom CSS */
EOF;

$document->addStyleDeclaration($styles);