/**
 * Main JavaScript file
 *
 * @package         Tabs
 * @version         3.7.1
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2014 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

(function($) {
	$(document).ready(function() {
		if (typeof( window['nn_tabs_use_hash'] ) != "undefined") {
			nnTabs = {
				show  : function(id, scroll, openparents) {
					var $this = this;
					var $el = $('a[href$="#' + id + '"]');

					if (openparents) {
						var $parents = $el.parents().get().reverse();
						var hasparents = 0;
						$($parents).each(function() {
							if ($(this).hasClass('tab-pane') && !$(this).hasClass('in')) {
								$(this).parent().parent().find('a[href="#' + this.id + '"]').tab('show')
									.on('shown shown.bs.tab', function() {
										$el.tab('show');
									});
								hasparents = 1;
							}
						});

						if (!hasparents) {
							$el.tab('show');
						}
					} else {
						$el.tab('show');
					}

					$el.tab('show');

					var $pane = $('#' + id);
					$pane.addClass('in active').removeClass('fade');
					$pane.parent().find('> .tab-pane.fade').removeClass('in').removeClass('active');


					$el.focus();
				},

			};

			if (nn_tabs_use_hash) {
				if (window.location.hash) {
					var id = window.location.hash.replace('#', '');
					if (id.indexOf("&") == -1 && id.indexOf("=") == -1 && $('.nn_tabs > .tab-content > #' + id).length > 0) {
						if (!nn_tabs_urlscroll) {
							// scroll to top to prevent browser scrolling
							$('html,body').animate({scrollTop: 0});
						}

						nnTabs.show(id, nn_tabs_urlscroll, 1);
					}
				}

				$('.nn_tabs-tab a[data-toggle="tab"]').on('show show.bs.tab', function($e) {
					window.location.hash = String($e.target).substr(String($e.target).indexOf("#") + 1);
					$e.stopPropagation();
				});
			}


			// Re-inintialize Google Maps on tabs show
			$('.tab-pane.active iframe').each(function() {
				$(this).attr('reloaded', true);
			});
			$('.nn_tabs-tab a[data-toggle="tab"]').on('show show.bs.tab', function($e) {
				if (typeof initialize == 'function') {
					initialize();
				}
				var id = String($e.target).substr(String($e.target).indexOf("#") + 1);
				var el = $('#' + decodeURIComponent(id));
				if (el) {
					el.find('iframe').each(function() {
						if (!$(this).attr('reloaded')) {
							this.src += '';
							$(this).attr('reloaded', true);
						}
					});
				}
			});
			$(window).resize(function() {
				if (typeof initialize == 'function') {
					initialize();
				}
				$('.tab-pane iframe').each(function() {
					$(this).attr('reloaded', false);
				});
				$('.tab-pane.active iframe').each(function() {
					this.src += '';
					$(this).attr('reloaded', true);
				});
			});
		}
	});
})(jQuery);
