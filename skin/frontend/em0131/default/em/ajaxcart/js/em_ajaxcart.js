/*
 * ajaxcart javascript;
 * update date : 06/06/2014.
 */
var em_box;
var em_ajc_counter;

function ajax_option(param) {
	var url = em_ajc_baseurl + "ajaxcart/opt/index/";
	em_box.open();
	new Ajax.Request(url, {
		parameters: param,
		onSuccess: function(res) {
			data = res.responseText.evalJSON();
			changeOptions(data);
		}
	});
}

function ajax_add(url, param) {
	if (typeof param == 'undefined') {
		param = 'ajax_package_name=' + em_ajc_package_name + '&ajax_layout=' + em_ajc_layout + '&ajax_template=' + em_ajc_template + '&ajax_skin=' + em_ajc_skin;
	}
	var link = url.replace('checkout', 'ajaxcart').replace('wishlist/index', 'ajaxcart/wishlist');
	var tmp = url.search("in_cart"); //	shopping cart page
	em_box.open();
	new Ajax.Request(link, {
		parameters: param,
		onSuccess: function(data) {
			html = data.responseText.evalJSON();
			if (html.options == 1) changeOptions(html);
			else {
				if (tmp > 0) { //	addtocart in shopping cart page
					window.location.href = em_ajc_baseurl + 'checkout/cart/';
				} else {
					changeHTML(html);
				}
			}
		}
	});
}

function changeOptions(data) {
	$('ajax_loading').hide();
	$('closeLink').show();
	$('viewcart_button').show();
	$('ajax_content').show();
	$('ajc_btn_checkout').hide();
	$$('#ajax_image').each(function(el) {
		el.innerHTML = data.html;
	});
	data.html.evalScripts();
};

function changeHTML(html) {
	$('ajax_loading').hide();
	$('closeLink').show();
	$('viewcart_button').hide();
	$('ajc_btn_checkout').show();
	$('ajax_content').show();
	$$('#ajax_image').each(function(el) {
		el.innerHTML = html.msg;
	});
	if (em_ajc_currentpage == 1) {
		window.location.href = em_ajc_baseurl + 'checkout/cart/';
		return true;
	}
	if (html.check == 'success') {
		$$('.top-link-cart').each(function(el) {
			el.innerHTML = html.toplink;
			el.title = html.toplink;
		});
		$$('.em-wrapper-topcart').each(function(el) {
			var newElement = new Element('div');
			newElement.update(html.sidebar);
			var div = newElement.select('div')[0];
			el.update(div.innerHTML);
		});
		if (html.check_crosssell == 1) {
			$('ajax_crosssell').innerHTML = html.crosssell;
			$('ajax_crosssell').show();
		}
		$$('.em-wrapper-topcart-mobile .em-topcart-qty').each(function(el) {
            el.innerHTML = html.qty;
		});
		if (html.w_check == 1) {
			var sub = html.w_sub;
			if ($$('.add-to-cart .btn-cart').length > 0) $$('.add-to-cart .btn-cart')[0].remove();
			if ($$('.add-to-cart .paypal-logo').length > 0) $$('.add-to-cart .paypal-logo')[0].remove();
			var tmp_whish = $$('.add-to-cart')[0].innerHTML;
			$$('.add-to-cart')[0].update(sub.text + tmp_whish);
			if (sub.sidebar == "") {
				$$('.block-wishlist')[0].remove();
			} else {
				$$('.block-wishlist').each(function(el) {
					var newElement = new Element('div');
					newElement.update(sub.sidebar);
					var div = newElement.select('div')[0];
					el.update(div.innerHTML);
				});
			}
			var $$li = $$('.header .links li');
			if ($$li.length > 0) {
				$$li.each(function(li) {
					var a = li.down('a');
					var title = a.readAttribute('title');
					if (title.search("ishlist") > 0) {
						a.setAttribute("title", sub.link);
						a.update(sub.link);
					}
				});
			}
		}
	}
	deleteItem();
	// Auto close box ajaxcart . var em_ajc_time in template/em_ajaxcart/em_head.phtml
	if (em_ajc_time > 0) {
		count = em_ajc_time;
		jQuery('.emajc_num').html('&nbsp;&nbsp;(' + count + ')');
		em_ajc_counter = setInterval(em_ajc_timer, 1000);
	}
}

