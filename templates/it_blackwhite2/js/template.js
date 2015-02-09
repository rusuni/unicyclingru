/***********************************************************************************************/
/* Main IceTheme template Scripts */
/***********************************************************************************************/

/* default joomla script */
(function($)
{
	$(document).ready(function()
	{
		$('*[rel=tooltip]').tooltip()

		// Turn radios into btn-group
		$('.radio.btn-group label').addClass('btn');
		$(".btn-group label:not(.active)").click(function()
		{
			var label = $(this);
			var input = $('#' + label.attr('for'));

			if (!input.prop('checked')) {
				label.closest('.btn-group').find("label").removeClass('active btn-success btn-danger btn-primary');
				if (input.val() == '') {
					label.addClass('active btn-primary');
				} else if (input.val() == 0) {
					label.addClass('active btn-danger');
				} else {
					label.addClass('active btn-success');
				}
				input.prop('checked', true);
			}
		});
		$(".btn-group input[checked=checked]").each(function()
		{
			if ($(this).val() == '') {
				$("label[for=" + $(this).attr('id') + "]").addClass('active btn-primary');
			} else if ($(this).val() == 0) {
				$("label[for=" + $(this).attr('id') + "]").addClass('active btn-danger');
			} else {
				$("label[for=" + $(this).attr('id') + "]").addClass('active btn-success');
			}
		});
	})
})(jQuery);



/* jQuery scripts for IceTheme template */
jQuery(document).ready(function() {   

	/* initialize bootstrap tooltips */
	jQuery("[rel='tooltip']").tooltip();
	
	/* language module hover efffect for flags */
	jQuery(".mod-languages li").hover(function () {
		jQuery(".mod-languages li").css({opacity : .25});
	  }, 
	  function () {
		jQuery(".mod-languages li").css({ opacity : 1});
	  }
	);	
	
	/* effect for the footer menu on hover */
	jQuery("#footer .footermenu ul.nav li a").hover(function () {
		jQuery("#footer .footermenu ul.nav li a").css({color : '#999'});
	  }, 
	  function () {
		jQuery("#footer .footermenu ul.nav li a").css({ color : '#555'});
	  }
	);	
	
	/* social icons effect on hover */
	jQuery("#social_icons li a").hover(function () {
		jQuery("#social_icons li a").css({opacity : .15});
	  }, 
	  function () {
		jQuery("#social_icons li a").css({ opacity : .5});
	  }
	);	
	
	/* add a class to icemegamenu module element */
	jQuery(".ice-megamenu-toggle a").attr("href", "#mainmenu")
	
	/* add some adjustments to joomla articles */
	jQuery(".createdby").prepend("<span class=\"icon-user\"></span>");
	jQuery(".category-name").prepend("<span class=\"icon-folder-close\"></span>");
	
	/* fade slideshow with white bg on menu hover */
	jQuery("#mainmenu").hover(function(){     
		jQuery("#iceslideshow > div > div:first-child").addClass("icemegamenu-hover");    
	},     
	function(){    
	   jQuery("#iceslideshow > div > div:first-child").removeClass("icemegamenu-hover");     
	});


	
	
});  
	
		
// detect if screen is with touch or not (pure JS)
if (("ontouchstart" in document.documentElement)) {
	document.documentElement.className += "with-touch";
}else {
	document.documentElement.className += "no-touch";
}
					
