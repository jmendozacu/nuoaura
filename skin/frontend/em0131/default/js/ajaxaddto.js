;(function($) {
	$.emAjaxWishList = function(el) {
		var linkWishList = $(el);
		var href = linkWishList.attr('href');
		linkWishList.init = function() {
			if (href.indexOf('wishlist/index/add') != -1) {
				href = href.replace("wishlist/index/add", "themeframework/ajaxaddto/wishlist");
				href += 'isAjax/1';
				linkWishList.attr('href', href);
				linkWishList.doClick();
			}
		}
		linkWishList.doClick = function() {
			var bg = $('#bg_fade');
			var data;
			var attr = linkWishList.attr('onclick');
			linkWishList.attr('onclick', '');
			linkWishList.bind('click', function(event) {
				if (typeof attr !== undefined && attr !== false) {
					if (typeof productAddToCartForm !== 'undefined' && productAddToCartForm.submit) {
						data = $('#product_addtocart_form').serialize();
						data += '&isAjax=1';
					} else if (typeof qs_productAddToCartForm !== 'undefined' && qs_productAddToCartForm.submit) {
						data = $('#qs_product_addtocart_form').serialize();
						data += '&isAjax=1';
					}
				}
				bg.css({
					'opacity': '0.5',
					'display': 'block',
					'visibility': 'visible'
				});
				$('#ajaxaddto_compareLink').hide();
				var contain = $("#addtoContainer");
				var tmp_href = $("#ajaxaddto_login_link").attr("href");
				showTop(contain);
				$(contain).show();
				$('#ajaxaddto_loading').show();
				$.ajax({
					url: href,
					data: data,
					dataType: 'json',
					type: 'post',
					success: function(data) {
						$('#ajaxaddto_loading').hide();
						$('#ajaxaddto_content').show();
						$('#ajaxaddto_login_link').show();
						if (data.status == 0) {
							var str = href.replace("themeframework/ajaxaddto/wishlist","wishlist/index/add");
							str = str.replace("isAjax/1","");
							$('#ajaxaddto_login_link').attr('href', str);
							$('#ajaxaddto_image').append(data.message);
						} else {
							$('#ajaxaddto_image').append(data.message);
							$('.em-links-wishlist').replaceWith(data.toplink);
							$('.block-wishlist').replaceWith(data.sidebar);
						}
						var count = EM.SETTING.AJAXCART_AUTOCLOSE;
						if (count > 0) {
							$('.setcountdown').html('&nbsp;&nbsp;(' + count + ')');
							linkWishList.em_countdown = setInterval(function() {
								count = count - 1;
								if (count <= 0) {
									clearInterval(linkWishList.em_countdown);
									bg.css({
										'opacity': '0',
										'display': 'none',
										'visibility': 'hidden'
									});
									$("#addtoContainer").hide();
									$('#ajaxaddto_image').html('');
									$('#ajaxaddto_loading').hide();
									$('#ajaxaddto_content').hide();
									$('.setcountdown').html('');
									return;
								}
								$('.setcountdown').html('&nbsp;&nbsp;(' + count + ')');
							}, 1000);
						}
						$("#ajaxaddto_closeLink").click(function(event) {
							event.preventDefault();
							clearInterval(linkWishList.em_countdown);
							bg.css({
								'opacity': '0',
								'display': 'none',
								'visibility': 'hidden'
							});
							$('#ajaxaddto_login_link').attr('href', tmp_href);
							$("#addtoContainer").hide();
							$('#ajaxaddto_image').html('');
							$('#ajaxaddto_loading').hide();
							$('#ajaxaddto_content').hide();
						});
					}
				});
				event.preventDefault();
			});
		}
		linkWishList.init();
	}
	$.emAjaxCompare = function(el, options) {
		var defaults = {
			sidebarSelector: ".block-compare",
		}
		var bg = $('#bg_fade');
		var vars = $.extend({}, defaults, options);
		var containerSidebar = $(vars.sidebarSelector);
		var linkCompare = $(el);
		linkCompare.init = function() {
			var href = linkCompare.attr('href');
			if (href.search('catalog/product_compare/add') != -1) {
				href = href.replace("catalog/product_compare/add", "themeframework/ajaxaddto/compare");
				if (href.indexOf("?")) {
					href = href.split("?")[0];
				}
				if (window.location.href.match("https://") && !href.match("https://")) {
					href = href.replace("http://", "https://");
				}
				if (window.location.href.match("http://") && !href.match("http://")) {
					href = href.replace("https://", "http://");
				}
				href += 'isAjax/1';
				linkCompare.attr('href', href);
			}
			$(this).unbind('click');
			$(this).bind('click', function(event) {
				event.preventDefault();
				var url = $(this).attr('href');
				var contain = $("#addtoContainer");
				showTop(contain);
				$(contain).show();
				$('#ajaxaddto_login_link').hide();
				$('#ajaxaddto_loading').show();
				bg.css({
					'opacity': '0.5',
					'display': 'block',
					'visibility': 'visible'
				});
				$.ajax({
					url: href,
					dataType: 'json',
					success: function(data) {
						$('#ajaxaddto_loading').hide();
						$('#ajaxaddto_content').show();
						if (data.status == 0) {
							$('#ajaxaddto_image').append(data.message);
						} else {
							$('#ajaxaddto_image').append(data.message);
							$('#ajaxaddto_compareLink').show();
							$(vars.sidebarSelector).replaceWith(data.sidebar);
						}
						var count = EM.SETTING.AJAXCART_AUTOCLOSE;
						if (count > 0) {
							$('.setcountdown').html('&nbsp;&nbsp;(' + count + ')');
							linkCompare.em_ajc_counter = setInterval(function() {
								count = count - 1;
								if (count <= 0) {
									clearInterval(linkCompare.em_ajc_counter);
									bg.css({
										'opacity': '0',
										'display': 'none',
										'visibility': 'hidden'
									});
									$("#addtoContainer").hide();
									$('#ajaxaddto_image').html('');
									$('#ajaxaddto_loading').hide();
									$('#ajaxaddto_content').hide();
									$('#ajaxaddto_compareLink').hide();
									$('.setcountdown').html('');
									return;
								}
								$('.setcountdown').html('&nbsp;&nbsp;(' + count + ')');
							}, 1000);
						}
						new $.emAjaxRemoveCompare(options);
					}
				});
				$("#ajaxaddto_closeLink").click(function(event) {
					event.preventDefault();
					clearInterval(linkCompare.em_ajc_counter);
					bg.css({
						'opacity': '0',
						'display': 'none',
						'visibility': 'hidden'
					});
					$("#addtoContainer").hide();
					$('#ajaxaddto_image').html('');
					$('#ajaxaddto_loading').hide();
					$('#ajaxaddto_content').hide();
				});
			});
		}
		linkCompare.init();
	}
	$.emAjaxRemoveCompare = function(options) {
		var containerSidebar = $(options.sidebarSelector);
		containerSidebar.initRemoveCompareLink = function(options) {
			var listItems = $(containerSidebar);
			listItems.find('a.btn-remove').each(function() {
				var href = $(this).attr('href');
				if (href.search('catalog/product_compare/remove') != -1) {
					var tmp = href.search("catalog/product_compare/remove/");
					var baseurl = href.substr(0, tmp);
					var tmp_2 = href.search("/product/") + 9;
					var tmp_3 = href.search("/uenc/");
					var id = href.substr(tmp_2, tmp_3 - tmp_2);
					href = urlsite + 'index.php/themeframework/ajaxaddto/removeCompare/product/' + id;
					if (href.indexOf("?")) {
						href = href.split("?")[0];
					}
					if (window.location.href.match("https://") && !href.match("https://")) {
						href = href.replace("http://", "https://");
					}
					if (window.location.href.match("http://") && !href.match("http://")) {
						href = href.replace("https://", "http://");
					}
					$(this).attr('href', href);
				}
				$(this).attr('onclick', "");
				$(this).unbind('click');
				$(this).bind('click', function(event) {
					event.preventDefault();
					var conf = confirm(Translator.translate('Are you sure you would like to remove this item from the compare products?'));
					if (conf == false) return;
					var url = $(this).attr('href');
					doClick(url);
				});
			});
			var clearLink = $('a[href*="catalog/product_compare/clear"]');
			if (typeof clearLink !== "undefined") {
				var urlClear = clearLink.attr('href');
				urlClear = urlsite + 'index.php/themeframework/ajaxaddto/clearCompare';
				if (urlClear.indexOf("?")) {
					urlClear = urlClear.split("?")[0];
				}
				if (window.location.href.match("https://") && !urlClear.match("https://")) {
					urlClear = urlClear.replace("http://", "https://");
				}
				if (window.location.href.match("http://") && !urlClear.match("http://")) {
					urlClear = urlClear.replace("https://", "http://");
				}
				clearLink.attr('onclick', "");
				clearLink.unbind('click');
				clearLink.attr('href', urlClear);
				clearLink.bind('click', function(event) {
					event.preventDefault();
					var conf = confirm(Translator.translate('Are you sure you would like to remove all products from your comparison?'));
					if (conf == false) return;
					var url = $(this).attr('href');
					$('#ajaxaddto_compareLink').hide();
					doClick(url);
				});
			}
		}

		function doClick(url) {
			var bg = $('#bg_fade');
			var contain = $("#addtoContainer");
			showTop(contain);
			$(contain).show();
			$('#ajaxaddto_login_link').hide();
			$('#ajaxaddto_loading').show();
			bg.css({
				'opacity': '0.5',
				'display': 'block',
				'visibility': 'visible'
			});
			$.ajax({
				url: url,
				dataType: 'json',
				success: function(data) {
					$('#ajaxaddto_loading').hide();
					$('#ajaxaddto_content').show();
					if (data.status == 0) {
						$('#ajaxaddto_image').append(data.message);
					} else {
						$('#ajaxaddto_image').append(data.message);
						$(containerSidebar).replaceWith(data.sidebar);
					}
					var count = EM.SETTING.AJAXCART_AUTOCLOSE;
					$('.setcountdown').html('&nbsp;&nbsp;(' + count + ')');
					em_countdown = setInterval(function() {
						count = count - 1;
						if (count <= 0) {
							clearInterval(em_countdown);
							bg.css({
								'opacity': '0',
								'display': 'none',
								'visibility': 'hidden'
							});
							$("#addtoContainer").hide();
							$('#ajaxaddto_image').html('');
							$('#ajaxaddto_loading').hide();
							$('#ajaxaddto_content').hide();
							$('.setcountdown').html('');
							return;
						}
						$('.setcountdown').html('&nbsp;&nbsp;(' + count + ')');
					}, 1000);
					new $.emAjaxRemoveCompare(options);
				}
			});
			$("#ajaxaddto_closeLink").click(function(event) {
				event.preventDefault();
				clearInterval(em_countdown);
				bg.css({
					'opacity': '0',
					'display': 'none',
					'visibility': 'hidden'
				});
				$("#addtoContainer").hide();
				$('#ajaxaddto_image').html('');
				$('#ajaxaddto_loading').hide();
				$('#ajaxaddto_content').hide();
			});
		}
		containerSidebar.initRemoveCompareLink(options);
	}
	$.fn.emAjaxWishList = function() {
		return this.each(function() {
			new $.emAjaxWishList(this);
		});
	}
	$.fn.emAjaxCompare = function(options) {
		new $.emAjaxRemoveCompare(options);
		return this.each(function() {
			new $.emAjaxCompare(this, options);
		});
	}

	function showTop(element) {
		var w = $(window).width();
		if (isMobile || w <= 767) {
			var pop_width = (w - 300) / 2;
			$(element).css({
				"top": "15px",
				"left": Math.round(pop_width) + 'px',
			});
		} else {
			var pop_width = (w - 400) / 2;
			$(element).css({
				"top": "15px",
				"left": Math.round(pop_width) + 'px',
			});
		}
	}
	$(window).resize($.throttle(250, function() {
		var contain = $("#addtoContainer");
		if (contain.is(':visible') && contain.css('display') != 'none') showTop(contain);
	}));
})(jQuery);