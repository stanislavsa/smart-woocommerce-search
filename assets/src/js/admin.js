(function($) {

	"use strict";

	$(function() {

		/**
		 * Init postboxes
		 */

		postboxes.add_postbox_toggles(pagenow);

		/**
		 * Widget list actions
		 */

		$('.ysm-custom-widgets-list').on('click', '.ysm-widget-remove', function(){
			var $td = $(this).parents('td'),
				id = $(this).data('id');

			$td.addClass('ysm-loader');

			if ( confirm(ysm_L10n.widget_delete) ) {

				$.post(ajaxurl, {
					action: 'ysm_widget_delete',
					id: id,
					nonce: $('#ysm_widgets_nonce').val()
				},
				function($r) {
					if ($r == 1) {
						$td.removeClass('ysm-loader').parent().remove();
					}
				});

			}

			return false;
		});

		$('.ysm-custom-widgets-list').on('click', '.ysm-widget-duplicate', function(){
			var $td = $(this).parents('td'),
				id = $(this).data('id'),
				tmpl = wp.template('ysm-widget-list-row');

			$td.addClass('ysm-loader');

			$.post(ajaxurl, {
				action: 'ysm_widget_duplicate',
				id: id,
				nonce: $('#ysm_widgets_nonce').val()
			},
			function($r) {
				if ($r && $r['id'] ) {
					var data = {
							id: $r['id'],
							name: $r['name']
						},
						output = tmpl(data);

					$td.removeClass('ysm-loader');
					$('.ysm-custom-widgets-list').find('tbody').append(output);
				}
			}, 'json');

			return false;
		});

		$('.ymapp-settings__content #loader').on('change', function(){
			var new_val = $('.ysm-loader-preview').attr('src')
							.replace(/images\/\S*.gif$/, 'images/' + $(this).val() + '.gif');
			$('.ysm-loader-preview').attr('src', new_val);
		});


		// JS for autocomplete
		$('#selected_products').select2({
			ajax: {
				url: ysmProductSearch.ajax_url,
				type: 'GET',
				dataType: 'json',
				delay: 250,
				data: function (params) {
					console.log('Select2 Search Params:', params);
					return {
						action: 'ysm_search_products',
						term: params.term
					};
				},
				processResults: function (data) {
					console.log('Select2 Received Data:', data);
					return {
						results: data || []
					};
				},
				error: function(jqXHR, textStatus, errorThrown) {
					console.error('AJAX Error:', {
						status: jqXHR.status,
						statusText: jqXHR.statusText,
						responseText: jqXHR.responseText
					});
				}
			},
			minimumInputLength: 1,
			placeholder: 'Search products',
			templateResult: function(data) {
				return data.text;
			},
			templateSelection: function(data) {
				return data.text;
			}
		});

	});

})(jQuery);
