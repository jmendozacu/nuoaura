/**
* Lightbox
*
* This libary is used to create a lightbox in a web application.  This library
* requires the Prototype 1.6 library and Script.aculo.us core, effects, and dragdrop
* libraries.  To use, add a div containing the content to be displayed anywhere on 
* the page.  To create the lightbox, add the following code:
*
*	var test;
*	
*	Event.observe(window, 'load', function () {
*		test = new Lightbox('idOfMyDiv');
*	});
*	
*	Event.observe('lightboxLink', 'click', function () {
*		test.open();
*	});
*
*	Event.observe('closeLink', 'click', function () {
*		test.close();
*	});
*     
*/

var LightboxAJC = Class.create({
	open : function () {
		this._topWindow(this.container);		// Value : _centerWindow  or  _topWindow
		this._fade('open', this.container);
	},
	
	close : function () {
		this._fade('close', this.container);
	},
	
	_fade : function fadeBg(userAction,whichDiv){
		if(userAction=='close'){
			new Effect.Opacity('bg_fade',
					   {duration:.5,
					    from:0.5,
					    to:0,
					    afterFinish:this._makeInvisible,
					    afterUpdate:this._hideLayer(whichDiv)});
		}else{
			new Effect.Opacity('bg_fade',
					   {duration:.5,
					    from:0,
					    to:0.5,
					    beforeUpdate:this._makeVisible,
					    afterFinish:this._showLayer(whichDiv)});
		}
	},
	
	_makeVisible : function makeVisible(){
		$("bg_fade").setStyle({'visibility':'visible'});
	},

	_makeInvisible : function makeInvisible(){
		$("bg_fade").setStyle({'visibility':'hidden'});
	},

	_showLayer : function showLayer(userAction){
		$(userAction).setStyle({'display':'block'});
	},
	
	_hideLayer : function hideLayer(userAction){
		$(userAction).setStyle({'display':'none'});
	},
	
	_topWindow : function centerWindow(element) {
		var windowHeight = parseFloat($(element).getHeight())/2; 
		var windowWidth = parseFloat($(element).getWidth())/2;
		var w	=	document.viewport.getWidth()/2;
		var h	=	document.viewport.getHeight()/2;
		var pop_width	=	w-windowWidth;
		var pop_height	=	h-windowHeight-30;
		var tmp = $$('button.btn-cart')[0];
		if(typeof window.innerHeight != 'undefined') {
			$(element).style.top = '0px';
			$(element).style.left = Math.round(pop_width)+'px';
		} else {
			$(element).setStyle({'top':'0px'});
			$(element).setStyle({'left':Math.round(pop_width)+'px'});
		}
	},

	_centerWindow : function centerWindow(element) {
		var windowHeight = parseFloat($(element).getHeight())/2; 
		var windowWidth = parseFloat($(element).getWidth())/2;
		var w	=	document.viewport.getWidth()/2;
		var h	=	document.viewport.getHeight()/2;
		var pop_width	=	w-windowWidth;
		var pop_height	=	h-windowHeight-30;
		var tmp = $$('button.btn-cart')[0];
		if(typeof window.innerHeight != 'undefined') {
			$(element).style.top = Math.round(pop_height)+'px';
			$(element).style.left = Math.round(pop_width)+'px';
		} else {
			$(element).setStyle({'top':Math.round(pop_height)+'px'});
			$(element).setStyle({'left':Math.round(pop_width)+'px'});
		}
	},

	initialize : function(containerDiv) {
		this.container = containerDiv;		
		if($('bg_fade') == null) {
			var screen = new Element('div', {'id': 'bg_fade'});
			document.body.appendChild(screen);
		}
		//new Draggable(this.container);	//	event move lightbox 
		this._hideLayer(this.container);
		var temp = this;
		Event.observe(window, "resize", function() {
			temp._topWindow(temp.container);
		});
	}
});