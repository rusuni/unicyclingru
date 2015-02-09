<?php
/**
* Kunena Component
* @package Kunena.Template.Fontaine
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
	// Load css from default template
	$template->addStyleSheet ( 'css/kunena.forum.css' );
	if ($skinner) {
		$template->addStyleSheet ( 'css/kunena.skinner.css' );
	} else {
		$template->addStyleSheet ( 'css/kunena.default.css' );
	}
}
$cssurl = JUri::root(true) . '/components/com_kunena/template/fontaine/css';
?>

<?php
$mediaurl = JUri::root(true) . "/components/com_kunena/template/{$template->name}/media";