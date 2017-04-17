function em_expand(number){
	var div = jQuery('.num_'+number);
	if(div.hasClass("expand")){
		div.removeClass("expand");
	}else{
		div.addClass("expand");
	}
}