/**
 * @version		$Id$
 * @author		NooTheme
 * @package		Joomla.Site
 * @subpackage	mod_noo_timeline
 * @copyright	Copyright (C) 2013 NooTheme. All rights reserved.
 * @license		License GNU General Public License version 2 or later; see LICENSE.txt, see LICENSE.php
 */
!function($){
	"use strict";
	$.fn.nootimeline = function(options){
		return this.each(function(){
			var $this = $(this),
				controls = $this.find(".noo-tl-control,.noo-tl-time,.noo-tl-title");
			controls.click(function(){
				var that = $(this),
					parent = that.closest(".noo-tl-item");
				if(parent.hasClass("selected")){
					parent.removeClass("selected").find(".noo-tl-desc").hide(400);
				}else{
					var selected = $this.find(".selected");
					if(selected){
						selected.removeClass("selected").find(".noo-tl-desc").hide(400);
					}
					parent.addClass("selected").find(".noo-tl-desc").show(400);
				}
				return false;
			});
		});
	}
}(window.jQuery);