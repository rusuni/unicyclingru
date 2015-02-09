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

class AC_helper {

    var $params = array();
    var $staticParams = array();

    function __construct($globalParams) {
        // Default parameters
        $this->staticParams['textAlign'] = $globalParams->get('ac_textAlign', 'left');
        $this->staticParams['vertAlign'] = $globalParams->get('ac_vertAlign', 'top');
        $this->staticParams['spacing'] = $globalParams->get('ac_spacing', 10);
        $this->staticParams['hyphenator'] = $globalParams->get('ac_hyphenator', 1);
        $this->staticParams['brake_code'] = $globalParams->get('ac_brake_code', "ACBR");
    }

    //Gets the atributes value by name, else returns false
    private function AC_getAttribute($attrib, $tag, $default) {
        //get attribute from html tag
        $tag = str_replace("}", "", $tag);
        $re = '/' . preg_quote($attrib) . '=([\'"])?((?(1).+?|[^\s>]+))(?(1)\1)/is';
        if (preg_match($re, $tag, $match)) {
            return urldecode($match[2]);
        }
        return $default;
    }

    function AC_createColumns($source_html, $matchValue, $id, $langDirection) {

        $this->params['textAlign'] = $this->AC_getAttribute("textAlign", $matchValue, $this->staticParams['textAlign'], 'left');
        $this->params['vertAlign'] = $this->AC_getAttribute("vertAlign", $matchValue, $this->staticParams['vertAlign'], 'top');
        $this->params['spacing'] = $this->AC_getAttribute("spacing", $matchValue, $this->staticParams['spacing'], 10);
        $this->params['hyphenator'] = $this->AC_getAttribute("hyphenator", $matchValue, $this->staticParams['hyphenator'], 1);
        $this->params['brake_code'] = $this->AC_getAttribute("brake_code", $matchValue, $this->staticParams['brake_code'], "ACBR");
        $html = "";

        // Split string at separators
        $columnsArray = explode($this->params['brake_code'], $source_html);
        $html.='<table border="0" cellspacing="0" cellpadding="0" width="100%" class="AC_table"><tbody><tr style="border-style: none;">';
        foreach ($columnsArray as $key => $value) {
            // Add content		
            $html.='<td width="' . floor(100 / count($columnsArray)) . '%" style="border-style: none; text-align:' . $this->params['textAlign'] . '; vertical-align:' . $this->params['vertAlign'] . '" class="hyphenate">' . $value . '</td>';

            if (count($columnsArray) - 1 != $key) {// Check is last
                $html.='<td style="border-style: none;"><div style="display:block; width:' . $this->params['spacing'] . 'px">&nbsp;</div></td>';
            }
        }
        $html.='</tr></tbody></table>' . "\n";
        $html.='<br style="clear:both;" />';

        return $html;
    }

}

?>