function em_ajc_timer() {
	count = count - 1;
	if (count <= 0) {
		clearInterval(em_ajc_counter);
		//em_ajc_counter ended, do something here
		em_box.close();
		$('ajax_image').update('');
		$('ajax_loading').show();
		$('closeLink').hide();
		$('ajc_btn_checkout').hide();
		$('ajax_crosssell').hide();
		jQuery('.emajc_num').html('');
		return;
	}
	//Do code for showing the number of seconds here
	jQuery('.emajc_num').html('&nbsp;&nbsp;(' + count + ')');
};
// pre-submit callback

function showRequest(formData, jqForm, options) {
	em_box.open();
	return true;
};
// post-submit callback

function showResponse(responseText, statusText, xhr, $form) {
	if (responseText.options == 1) changeOptions(responseText);
	else
	changeHTML(responseText);
	if ($('product_addtocart_form')) {
		var url = $('product_addtocart_form').readAttribute('action');
		var link = url.replace('ajaxcart', 'checkout').replace('ajaxcart/wishlist', 'wishlist/index');
		$('product_addtocart_form').setAttribute("action", link);
	}
};

function ajcLocation(url) {
	var opt = url.search("options=cart");
	if (opt > 0) { // Use less than 1.9.0.0
		var ind = url.substr(0, opt - 1);
		ind = ind.replace(em_ajc_baseurl, '');
		param = 'ajax_option=' + ind;
		ajax_option(param);
	} else {
		ind = url.replace(em_ajc_baseurl, '');
		param = 'ajax_option=' + ind;
		ajax_option(param);
	}
};

function setLocation(url) {
	if (check_url(url) == 1) {
		window.location.href = url;
		return false;
	}
	var opt = url.search("options=cart");
	if (opt > 0) { // Use less than 1.9.0.0
		var ind = url.substr(0, opt - 1);
		ind = ind.replace(em_ajc_baseurl, '');
		param = 'ajax_option=' + ind;
		ajax_option(param);
	} else {
		var tmp = url.search("checkout/cart/");
		if (tmp > 0) ajax_add(url);
		else {
			if (em_ajc_currentpage == 1) {
				window.location.href = url;
				return true;
			}
			ind = url.replace(em_ajc_baseurl, '');
			param = 'ajax_option=' + ind;
			ajax_option(param);
		}
	}
}

function check_url(url) {
	var att = 0;
	if (em_ajc_enable == 1) {
		att = 1;
	}
	var arr = ["checkout/onepage/", "wishlist/index/", "dir=", "limit="];
	jQuery.each(arr, function(i, value) {
		var redir = url.search(value); // link setLocation not btn cart
		if (redir > 0) {
			att = 1
		}
	});
	return att;
}

function initAjaxCart(container, formID) {
	if (container != '') container += ' ';
	if (em_ajc_enable == 1) return false;
	var containerDiv = $(container + 'containerDiv'); //	create container ajaxcart				
	if (containerDiv) em_box = new LightboxAJC(containerDiv);
	var options = {
		beforeSubmit: showRequest,
		success: showResponse,
		dataType: 'json'
	};
	if (em_box) {
		$$(container + 'button.btn-cart').each(function(el) {
			if (el.up('form#' + formID)) {
				var url = $(formID).readAttribute('action');
				var link = url.replace('checkout', 'ajaxcart').replace('wishlist/index', 'ajaxcart/wishlist');
				el.onclick = function() {
					if (typeof productAddToCartForm != 'undefined' && productAddToCartForm.submit && container == '') {
						var emajcForm = productAddToCartForm;
						if (('undefined' != typeof productAddToCartFormOld) && productAddToCartFormOld) emajcForm = productAddToCartFormOld;
						if (emajcForm.validator && emajcForm.validator.validate()) {
							jQuery('#' + formID).ajaxForm(options);
							$(formID).setAttribute("action", link);
							jQuery('#' + formID).submit();
						}
					} else if (typeof qs_productAddToCartForm != 'undefined' && qs_productAddToCartForm.submit) {
						var qs_emajcForm = qs_productAddToCartForm;
						if (('undefined' != typeof qs_productAddToCartFormOld) && qs_productAddToCartFormOld) qs_emajcForm = qs_productAddToCartFormOld;
						if (qs_emajcForm.validator && qs_emajcForm.validator.validate()) {
							jQuery('#' + formID).ajaxForm(options);
							$(formID).setAttribute("action", link);
							jQuery('#' + formID).submit();
						}
					}
					return false;
				}
			}
			if (el.up('form#wishlist-view-form')) {
				el.onclick = function() {
					var form = $('wishlist-view-form');
					var dir_up = el.up('#wishlist-table tr');
					var str = dir_up.readAttribute('id');
					var itemId = str.replace("item_", "");
					addWItemToCart(itemId);
				}
			}
			if (el.up('form#reorder-validate-detail')) {
				el.onclick = function() {
					var url = $('reorder-validate-detail').readAttribute('action');
					var param = $('reorder-validate-detail').serialize() + '&ajax_package_name=' + em_ajc_package_name + '&ajax_layout=' + em_ajc_layout + '&ajax_template=' + em_ajc_template + '&ajax_skin=' + em_ajc_skin;
					if (param.search("ajaxcart") < 0) {
						if (reorderFormDetail.submit) {
							if (reorderFormDetail.validator && reorderFormDetail.validator.validate()) {
								ajax_add(url, param);
							}
							return false;
						}
					}
				}
			}
			if (em_ajc_currentpage == 1) {
				var url = el.readAttribute('onclick');
				if (url) {
					var tmp = url.search("checkout/cart/");
					if (tmp < 0) {
						var str = url.replace('setLocation', 'ajcLocation');
						el.setAttribute("onclick", str);
					}
				}
			}
		});
	}
	deleteItem();
	Event.observe('closeLink', 'click', function() {
		clearInterval(em_ajc_counter);
		em_box.close();
		$('ajax_image').update('');
		$('ajax_loading').show();
		$('closeLink').hide();
		$('viewcart_button').hide();
		$('ajc_btn_checkout').hide();
		$('ajax_crosssell').hide();
		jQuery('.emajc_num').html('');
	});
};
document.observe("dom:loaded", function() {
	initAjaxCart('', 'product_addtocart_form');
});

