/**
 * EM MegaMenu Core UI javascript
 *
 * @author Giao L. Trinh (giao.trinh@emthemes.com)
 * @copyright 2012 CodeSpot Software JSC., http://www.emthemes.com/
 * @license: commerical license purchased on http://www.emthemes.com/
 */
(function ($) {

var INDENT = 30;

EM_MegaMenu = {
	emWysiwygEditor: null
};


EM_MegaMenu.MenuItem = Class.create({
	$dom: null,
	data: null,

	textFieldId: '',

	initialize: function(el) {
		this.$dom = $(el);
		this.$dom.data('EM_MegaMenu_Object',  this);
		this.data = {};
				
		if (!this.$dom.hasClass('ui-widget'))
			this.$dom.addClass('ui-widget ui-widget-content ui-helper-clearfix ui-corner-all')
				.find('.menu-item-header').addClass("ui-widget-header ui-corner-all")
					.prepend( "<button class='ui-icon ui-icon-minusthick'></button>")
					.prepend( "<button class='ui-child ui-child-plusthick'></button>");

		if ($('input[name=type]', this.$dom).val() == 'text') {
			this.textFieldId = 'em_megamenu_text_' + unique();
			$('textarea[name=text]', this.$dom).attr('id', this.textFieldId);
		}

		this.initEvents();
		this.save();
	},
	getDepth: function(el) {
		var m = $(el).attr('class').match(/menu-item-depth-([0-9]+)/);
		return m ? parseInt(m[1]) : 0;
	},
	initEvents: function() {
		// toggle show/hide content
		$('.menu-item-header .ui-icon', this.$dom).click(this.toggleContent.bind(this, 'slide'));

		// toggle show/hide child
		$('.menu-item-header .ui-child', this.$dom).click(this.toggleChild.bind(this, 'slide'));

		// click WYSIWYG Editor button
		$('button.btn-wysiwyg, input.btn-wysiwyg', this.$dom).click(EM_MegaMenu.emWysiwygEditor.open.bind(EM_MegaMenu.emWysiwygEditor, this.textFieldId));

		// click Apply button
		$('button[name=apply], input[name=apply]', this.$dom).click(function() {
			this.save();
		}.bind(this));

		// click Revert button
		$('button[name=revert], input[name=revert]', this.$dom).click(this.revert.bind(this));

		// click Delete button
		$('button[name=delete], input[name=delete]', this.$dom).click(function() {
			var r=confirm("Are you sure to delete ?");
			if (r==true){
				this.remove();
			}
		}.bind(this));

		return this;
	},

	toggleChild: function(effect) {
		var depth = this.getDepth(this.$dom);
		var tmp = this;

		if (typeof effect == 'undefined') var effect = 'slide';
		$('.menu-item-header .ui-child', this.$dom).toggleClass( "ui-child-minusthick" ).toggleClass( "ui-child-plusthick" );
		var state = $('.ui-child-minusthick',this.$dom).length > 0 ? 'open' : 'close';
console.log(state);		
		this.$dom.nextAll('.menu-item')
			.each(function() {
				if (tmp.getDepth($(this)) <= depth) return false;
				if(state == 'close'){
					$(this).hide();
					$('.menu-item-header .ui-child',$(this)).removeClass( "ui-child-minusthick" ).addClass( "ui-child-plusthick" );
				} else {
					if(tmp.getDepth($(this)) == depth + 1){
						$(this).show();
					}
				}
			});	
		return this;
	},
	toggle: function(effect) {
		this.toggleContent(effect);
		//this.toggleChild(effect);
	},
	toggleContent: function(effect) {
		if (typeof effect == 'undefined') var effect = 'slide';
		
		$('.menu-item-header .ui-icon', this.$dom).toggleClass( "ui-icon-minusthick" ).toggleClass( "ui-icon-plusthick" );
		if (effect == 'slide')
			$('.menu-item-content', this.$dom).slideToggle();
		else
			$('.menu-item-content', this.$dom).toggle();
			
		return this;
	},
		
	save: function() {
		$('input, textarea, select', this.$dom).each(function(item) {
			var type = $(this).attr('type') ? $(this).attr('type').toUpperCase() : '';
			if (type == 'BUTTON' || type == 'SUBMIT' || !this.name) return;
			item.data[this.name] = $(this).val();

			// change menu item's header
			if (this.name == 'label' || this.name == 'title') 
				$('.menu-item-header .title', item.$dom).html($(this).val());
		}.curry(this));
		return this;
	},
	
	revert: function() {
		$('input, textarea, select', this.$dom).each(function(item) {
			var type = $(this).attr('type') ? $(this).attr('type').toUpperCase() : '';
			if (type == 'BUTTON' || type == 'SUBMIT' || !this.name || typeof item.data[this.name] == 'undefined') return
			$(this).val(item.data[this.name]);
		}.curry(this));
		return this;
	},
	
	remove: function() {
		var tmp = this;
		var depth = this.getDepth(this.$dom);
		this.$dom.nextAll('.menu-item').not('.ui-sortable-placeholder')
			.each(function() {
				if (tmp.getDepth($(this)) <= depth) return false;
				$(this).fadeOut(100,$(this).remove.bind($(this)));
			});	
		this.$dom.fadeOut(300, this.$dom.remove.bind(this.$dom));
	}
});
var MenuItem = EM_MegaMenu.MenuItem;



EM_MegaMenu.FixedMenuItem = Class.create(MenuItem, {
	initialize: function($super, el, canvas) {
		$super(el);
		this.$dom.draggable({
			helper: 'clone',
			connectToSortable: $('.dropzone', canvas.$dom),
			cursor: 'move',
			tolerance: 'pointer',
			revert: 'invalid',
			/*drag:  _.throttle(self.dragAdjust, 500 ),*/
			stop: function(e, ui) {
				var ele = ui.helper;
				if($(ele).hasClass('group')){	
					depth = canvas.getDepth($(ele));
					var transport = $('.menu-item-transport', $(ele));
					$('.menu-item-header .ui-icon',$(ele)).removeClass( "ui-icon-plusthick" ).addClass( "ui-icon-minusthick" );
					$('.menu-item-header .ui-child',$(ele)).removeClass( "ui-child-plusthick" ).addClass( "ui-child-minusthick" );
					$(this).nextAll('.menu-item').not('.ui-sortable-placeholder')
						.each(function() {
							if (canvas.getDepth(this) <= depth) return false;
							
							var childDepth = canvas.getDepth(this);
							$(this).clone().appendTo(transport).add(this)
									.removeClass('menu-item-depth-' + childDepth)
									.addClass('menu-item-depth-' + (childDepth - depth))
									.addClass('clone')
									;
										
							canvas._childrenOfDragging.push( $(this).clone() );
						});
				}
			},
		});
		//this.$dom.drag( jQuery.throttle( self.dragAdjust, 500 ) );
		//}).disableSelection();
	}
});
var FixedMenuItem = EM_MegaMenu.FixedMenuItem;



EM_MegaMenu.Canvas = Class.create({
	$dom: null,
	_childrenOfDragging: null,
	
	initialize: function(el) {
		this.$dom = $(el);
		this.$dom.data('EM_MegaMenu_Object',  this);
		var canvas = this;

		$('.menu-item', this.$dom).each(function() {
			canvas.createMenuItem(this).toggle('none');
		});

		$('.dropzone', this.$dom).sortable({
			items: '.menu-item',
			cursor: 'move',
			tolerance: 'pointer',
			revert: true,

			start: $.throttle(300,function(event, ui) {
				canvas.prepareTransport(ui.item);
			}),

			stop: $.throttle(300,function(event, ui) {

				// clone the MenuItem
				if (!$(ui.item).data('EM_MegaMenu_Object') || !($(ui.item).data('EM_MegaMenu_Object') instanceof MenuItem)) {
					$(ui.item).addClass('menu-item-depth-0');
					canvas.createMenuItem(ui.item);
				}

				// update depth level
				var depth = canvas.getDepth(ui.placeholder);
				var orgDepth = canvas.getDepth(ui.item);

				$(ui.item).removeClass('menu-item-depth-' + orgDepth)
					.addClass('menu-item-depth-' + depth);

				canvas.restoreTransport(ui.item);
			}),

			over: $.throttle(300,function(event, ui) {
				var mi, mp, parent, depth, parentDepth;
				depth = canvas.getDepth(ui.placeholder);

				$(ui.placeholder).removeClass('menu-item-depth-' + depth);

				parent = $(ui.placeholder).prevAll('.menu-item:visible').not(ui.item).first();
				if (parent.length > 0) {
					parentDepth = canvas.getDepth(parent);
					if (ui.position.left - ui.originalPosition.left >= INDENT)
						depth = Math.min(depth + 1, parentDepth + 1);
					else if (ui.originalPosition.left - ui.position.left >= INDENT)
						depth = Math.max(parentDepth - Math.round((ui.originalPosition.left - ui.position.left)/INDENT) + 1, 0);
				}
				else depth = 0;

				$(ui.placeholder).addClass('menu-item-depth-' + depth);
			})
		});
	},

	prepareTransport: function(el) {
		// clone child menu-item to the transport div
		var canvas = this;
		var depth = canvas.getDepth(el);
		var transport = $('.menu-item-transport', el);

		canvas._childrenOfDragging = [];

		$(el).nextAll('.menu-item').not('.ui-sortable-placeholder')
			.each(function() {
				if (canvas.getDepth(this) <= depth) return false;
				var childDepth = canvas.getDepth(this);
				$(this).clone().appendTo(transport)
					.add(this)
						.removeClass('menu-item-depth-' + childDepth)
						.addClass('menu-item-depth-' + (childDepth - depth))

				canvas._childrenOfDragging.push( $(this).hide() );
			});
	},

	restoreTransport: function(el) {
		var canvas = this;
		var parentDepth = canvas.getDepth(el);
		
		$(canvas._childrenOfDragging).each(function() {
			var oldDepth = canvas.getDepth(this);
			var newDepth = parentDepth + oldDepth;
			$(this).removeClass('menu-item-depth-' + oldDepth)
				.addClass('menu-item-depth-' + newDepth)
				.show();
			if($(this).hasClass('clone')){
				new FixedMenuItem(this, canvas);
				$(this).removeClass('clone');
			}	
		}).insertAfter(el);
		
		$('.menu-item-transport', el).html('');
	},

	createMenuItem: function(el) {
		if ($('.menu-item-transport', el).length == 0)
			$(el).append('<div class="menu-item-transport"></div>');
			
		return new MenuItem(el);
	},

	getDepth: function(el) {
		var m = $(el).attr('class').match(/menu-item-depth-([0-9]+)/);
		return m ? parseInt(m[1]) : 0;
	},

	updateMenuNest: function() {

	},

	serializeArray: function() {
		var canvas = this;
		var results = [];
		$('.menu-item', this.$dom).each(function() {
			if ($(this).data('EM_MegaMenu_Object')) {
				var data = $.extend(true, {}, $(this).data('EM_MegaMenu_Object').data);
				data['depth'] = canvas.getDepth(this);
				results.push(data);
			}
		});
		return results;
	},

	serialize: function(key) {
		if (!key) key = 'menu';
		
		var result = {};
		result[key] = this.serializeArray();
		return $.param(result);
	}

});
var Canvas = EM_MegaMenu.Canvas;

EM_MegaMenu.Toolbox = Class.create({
	$dom: null,

	initialize: function(el, canvas) {
		this.$dom = $(el);
		this.$dom.data('EM_MegaMenu_Object',  this);
		
		$('.menu-item', this.$dom).each(function() {
			new FixedMenuItem(this, canvas);
		});
	}
});
var Toolbox = EM_MegaMenu.Toolbox;

function unique() {
	time = new Date().getTime();
	var number = Math.floor(Math.random() * 999);
	return time+''+number;
}

})(jQuery);

	var emmenu_global_url;
	jQuery.fn.megamenucenter = function () {
		var $ = jQuery;
		this.css("position","absolute");
		this.css("top", Math.max(0, (($(window).height() - $(this).outerHeight()) / 2) + 
													$(window).scrollTop()) + "px");
		this.css("left", Math.max(0, (($(window).width() - $(this).outerWidth()) / 2) + 
													$(window).scrollLeft()) + "px");
		return this;
	}

	function variableshow(e) {
		var $ = jQuery;
		var div = $(".em_megamenu_variable");
		var td = $(e).parents("td");
		emmenu_global_url = td.find(".input_emvariable");

		$(".em_megamenu_bg").show();
		div.megamenucenter();
		div.show();
	}

	function variablehide() {
		var $ = jQuery;
		$(".em_megamenu_bg").hide();
		$(".em_megamenu_variable").hide();
	}

	function iconshow(e) {
		var $ = jQuery;
		var div = $(".em_megamenu_icon");
		var td = $(e).parents("td");
		emmenu_global_url = td.find(".input_emicon");

		$(".em_megamenu_bg").show();
		div.megamenucenter();
		div.show();
	}

	function iconhide() {
		var $ = jQuery;
		$(".em_megamenu_bg").hide();
		$(".em_megamenu_icon").hide();
	}

	function insert_url(url) {
		var $ = jQuery;
		emmenu_global_url.val(url);
		variablehide();
		iconhide();
	}

	function upAjaxImage(){
		var $ = jQuery;
		var options = {
			beforeSubmit:  showRequest,
			success:       showResponse,
			dataType: 'json'
		};
		jQuery('#upload_form').ajaxForm(options);
		jQuery('#upload_form').submit();
	}

	// pre-submit callback
	function showRequest(formData, jqForm, options) {
		jQuery('.em_btn_upload').attr("disabled", "disabled");
		jQuery('.img_loading').show();
		return true;
	}

	// post-submit callback
	function showResponse(responseText, statusText, xhr, $form)  {
		var rs = responseText;
		var classmsg;
		var url_del = 	jQuery('.em_megamenu_icon .emicon_urldel').html();

		if(rs.check == 1 )	classmsg = 'success-msg';
		else	classmsg = 'error-msg';
		var msg = '<ul class="messages"><li class="'+classmsg+'"><ul><li><span>'+rs.msg+'</span></li></ul></li></ul>';
		jQuery('.emicon_message').html(msg);
		setTimeout(function(){ jQuery(".emicon_message").html(''); }, 5000);

		if(rs.check == 1){
			var num = jQuery(".icon_num").html();
			var i = parseInt(num)+1;
			var html	= 	'<li class="item vid_'+i+'">';
				html	+=		'<div class="row_1">'+i+'</div>';
				html	+=		'<div class="row_2"><a onclick="insert_url(\''+rs.img_url+'\')" href="javascript:void(0);"><img src="'+rs.img_name+'" style="width:55px;height:55px;"/> '+rs.name+'</a></div>';
				html	+=		'<div class="row_3"><a href="javascript:void(0);" onclick="delAjaxImage(\''+url_del+'\',\''+rs.name+'\',\''+i+'\')" title="Delete"><span>Delete</span></a></div>';
				html	+=	'</li>';

			jQuery('.em_megamenu_icon .icon_num').html(i);
			jQuery('.em_megamenu_icon .emicon_info ul').append(html);
		}
		jQuery('.img_loading').hide();
		jQuery('.em_btn_upload').removeAttr("disabled");
		jQuery('#edit_form').attr("action", jQuery('.old_action').html());
	}

	function delAjaxImage(url,name,id){
		var $ = jQuery;
		var r=confirm("Are you sure to delete ?");
		if (r==true){
			var param = { file: name };
			$.get( url, param )
				.done(function( data ) {
					if(data == 1){
						$(".vid_"+id).remove();
					}
				});
		}
	}

	jQuery(document).ready(function() {
		var $ = jQuery;

		$(".emvariable_subcat").click(function() {
			var div = $(this);
			var id = div.html();
			
			if(div.hasClass("collapse")) div.removeClass("collapse").addClass("expand");
			else div.removeClass("expand").addClass("collapse");
			div.parents("li").find(".emvariable-parent-"+id).slideToggle();

		});
	});