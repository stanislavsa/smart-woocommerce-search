(function($) {

	"use strict";

	$(function() {

		/**
		 * Init colorpicker
		 */

		$( '.sm-color-picker' ).wpColorPicker();

		/**
		 * Init postboxes
		 */

		postboxes.add_postbox_toggles(pagenow);

		/**
		 * Widget list actions
		 */

		$('.ysm-widgets-list').on('click', '.ysm-widget-remove', function(){
			var $td = $(this).parents('td'),
				id = $(this).data('id');

			$td.addClass('ysm-loader');

			if ( confirm(ysm_L10n.widget_delete) ) {

				$.post(ajaxurl, {
					action: 'ysm_widget_delete',
					id: id
				},
				function($r) {
					if ($r == 1) {
						$td.removeClass('ysm-loader').parent().remove();
					}
				});

			}

			return false;
		});

		$('.ysm-widgets-list').on('click', '.ysm-widget-duplicate', function(){
			var $td = $(this).parents('td'),
				id = $(this).data('id'),
				tmpl = wp.template('ysm-widget-list-row');

			$td.addClass('ysm-loader');

			$.post(ajaxurl, {
				action: 'ysm_widget_duplicate',
				id: id
			},
			function($r) {
				if ($r && $r['id'] ) {
					var data = {
							id: $r['id'],
							name: $r['name']
						},
						output = tmpl(data);

					$td.removeClass('ysm-loader');
					$('.ysm-widgets-list').find('tbody').append(output);
				}
			}, 'json');

			return false;
		});

		/**
		 * Widget settings tabs
		 */
		$('#ysm-widget-settings-nav-wrapper > a').on('click', function(){
			var id = $(this).attr('href'),
				holder = $(this).parent('.nav-tab-wrapper');

			holder.find('a').removeClass('nav-tab-active');
			$(this).addClass('nav-tab-active');
			holder.parent().find('.ysm-widget-settings-tab').hide();
			$(id).fadeIn();
			return false;
		});

		/**
		 * Admin footer text rating
		 */
		$('#ysm-rate-plugin').on('click', function(){
			$(this).parent().text( 'Thank you!' );
		});
	});

})(jQuery);