function deleteItem() {
	$$('a').each(function(el) {
		if (el.href.search('checkout/cart/delete') != -1 && el.href.search('javascript:ajax_del') == -1) {
			el.href = 'javascript:ajax_del(\'' + el.href + '\')';
		}
		if (el.up('.truncated')) {
			var a = el.up('.truncated');
			a.observe('mouseover', function() {
				a.down('.truncated_full_value').addClassName('show');
			});
			a.observe('mouseout', function() {
				a.down('.truncated_full_value').removeClassName('show');
			});
		}
	});
};

function ajax_del(url) {
	var check = $('shopping-cart-table');
	if (check) {
		window.location.href = url;
	} else {
		var tmp = url.search("checkout/cart/");
		var baseurl = url.substr(0, tmp);
		var tmp_2 = url.search("/id/") + 4;
		var tmp_3 = url.search("/uenc/");
		var id = url.substr(tmp_2, tmp_3 - tmp_2);
		var link = baseurl + 'ajaxcart/index/delete/id/' + id;
		em_box.open();
		new Ajax.Request(link, {
			onSuccess: function(data) {
				var html = data.responseText.evalJSON();
				$$('.top-link-cart').each(function(el) {
					el.innerHTML = html.toplink;
					el.title = html.toplink;
				});
				$$('.em-wrapper-topcart').each(function(el) {
					var newElement = new Element('div');
					newElement.update(html.sidebar);
					var div = newElement.select('div')[0];
					el.update(div.innerHTML);
				});
				$$('.em-wrapper-topcart-mobile .em-topcart-qty').each(function(el) {
                    el.innerHTML = html.qty;
        		});
				em_box.close();
				deleteItem();
			}
		});
	}
};

function validateDownloadableCallback(elmId, result) {
	container = $('downloadable-links-list');
	if (result == 'failed') {
		container.removeClassName('validation-passed');
		container.addClassName('validation-failed');
	} else {
		container.removeClassName('validation-failed');
		container.addClassName('validation-passed');
	}
};

function validateOptionsCallback(elmId, result) {
	var container = $(elmId).up('ul.options-list');
	if (result == 'failed') {
		container.removeClassName('validation-passed');
		container.addClassName('validation-failed');
	} else {
		container.removeClassName('validation-failed');
		container.addClassName('validation-passed');
	}
};

function ajaxaddnext() {
	if (ajcAddToCartForm.validator && ajcAddToCartForm.validator.validate()) {
		var options = {
			beforeSubmit: showRequestOptions,
			success: showResponse,
			dataType: 'json'
		};
		jQuery('#ajc_addtocart_form').ajaxForm(options);
		jQuery('#ajc_addtocart_form').submit();
	}
};
// pre-submit callback

function showRequestOptions(formData, jqForm, options) {
	$('ajax_loading').show();
	$('closeLink').hide();
	$('viewcart_button').hide();
	return true;
};

function ajax_changeQty(val) {
	qty = parseInt($('qty').value);
	if (!isNaN(qty)) {
		qty = val ? qty + 1 : (qty > 1 ? qty - 1 : 1);
		$('qty').value = qty;
	}
};