/**
 * EM QuickShop
 *
 * @license commercial software
 * @copyright (c) 2012 Codespot Software JSC - EMThemes.com. (http://www.emthemes.com)
 */
// disable QuickShop:
// EM_QUICKSHOP_DISABLED = true;
jQuery.noConflict();
var em_qs = null;
jQuery(function($) {
	// quickshop init

	function _qsJnit() {
		//insert quickshop popup
        var $_qsLink = $('body').find('.quickshop-link');
        var $_qsContent = $('body').find("#quickshop-content");
        var $_qsModal = jQuery('body').find('#em-qs-modal');
        $_qsLink.on('click',function(event){
            event.preventDefault(); 
            $_qsModal.modal('show');
            var $_qsrequest = $.ajax({
                url: $(this).attr('href'),
                method: 'POST',
                dataType : 'html',
            });
            $_qsrequest.done(function(data){
                $_qsContent.html(data);
                $_qsContent.removeClass('emfilter-ajaxblock-loading').addClass('emfilter-ajaxblock-loaded');
                $(window).trigger("qs_load");
                initAjaxCart('#em-qs-modal .quickshop-main','qs_product_addtocart_form');
            });
            return false;                	
        });
        $_qsModal.on('hidden.bs.modal', function (e) {
            $_qsContent.empty();
            $_qsContent.removeClass('emfilter-ajaxblock-loaded').addClass('emfilter-ajaxblock-loading');
            $(window).off("qs_load");
        });
	}
	if ((typeof EM_QUICKSHOP_DISABLED == 'undefined' || !EM_QUICKSHOP_DISABLED) && !isMobile) {
	   _qsJnit();
	}	
	em_qs = _qsJnit;
});