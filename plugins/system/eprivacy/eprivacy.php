<?php

/**
 * @package plugin System - EU e-Privacy Directive
 * @copyright (C) 2010-2011 RicheyWeb - www.richeyweb.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * System - EU e-Privacy Directive Copyright (c) 2011 Michael Richey.
 * System - EU e-Privacy Directive is licensed under the http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

/**
 * ePrivacy system plugin
 */
class plgSystemePrivacy extends JPlugin {
    public $_cookieACL;
    public $_defaultACL;
    public $_prep;
    public $_eprivacy;
    public $_clear;
    public $_country;
    public $_display;
    public $_displayed;
    public $_displaytype;
    public $_config;
    public $_exit;
    public $_eu;
    public $_version;
    public $_app;
    public $_doc;

    public function __construct(&$subject, $config) {
        $this->_cookieACL=false;
        $this->_defaultACL= false;
        $this->_groupadded = false;
        $this->_prep = false;
        $this->_eprivacy = false;
        $this->_clear = array();
        $this->_country = false;
        $this->_display = true;
        $this->_displayed = false;
        $this->_displaytype = 'message';
        $this->_exit = false;
        $this->_eu=array(
            /* special cases - we run these just to be safe */
            'Anonymous Proxy','Satellite Provider',
            /* member states */
            'Austria','Belgium','Bulgaria','Cyprus','Czech Republic','Denmark','Estonia','Finland','France','Germany',
            'Greece','Hungary','Ireland','Italy','Latvia','Lithuania','Luxembourg','Malta','Netherlands','Poland',
            'Portugal','Romania','Slovakia','Slovenia','Spain','Sweden','United Kingdom',
            /* overseas member state territories */
            'Virgin Islands, British'/*United Kingdom*/,
            'French Guiana','Guadeloupe','Martinique','Reunion'/*France*/
        );
        $this->_version = JVERSION;
        $this->_app = JFactory::getApplication();
        $this->_doc = JFactory::getDocument();
        parent::__construct($subject, $config);
    }
    public function onAjaxEprivacy() {
        $jinput = $this->_app->input;
        switch($jinput->get('task',false)) {
            case 'accept':
                $return = $this->_getAccept(true);
                break;
            case 'unaccept':
                $return = $this->_getDecline(true);
                break;
            default: // false
                $return = array('result'=>false);
                break;
        }
        return $return;
    }
    public function onAfterInitialise() {
        if ($this->_exitEarly(true)) return;

        $this->_displaytype = $this->params->get('displaytype','message');
        $userconfig = JComponentHelper::getParams('com_users');
        $this->_defaultACL = $userconfig->get('guest_usergroup',1);
        $this->_cookieACL = $this->params->get('cookieACL',$userconfig->get('guest_usergroup',1));

        // this shouldn't affect logged in users
        if($this->_isGuest()) return;

        // guest accepted - yay
        if($this->_getAccept()) return;

        // guest/user just declined
        if($this->_getDecline()) return;

        // guests who have accepted
        if ($this->_app->getUserState('plg_system_eprivacy', false)) {
            $this->_groupadded = true;
            $this->_display = false;
            $this->_eprivacy = true;
            return;
        }

        // guests who have already accepted and have a cookie
        if($this->_hasLongTermCookie()) return;


        // are they in a country where eprivacy is required?
        if($this->params->get('geoplugin',false)) {
            $this->_useGeoPlugin();
        } else {
            $this->_app->setUserState('plg_system_eprivacy_non_eu',false);
        }

        if(!$this->_eprivacy) {
            $this->_cleanHeaders();
        }
        return true;
    }
    public function onBeforeCompileHead() {
        if ($this->_exitEarly()) return true;
        $this->_pagePrepJS($this->_displaytype,$this->_display);
        $this->_requestAccept();
        if(!$this->_eprivacy) $this->_cleanHeaders();
        // did the user just decline
        $this->_getDecline();
        return true;
    }
    public function onBeforeRender() {
        if ($this->_exitEarly()) return true;
        // because JAT3 is lame!
        $this->onBeforeCompileHead();
    }
    public function onAfterRender() {
        if ($this->_exitEarly()) return true;
        if(!$this->_eprivacy) $this->_cleanHeaders();
        // did the user just decline
        $this->_getDecline();
        return true;
    }
    private function _cleanHeaders() {
        $hasheaders = false;
        foreach (headers_list() as $header) {
            if($hasheaders) continue;
            if (preg_match('/Set-Cookie/', $header)) {
                $hasheaders = true;
            }
        }
        if(!$hasheaders) return;
        if(version_compare(phpversion(),5.3,'>=')) {
            header_remove('Set-Cookie');
        } else {
            header('Set-Cookie:');
        }
    }
    private function _requestAccept() {
        if(JFactory::getUser()->id) return true;
        switch($this->params->get('displaytype','message')) {
            case 'message':
                if($this->_display && !$this->_displayed) {
                    $this->_displayed=true;
                    $msg = $this->_setMessage();
                    $this->_app->enqueueMessage($msg, $this->params->get('messagetype','message'));
                }
                break;
            default:
                break;
        }
    }
    private function _pagePrepJS($type,$autoopen=true){
        if($this->params->get('jsfw','mootools') == 'mootools') {
            JHtml::_('behavior.framework',true);
            $loadscripts = array('mthash.js','eprivacy.js');
        } else {
            $loadscripts = array();
            if(version_compare(JVERSION, 3, '>=')) {
                JHtml::_('jquery.framework',true,true);
                $loadscripts = array_merge($loadscripts,array('jqclass.js','jqurl.js','jqeprivacy.js'));
//                if($this->params->get('longtermcookie',false)) $loadscripts[]='jquery-cookie.js';
            }
        }
        foreach($loadscripts as $loadscript) 
            $this->_doc->addScript(JURI::root(true).'/media/plg_system_eprivacy/js/'.$loadscript);
        $this->loadLanguage('plg_system_eprivacy');
        if($this->_prep) return;
        $options = array('displaytype'=>$type,'autoopen'=>($autoopen?true:false),'accepted'=>($this->_eprivacy?true:false));
        if($this->_config['geopluginjs']===true) {
            $options['geopluginjs']=true;
            $options['eu']=$this->_eu;
            $this->_doc->addScript('http://www.geoplugin.net/javascript.gp');
        }
        if(in_array($type,array('message','confirm','module','modal','ribbon'))) {
            $this->_getCSS('module');
            $this->_jsStrings($type);
        }
        switch($type) {
            case 'message':
            case 'confirm':
            case 'module':
                break;
            case 'modal':
                JHtml::_('behavior.framework',true);
                $this->_doc->addScript(JURI::root(true).'/media/plg_system_eprivacy/js/mthash.js');
                JHtml::_('behavior.modal');
                $options['policyurl']=$this->params->get('policyurl','');
                $options['modalclass']=$this->params->get('modalclass','');
                $options['modalwidth']=$this->params->get('modalwidth',600);
                $options['modalheight']=$this->params->get('modalheight',400);
                if($this->params->get('lawlink',1)) {
                    $url=$this->_getLawLink();
                } else {
                    $url='';
                }
                $options['lawlink']=$url;
                break;
            case 'ribbon':
                $this->_getCSS('ribbon');
                $options['policyurl']=$this->params->get('policyurl','');
                if($this->params->get('lawlink',1)) {
                    $url=$this->_getLawLink();
                } else {
                    $url='';
                }
                $options['lawlink']=$url;
                break;
            case 'cookieblocker';
                break;
        }
        $options['version']=JVERSION;
        $options['root']=JURI::root();
        $this->_doc->addStyleDeclaration("\n#plg_system_eprivacy { width:0px;height:0px;clear:none; BEHAVIOR: url(#default#userdata); }\n");
        $this->_doc->addScriptDeclaration("\nwindow.plg_system_eprivacy_options = ".json_encode($options).";\n");
        $this->_prep = true;
    }
    private function _getLawLink() {
        $lang = explode('-',JFactory::getLanguage()->getTag());
        $langtag = strtoupper($lang[0]);
        $linklang = 'EN';
        if(in_array($langtag,array('BG','ES','CS','DA','DE','ET','EL','EN','FR','GA','IT','LV','LT','HU','MT','NL','PL','PT','RO','SK','SL','FI','SV'))) {
            $linklang = $langtag;
        }
        $url='http://eur-lex.europa.eu/LexUriServ/LexUriServ.do?uri=CELEX:32002L0058:'.$linklang.':NOT';
        return $url;
    }
    private function _setMessage() {
        $msg = '<div class="plg_system_eprivacy_message">';
        $msg.= '<h2>'.JText::_('PLG_SYS_EPRIVACY_MESSAGE_TITLE').'</h2>';
        $msg.= '<p>'.JText::_('PLG_SYS_EPRIVACY_MESSAGE').'</p>';

        if(strlen(trim($this->params->get('policyurl','')))) {
            $msg.= '<p><a href="'.trim($this->params->get('policyurl','')).'">'.JText::_('PLG_SYS_EPRIVACY_POLICYTEXT').'</a></p>';
        }
        if($this->params->get('lawlink',1)) {
            $msg.= '<p><a href="'.$this->_getLawLink().'" onclick="window.open(this.href);return false;">'.JText::_('PLG_SYS_EPRIVACY_LAWLINK_TEXT').'</a></p>';
        }

        $msg.= '<button class="plg_system_eprivacy_agreed">' . JText::_('PLG_SYS_EPRIVACY_AGREE') . '</button>';
        $msg.= '<button class="plg_system_eprivacy_declined">' . JText::_('PLG_SYS_EPRIVACY_DECLINE') . '</button>';
        $msg.= '<div id="plg_system_eprivacy"></div>';
        $msg.= '</div>';
        $msg.= '<div class="plg_system_eprivacy_declined">';
        $msg.= JText::_('PLG_SYS_EPRIVACY_DECLINED');
        $msg.= '<button class="plg_system_eprivacy_reconsider">' . JText::_('PLG_SYS_EPRIVACY_RECONSIDER') . '</button>';
        $msg.= '</div>';
        return $msg;
    }
    private function _useGeoPlugin() {
        require_once(JPATH_ROOT.DS.'plugins'.DS.'system'.DS.'eprivacy'.DS.'geoplugin'.DS.'geoplugin.class.php');
        if(function_exists('curl_init') || ini_get('allow_url_fopen')) {
            $geoplugin = new geoPlugin();
            $geoplugin->locate();
            if(!in_array(trim($geoplugin->countryName),$this->_eu)) {
                $this->_eprivacy = true;
                $this->_display = false;
                $this->_addViewLevel();
                JFactory::getApplication()->setUserState('plg_system_eprivacy',true);
                JFactory::getApplication()->setUserState('plg_system_eprivacy_non_eu',true);
            } else {
                JFactory::getApplication()->setUserState('plg_system_eprivacy_non_eu',false);
                $this->_country = trim($geoplugin->countryName);
                $this->_eprivacy = false;
                $this->_display = true;
            }
        } else {
            $this->_eprivacy = false;
            $this->_country = 'Geoplugin JS: Country Not Available to PHP';
            $this->_config = array('geopluginjs'=>true);
        }
    }
    private function _jsStrings($type){
        $strings = array(
            'message'=>array('CONFIRMUNACCEPT'),
            'module'=>array('CONFIRMUNACCEPT'),
            'modal'=>array('MESSAGE_TITLE','MESSAGE','POLICYTEXT','LAWLINK_TEXT','AGREE','DECLINE','CONFIRMUNACCEPT'),
            'confirm'=>array('MESSAGE','JSMESSAGE','CONFIRMUNACCEPT'),
            'ribbon'=>array('MESSAGE','POLICYTEXT','LAWLINK_TEXT','AGREE','DECLINE','CONFIRMUNACCEPT')
        );
        foreach($strings[$type] as $string)
            JText::script('PLG_SYS_EPRIVACY_'.$string);
    }
    private function _getAccept($ajax=false) {
        $jinput = $this->_app->input;
        if ($ajax || $jinput->get('eprivacy', false)) {
            $this->_addViewLevel();
            $this->_eprivacy = true;
            $this->_display = false;
            $this->_app->setUserState('plg_system_eprivacy', true);
            $return = array('accept'=>true);
            if($this->params->get('longtermcookie',0)) {
                $config = JFactory::getConfig();
                $name = 'plg_system_eprivacy';
                $value = date('Y-m-d');
                $expires=time()+60*60*24*(int)$this->params->get('longtermcookieduration',30);
                $path=strlen($config->get('cookie_path'))?$config->get('cookie_path'):'/';
                $domain=strlen($config->get('cookie_domain'))?$config->get('cookie_domain'):$_SERVER['HTTP_HOST'];
                if($ajax) {
                    $return['cookie']=array($name,$value,$expires,$path,$domain,false,false);
                } else {
                    $jinput->cookie->set($name,$value,$expires,$path,$domain,false,false);
                }
            }
            if($this->params->get('logaccept',false)) {
                $db = JFactory::getDbo();
                $query = $db->getQuery(true);
                $query->insert('#__plg_system_eprivacy_log');
                $query->columns('ip,state,accepted');
                $values=array();
                $values['ip']=$db->quote($_SERVER['REMOTE_ADDR']);
                $values['state']=$db->quote(($this->_country?$this->_country:'not detected'));
                $values['accepted']=$db->quote(version_compare(JVERSION,3,'>=')?JFactory::getDate()->toSql():JFactory::getDate()->toMySQL());
                $query->values(implode(',',$values));
                $db->setQuery($query);
                $db->execute();
            }
            return $ajax?$return:true;
        }
        return false;
    }
    private function _getDecline($ajax=false) {
        $jinput = $this->_app->input;
        if($ajax || $jinput->get('eprivacy_decline',false)) {
            $this->_app->setUserState('plg_system_eprivacy',false);
            $this->_addViewLevel('remove');
            $this->_eprivacy = false;
            $this->_display = true;
            $this->_cleanHeaders();
            $return = array('unaccept'=>true);
            if (isset($_SERVER['HTTP_COOKIE'])) {
                $return['cookies']=array();
                $config = JFactory::getConfig();
                $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
                $cookie_path = strlen($config->get('cookie_path'))?$config->get('cookie_path'):'/';
                $cookie_domain = strlen($config->get('cookie_domain'))?$config->get('cookie_domain'):$_SERVER['HTTP_HOST'];
                foreach($cookies as $cookie) {
                    $parts = explode('=', $cookie);
                    $name = trim($parts[0]);
                    $jinput->cookie->set($name, null, -3600);
                    $jinput->cookie->set($name, null, -3600, $cookie_path,$cookie_domain);
                    unset($_COOKIE[$name]);
                    $return['cookies'][]=$name;
                }
            }
            return $ajax?$return:true;
        }
        return false;
    }
    private function _exitEarly($initialise = false) {
        if($this->_exit) return true;
        // plugin should only run in the front-end
        if($this->_app->isAdmin()) {
            $this->_exit = true;
            return true;
        }

        // plugin should only run in HTML pages
        if(!$initialise){
            if($this->_doc->getType()!='html') {
                $this->_exit = true;
                return true;
            }
        }
        // shouldn't run in raw output
        if($this->_app->input->get('format','','cmd') == 'raw') {
            $this->_exit = true;
            return true;
        }
        return false;
    }
    private function _isGuest() {
        $user = JFactory::getUser();
        if(!$user->guest) {
            $this->_addViewLevel();
            $this->_display = false;
            $this->_eprivacy = true;
            return true;
        }
        return false;
    }
    private function _hasLongTermCookie() {
        if($this->params->get('longtermcookie',false)) {
            $accepted = $this->_app->input->cookie->get('plg_system_eprivacy',false);
            if($accepted) {
                $config = JFactory::getConfig();
                $this->_addViewLevel();
                $this->_eprivacy = true;
                $this->_display = false;
                $cookie_path = strlen($config->get('cookie_path'))?$config->get('cookie_path'):'/';
                $cookie_domain = strlen($config->get('cookie_domain'))?$config->get('cookie_domain'):$_SERVER['HTTP_HOST'];
                $this->_app->input->cookie->set('plg_system_eprivacy',$accepted,time()+60*60*24*(int)$this->params->get('longtermcookieduration',30), $cookie_path,$cookie_domain);
                return true;
            }
        }
        return false;
    }
    private function _reflectJUser($remove=false) {
        // this is kinda hacky - but reflection is so cool
        $user = JFactory::getUser();
        $JAccessReflection = new ReflectionClass('JUser');
        $_authLevels = $JAccessReflection->getProperty('_authLevels');
        $_authLevels->setAccessible(true);
        $groups = $_authLevels->getValue($user);
        switch($remove) {
            case 'remove':
                $key = array_search($this->_cookieACL,$groups);
                if($key) {
                    unset($groups[$key]);
                    $this->_groupadded = false;
                }
                break;
            default:
                if(!array_search($this->_cookieACL,$groups)) {
                    $groups[]=$this->_cookieACL;
                    $this->_groupadded = true;
                }
                break;
        }
        $_authLevels->setValue($user,$groups);
    }
    private function _addViewLevel($remove=false) {
        if(!class_exists('ReflectionClass',false) || !method_exists('ReflectionProperty','setAccessible')) return;
        if($this->_defaultACL == $this->_cookieACL) return;
        $this->_reflectJUser($remove);
    }
    private function _getCSS($type) {
        switch($type) {
            case 'ribbon':
                if($this->params->get('useribboncss',1)) {
                    $this->_doc->addStyleSheet(JURI::root(true).'/media/plg_system_eprivacy/css/ribbon.css');
                    $this->_doc->addStyleDeclaration($this->params->get('ribboncss'));
                }
                break;
            case 'module':
                if($this->params->get('usemodulecss',1)) {
                    $this->_doc->addStyleDeclaration($this->params->get('modulecss'));
                }
                break;
            default:
                break;
        }
    }
}