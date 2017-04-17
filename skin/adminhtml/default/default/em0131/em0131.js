/**
 * Emthemes
 *
 * @license commercial software
 * @copyright (c) 2014 Codespot Software JSC - Emthemes.com. (http://www.Emthemes.com)
 */
jQuery(function($) {
    function hideElement(){
        var e = $('.em-hide').parent('td').parent('tr');
        e.hide();
    };
	function emCssSettings(e,value) {
		var sChange = $(e).find('.em-visible');
		var sValue = sChange.val();
		var sHide = $(e).find('.em-invisible').parents('tr');
		if (sValue == value) {
			sHide.show();
		} else {
			sHide.hide();
		}
		sChange.change(function() {
			var sValueChange = $(this).val();
			if (sValueChange == value) {
				sHide.show();
			} else {
				sHide.hide();
			}
		});
	};

	function emPattern(e) {
		$('a.' + e).click(function() {
			$('a.' + e).removeClass('selected');
			$(this).addClass('selected');
			$('#' + e).val($(this).data('input-value'));
			return false;
		});
	};

	function settings() {
		// box/wide
		$('#general_box_wide').change(function() {
			var a = $('#general_box_wide').val();
			if (a == 'wide') {
				$('#typo_general_page_bg_color').closest('tr').hide();
				$('#typo_general_page_bg_image').closest('tr').hide();
				$('#typo_general_page_bg_file').closest('tr').hide();
				$('#typo_general_page_bg_position').closest('tr').hide();
				$('#typo_general_page_bg_repeat').closest('tr').hide();
                $('#typo_general_page_bg_attachment').closest('tr').hide();                
			} else {
				$('#typo_general_page_bg_color').closest('tr').show();
				$('#typo_general_page_bg_image').closest('tr').show();
				$('#typo_general_page_bg_file').closest('tr').show();
				$('#typo_general_page_bg_position').closest('tr').show();
				$('#typo_general_page_bg_repeat').closest('tr').show();
                $('#typo_general_page_bg_attachment').closest('tr').show();
			}
		});
		$('#product_detail_viewmode').change(function() {
			var b = $('#product_detail_viewmode').val();
			if (b == 'cloudzoom') {
				$('#product_detail_cloud_zoom_width').closest('tr').show();
				$('#product_detail_cloud_zoom_height').closest('tr').show();
			} else {
				$('#product_detail_cloud_zoom_width').closest('tr').hide();
				$('#product_detail_cloud_zoom_height').closest('tr').hide();
			}
		});
	};
	$(document).ready(function() {
		emCssSettings('#themeframework_theme_form_crossell',1);
		emCssSettings('#themeframework_theme_form_upsell',1);
		emCssSettings('#themeframework_theme_form_related',1);
		emCssSettings('#themeframework_theme_form_moreview',1);
        emCssSettings('#themeframework_theme_form_product_detail',1);
        emCssSettings('#themeframework_theme_form_general',1);
        emCssSettings('#themeframework_theme_tabs_products_grid_content',-1);
        emCssSettings('#themeframework_theme_tabs_products_list_content',-1);
		emPattern('typo_general_page_bg_image');
		emPattern('header_header_bg_image');
		emPattern('body_body_bg_image');
		emPattern('footer_footer_bg_image');
		settings();
		var a = $('#general_box_wide').val();
		if (a == 'wide') {
			$('#typo_general_page_bg_color').closest('tr').hide();
			$('#typo_general_page_bg_image').closest('tr').hide();
			$('#typo_general_page_bg_file').closest('tr').hide();
			$('#typo_general_page_bg_position').closest('tr').hide();
			$('#typo_general_page_bg_repeat').closest('tr').hide();
            $('#typo_general_page_bg_attachment').closest('tr').hide();
		} else {
			$('#typo_general_page_bg_color').closest('tr').show();
			$('#typo_general_page_bg_image').closest('tr').show();
			$('#typo_general_page_bg_file').closest('tr').show();
			$('#typo_general_page_bg_position').closest('tr').show();
			$('#typo_general_page_bg_repeat').closest('tr').show();
            $('#typo_general_page_bg_attachment').closest('tr').show();
		}
		var b = $('#product_detail_viewmode').val();
		if (b == 'cloudzoom') {
			$('#product_detail_cloud_zoom_width').closest('tr').show();
			$('#product_detail_cloud_zoom_height').closest('tr').show();
		} else {
			$('#product_detail_cloud_zoom_width').closest('tr').hide();
			$('#product_detail_cloud_zoom_height').closest('tr').hide();
		}
        hideElement();
	});
});