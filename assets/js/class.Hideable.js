Hideable=function(){
	
	this.vertical_elements=false;
	this.horizontal_elements=false;
	
	this.init=function(){
		this.vertical_elements=$(".vertical-hideable");
		this.horizontal_elements=$(".horizontal-hideable");
		if (this.vertical_elements.length==0) this.vertical_elements=false;
		if (this.horizontal_elements.length==0) this.horizontal_elements=false;
		return this;
	}
	
	this._adjustVerticalHeights=function(){
		$(".xs-hideable-box").each(function(){
			var _height=0;
			if ($(this).css("height")=="0px") {
				$(this).attr("style","height:auto !important;");
				_height=$(this).height();
				$(this).removeAttr("style");
			} else {
				_height=$(this).height();
			}
			$(this).height(_height);
		});	
	}
	
	this.bindEvents=function(){
		if (this.vertical_elements!==false) {
			var _handler_obj=this;
			this.vertical_elements.each(function(){
				_id=$(this).attr("id");
				if ($("*[data-related-to='"+_id+"']").length>0) {
					$("*[data-related-to='"+_id+"']").unbind("click");
					$("*[data-related-to='"+_id+"']").bind("click",(_handler_obj._verticalHandlerClick).bind(_handler_obj));
				}
			});
		}
		if (this.horizontal_elements!==false) {
			var _handler_obj=this;
			this.horizontal_elements.each(function(){
				_id=$(this).attr("id");
				if ($("*[data-related-to='"+_id+"']").length>0) {
					$("*[data-related-to='"+_id+"']").unbind("click");
					$("*[data-related-to='"+_id+"']").bind("click",(_handler_obj._horizontalHandlerClick).bind(_handler_obj));
				}
			});
		}
		return this;
	}
	
	this._verticalHandlerClick=function(e){
		var _target_id=$(e.target).closest("*[data-related-to]").data("related-to");
		var _target=$("#"+_target_id);
		if (_target.length>0) {
			_target.toggleClass("vertical-hideable-hidden");
			if (_target.hasClass("xs-vertical-hideable-hidden")) this._adjustVerticalHeights();
			_target.toggleClass("xs-vertical-hideable-hidden");
			_target.find(".hideable-box").toggleClass("hideable-box-hidden");
			$(e.target).closest("*[data-related-to]").toggleClass("vertical-hideable-handler-hidden");
			_target.parent().find(".bordered-left-sidebar").toggleClass("bordered-left-sidebar-hidden");
			_target.parent().find(".bordered-right-sidebar").toggleClass("bordered-right-sidebar-hidden");
		}
	}
	
	this._horizontalHandlerClick=function(e){
		var _target_id=$(e.target).closest("*[data-related-to]").data("related-to");
		var _target=$("#"+_target_id);
		if (_target.length>0) {
			$(e.target).closest("*[data-related-to]").toggleClass("horizontal-hideable-handler-hidden");
			_target.slideToggle(300);
		}
	}
	
	return this.init();
}