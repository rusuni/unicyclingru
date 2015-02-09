<?php
/**
 * @package		AJAX Toggler
 * @copyright	Copyright (C) 2009 - 2013 AlterBrains.com. All rights reserved.
 * @license		GNU/GPL, see LICENSE.txt
 */
defined('_JEXEC') or die;

if (JFactory::getApplication()->isAdmin() && JFactory::getUser()->id)
{
	class plgSystemAjaxtoggler extends JPlugin
	{
		private $active 	= false;
		private $messages 	= null;
	
		public function __construct(&$subject, $config)
		{
			parent::__construct($subject, $config);
			
			$this->input = JFactory::getApplication()->input;
			
			if ($this->params->get('exclude') && in_array($this->input->get('option'), $this->params->get('exclude'))) {
				return;
			}
	
			$this->active = true;

			$session = JFactory::getSession();
			
			// Lists
			if ($this->input->getBool('jatoggler')) {
				$session->set('jatoggler', 1);
				
				// we should normal redirect in $app->redirect, else we fail with stupid IE, spent 4!!!!! hours finding this issue
				jimport('joomla.environment.browser');
				$navigator = JBrowser::getInstance();
				if ($navigator->isBrowser('msie')) {
					$navigator->setBrowser('chrome');
				}
			}
		}
	
		public function onAfterRoute()
		{
			if (!$this->active) {
				return;
			}

			// Remove MooTools
			/*
			JHtml::_('behavior.framework');
			$doc = Jfactory::getdocument();
			unset($doc->_scripts['/media/system/js/mootools-core-uncompressed.js']);
			*/
			
			JHtml::_('jquery.framework');
			
			JFactory::getDocument()->addScriptDeclaration('jQuery(document).ready(function(){AjaxToggler.initialize({base:"'.JUri::root().'"})});');
			JFactory::getDocument()->addScript(JUri::root(true).'/plugins/system/ajaxtoggler/ajaxtoggler.js?v=4');
		}
		
		public function onAfterDispatch()
		{
			if (!$this->active) {
				return;
			}
			
			$session = JFactory::getSession();
			 
			if ($session->get('jatoggler')) {
				$session->set('jatoggler', 0);
				
				if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
					header('Content-Type: text/html; charset=utf-8');
					echo $this->getTable() . $this->getMessages() . $this->getToolbar() . $this->getPagination() . $this->getOrdering();
					exit();
				}
			 }
		}
		
		private function getTable()
		{
			$buf = '';
			
			// We have table
			if (preg_match_all('/(<table.*<\/table>)/Us', JFactory::getDocument()->getBuffer('component'), $m)) {
				$m = $m[1];
				for($i=0, $c=sizeof($m); $i<$c; $i++) {
					// Usual Joomla table
					if (JString::strpos($m[$i], ' class="table table-striped')) {
						$buf = $m[$i];
						break;
					}
					// K2
					if (JString::strpos($m[$i], ' class="adminlist table table-striped')) {
						$buf = $m[$i];
						break;
					}
				}
			}
			
			// No results! But keep our wrapper ID!
			else {
				$buf = '<div id="ajaxtogglerWrapper" class="alert alert-no-items">'.JText::_('JGLOBAL_NO_MATCHING_RESULTS').'</div>';
			}
			
			// small house-keeping
			$buf = strtr($buf, array(
				"\r" => '',
				"\n" => '',
				"\t" => ''
			));
			
			return $buf;
		}
		
		private function getMessages()
		{
			$messages = '';
			
			$this->messages = JFactory::getSession()->get('application.queue', array());
			
			if (is_array($this->messages) && !empty($this->messages)) {
				$messages = JFactory::getDocument()->getBuffer('message');
				$messages = JString::str_ireplace('<div id="system-message-container">', '<div>', $messages);
				$messages = '<div id="jatogglerMessages" style="display:none">' . $messages . '</div>';
			}
			return $messages;
		}
		
		private function clearMessages()
		{
			JFactory::getApplication()->getMessageQueue();
		}
		
		private function getToolbar()
		{
			$toolbar = '';
			
			// Reload toolbar if Trashed state changed
			$state1 	= JFactory::getSession()->get('jatoggler_filter_published');
			$state2		= $this->input->get('filter_published', $this->input->get('filter_state'));

			// New Joomla 3.2.1+ filter
			$filter = $this->input->getVar('filter', 'array');
			if (isset($filter['published'])) {
				$state2 = $filter['published'];
			}
			
			if (
				// Reload toolbar for selected components;
				($this->params->get('toolbar') && in_array($this->input->get('option'), $this->params->get('toolbar')))
				||
				// Reload toolbar if Trashed state changed
				($state1 == -2 || $state2 == -2) && ($state1 != $state2)
			) {
				JFactory::getSession()->set('jatoggler_filter_published', $state2);
				
				// render toolbar
				jimport('joomla.html.toolbar');

				$toolbar = JToolBar::getInstance('toolbar')->render('toolbar');
				$toolbar = JString::str_ireplace('<div class="btn-toolbar" id="toolbar">', '<div>', $toolbar);
				$toolbar = '<div id="jatogglerToolbar" style="display:none">'.$toolbar.'</div>';
			}
			return $toolbar;
		}
		
		private function getPagination()
		{
			$pagination = '';
			
			if (preg_match_all('/(<ul class="pagination-list">.*<\/ul>)/Us', JFactory::getDocument()->getBuffer('component'), $m)) {
				if (isset($m[0][0]) && $m[0][0]) {
					$pagination = $m[0][0] . '<input type="hidden" value="'.$this->input->getInt('limitstart').'" name="limitstart" />';
				}
			}
			return '<div id="jatogglerPagination" style="display:none">'.$pagination.'</div>';
		}

		private function getOrdering()
		{
			$ordering = JFactory::getApplication()->input->get('filter_order');

			// New Joomla 3.2.1+ list
			$list = $this->input->getVar('list', 'array');
			if (isset($list['fullordering'])) {
				$ordering = strtr($list['fullordering'], array(
					' ASC' 	=> '',
					' DESC' => '',
				));
			}
			
			if (in_array($ordering, array('a.ordering', 'ordering', 'a.lft', 'lft'))) {
				$doc = JFactory::getDocument();
				if (isset($doc->_script['text/javascript']) && $doc->_script['text/javascript']) {
					preg_match_all('/var sortableList = ([^;]*);/', $doc->_script['text/javascript'], $matches);
					
					if (isset($matches[0][0]) && $matches[0][0]) {
						return '<script>(function (jQuery){ '.str_replace('$.JSortableList', 'jQuery.JSortableList', $matches[0][0]).' })(jQuery);</script>';
					}
				}
			}
		}
	}
}
