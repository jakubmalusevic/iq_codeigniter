Tabs=function(selector){
	
	this.selector=false;
	this.handler=false;
	
	this.init=function(selector){
		this.selector=selector;
		this.handler=$(selector);
		if (this.handler.length==0) this.handler=false;
		if (this.handler!==false) {
			if (this.handler.children("li").length<2) this.handler.addClass("tabs-list-no-arrow");
		}
		this._hideDefaultTabs();
		return this;
	}
	
	this._hideDefaultTabs=function(){
		if (this.handler!==false) {
			var _active_tab_handler=this.handler.children("li.active").children("a");
			if (_active_tab_handler.length>0) {
				var _local_tab=false;
				if (_active_tab_handler.attr("href").substr(0,1)=="#") _local_tab=true;
				if (_local_tab) {
					var _tab_name="";
					if (_local_tab) _tab_name=_active_tab_handler.attr("href").substr(1);		
					if (_tab_name!="") {
						$(".tab-container").not("#tab-"+_tab_name).hide();
						$("#tab-"+_tab_name).show();						
					}
				}
			}
		}
	}
	
	this.bindEvents=function(){
		if (this.handler!==false) {
			this.handler.children("li").find("a:first").unbind("click",(this._tabsClickEvent).bind(this));
			this.handler.children("li").find("a:first").bind("click",(this._tabsClickEvent).bind(this));
			$(document).unbind("click",(this._hideTabsListByDocumentClick).bind(this));
			$(document).bind("click",(this._hideTabsListByDocumentClick).bind(this));			
		}
		return this;
	}
	
	this._hideTabsListByDocumentClick=function(e){
		if ($(e.target).closest(".tabs-list").length==0) {
			$(".opened-tabs-list").children("li").not(".active").slideUp(300,function(){
				$(this).removeAttr("style");
			});
			setTimeout(function(){
				$(".opened-tabs-list").children("li.active").removeAttr("style");
			},300);			
			$(".opened-tabs-list").removeClass("opened-tabs-list");
		}
	}
	
	this._tabsClickEvent=function(e){
		var _target=$(e.target);
		if (!_target.hasClass("typcn")) {
			var _local_tab=false;
			if (_target.attr("href").substr(0,1)=="#") _local_tab=true;
			var _tab_name="";
			if (_local_tab) _tab_name=_target.attr("href").substr(1);
			if (this._checkIsAbsolutePosition()) {
				if (this.handler.children("li").length>1) {
					if (this._checkIsHidden()) {
						this.handler.addClass("opened-tabs-list");
						this.handler.children("li").not(".active").slideDown(300);
						e.preventDefault();
						return false;
					} else {
						if (_target.closest("li").hasClass("active")) {
							this.handler.removeClass("opened-tabs-list");
							this.handler.children("li").not(".active").slideUp(300,function(){
								$(this).removeAttr("style");
							});		
							setTimeout(function(){
								this.handler.children("li.active").removeAttr("style");
							}.bind(this),300);
							e.preventDefault();
							return false;								
						} else {
							if (_local_tab) {
								this.displayTab(_tab_name);
								setTimeout(function(){
									this.handler.removeClass("opened-tabs-list");
								}.bind(this),1);
								this.handler.children("li").not(".active").slideUp(300,function(){
									$(this).removeAttr("style");
								});
								setTimeout(function(){
									this.handler.children("li.active").removeAttr("style");
								}.bind(this),300);							
								e.preventDefault();
								return false;						
							}
						}
					}						
				} else {
					if (!_target.closest("li").hasClass("active")) {
						if (_local_tab) {
							this.displayTab(_tab_name);	
							e.preventDefault();
							return false;
						}
					} else {
						e.preventDefault();
						return false;				
					}
				}
			} else {
				if (_local_tab) {
					this.displayTab(_tab_name);
					e.preventDefault();
					return false;						
				}			
			}
		} else {
			e.preventDefault();
			return false;
		}
	}
	
	this.displayTab=function(tab_name){
		if (tab_name!="") {
			var _parent=$("*[href='#"+tab_name+"']").closest("ul");
			_parent.children("li.active").removeClass("active");
			$("*[href='#"+tab_name+"']").closest("li").addClass("active");
			$(".tab-container").not("#tab-"+tab_name).hide();
			$("#tab-"+tab_name).show();
		}
	}
	
	this._checkIsAbsolutePosition=function(){
		var result=false;
		if (this.handler!==false) {
			_position=this.handler.css("position");
			if (_position=="absolute") result=true;
		}
		return result;
	}
	
	this._checkIsHidden=function(){
		var result=false;
		if (this.handler!==false) {
			if (this._checkIsAbsolutePosition()) {
				_at_least_one_hidden=false;
				this.handler.children("li").each(function(){
					if ($(this).css("display")=="none") _at_least_one_hidden=true;
				});
				if (_at_least_one_hidden) result=true;
			}
		}
		return result;
	}
	
	return this.init(selector);
}