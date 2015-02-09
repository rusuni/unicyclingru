<?php

/* ------------------------------------------------------------------------
  # plg_Admirorcolumnizer - Admiror Columnizer Plugin
  # ------------------------------------------------------------------------
  # author    Vasiljevski & Kekeljevic
  # copyright Copyright (C) 2011 admiror-design-studio.com. All Rights Reserved.
  # @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
  # Websites: http://www.admiror-design-studio.com/joomla-extensions
  # Technical Support:  Forum - http://www.vasiljevski.com/forum/index.php
  ------------------------------------------------------------------------- */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import library dependencies
jimport('joomla.event.plugin');
jimport('joomla.plugin.plugin');

class plgContentAdmirorcolumnizer extends JPlugin {

    //Constructor
    function plgContentAdmirorcolumnizer(&$subject) {
        parent::__construct($subject);

        $this->plugin = JPluginHelper::getPlugin('content', 'admirorcolumnizer');
        $this->params = new JRegistry($this->plugin->params);
        // load current language
        $this->loadLanguage();
    }

    //Joomla 1.5
    public function onPrepareContent(&$row, &$params, $limitstart = 0) {
        if (preg_match("#{AC[^}]*}(.*?){/AC}#s", strtoupper($row->text))) {
            $row->text = $this->textToColumns($row->text);
        }
    }

    //Joomla 1.6 and > function 
    public function onContentPrepare($context, &$row, &$params, $page = 0) {
        if (is_object($row)) {
            return $this->onPrepareContent($row, $params, $page);
        } else {
            if (preg_match("#{AC[^}]*}(.*?){/AC}#s", strtoupper($row))) {
                $row = $this->textToColumns($row);
            }
        }
        return true;
    }

    //This does all the work :)
    private function textToColumns($text) {
        if (preg_match_all("#{AC[^}]*}(.*?){/AC}|{ac[^}]*}(.*?){/ac}#s", $text, $matches, PREG_PATTERN_ORDER) > 0) {
            require_once (dirname(__FILE__) . '/admirorcolumnizer/scripts/AC_helper.php');
            $AC = new AC_helper($this->params);
            $doc = JFactory::getDocument();
            $html = "";
            foreach ($matches[0] as $matchKey => $matchValue) {
                $html = $AC->AC_createColumns(preg_replace("/{.+?}/", "", $matchValue), $matchValue, $matchKey . "_" . rand(0, 1000000), $doc->direction);
                $text = str_replace($matchValue, $html, $text);
            }
            if ($AC->params['hyphenator']) {
                $version = new JVersion();
                if ($version->RELEASE == "1.5") {
                    $doc->addScript(JURI::root() . 'plugins/content/admirorcolumnizer/scripts/Hyphenator.js');
                } else {
                    $doc->addScript(JURI::root() . 'plugins/content/admirorcolumnizer/admirorcolumnizer/scripts/Hyphenator.js');
                }
                $text.= '
                            <!-- AdmirorColumnizer 3 -->
                            <script type="text/javascript">
                                    Hyphenator.run();
                            </script>
                            ';
            }
        }
        return $text;
    }

}

//class plgContentAdmirorcolumnizer extends JPlugin
?>
