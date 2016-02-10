Navigation=function(selector){
	
	this.selector=false;
	this.handler=false;
	this.navigation_handler=false;
	
	this.init=function(selector){
		if (typeof selector=="undefined") selector="";
		if (selector!="") {
			this.selector=selector;
			this.handler=$(selector);
			if (this.handler.length==0) this.handler=false;
		}
		return this;
	}
	
	this.adjustSize=function(){
		if (this.handler!==false){
			var _total_width=this.handler.width();
			var _total_count=this.handler.children("li").length;
			var _cell_width=Math.floor(_total_width/_total_count);
			var _left_width=_total_width-_cell_width*_total_count;
			var _i=0;
			this.handler.children("li").each(function(){
				$(this).css("width",(100/_total_count)+"%");
				_i++;
			});
			this.adjustHeight();
		}
		return this;
	}
	
	this.adjustHeight=function(){
		if (this.handler!==false){
			var _max_height=0;
			this.handler.children("li").each(function(){
				$(this).children("a").css("height","auto");
				_this_height=$(this).children("a").height();
				if (_this_height>_max_height) _max_height=_this_height;
			});		
			if (_max_height>0) {
				this.handler.children("li").each(function(){
					$(this).children("a").height(_max_height);
				});
			}			
		}
	}
	
	this.setNavigationHandler=function(selector){
		this.navigation_handler=$(selector);
		if (this.navigation_handler.length==0) this.navigation_handler=false;
		return this;
	}
	
	this.bindEvents=function(){
		$(window).unbind("resize",(this.adjustHeight).bind(this));
		$(window).bind("resize",(this.adjustHeight).bind(this));
		if (this.navigation_handler!==false) {
			this.navigation_handler.unbind("click",(this._navigationHandlerClick).bind(this));
			this.navigation_handler.bind("click",(this._navigationHandlerClick).bind(this));
			$(document).unbind("click",(this._navigationHandlerClickByDocument).bind(this));
			$(document).bind("click",(this._navigationHandlerClickByDocument).bind(this));
		}
		return this;
	}
	
	this._navigationHandlerClick=function(){
		if (this.handler!==false){
			this.handler.stop().slideToggle(300);
		}
		if (this.navigation_handler!==false) {
			this.navigation_handler.toggleClass("opened");
		}
	}
	
	this._navigationHandlerClickByDocument=function(e){
		if ($(e.target).closest(".navigation-handler").length==0) {
			if (this.handler!==false){
				if (this.handler.css("display")!="none") {
					this.handler.stop().slideUp(300);
				}
			}
			if (this.navigation_handler!==false) {
				if (this.navigation_handler.hasClass("opened")) {
					this.navigation_handler.removeClass("opened");
				}
			}		
		}
	}
	
	this.getActiveModule=function(){
		var _return=location.href.replace(base_url,"");
		_return=_return.split("/")[0].toLowerCase();
		return _return;
	}
	
	this.reload=function(){
		var _wrapper=this.handler.closest("header");
		var _active_module=this.getActiveModule();
		if (_wrapper.length>0) {
			var _cover=_wrapper.find(".reload-navigation-cover");
			if (_cover.length==0) {
				_wrapper.append('<div class="reload-navigation-cover"><span class="grey-middle-loader">'+lang['refreshing']+'</span></div>');
				_cover=_wrapper.find(".reload-navigation-cover");
			}
			_cover.fadeIn(300,function(){
				$.post(base_url+"helpers/drawNavigation",{active_module:_active_module},function(data){
					_wrapper.find(".navigation-wrapper:eq(0)").html(data);
					_navigation=new Navigation("#primary-navigation").adjustSize().setNavigationHandler("#navigation-handler").bindEvents();
					_cover.fadeOut(300,function(){
						$(this).remove();
					});
				});
			}.bind(this));
		}
	}
	
	return this.init(selector);
}