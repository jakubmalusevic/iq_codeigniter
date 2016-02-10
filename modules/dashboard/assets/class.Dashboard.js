Dashboard=function(){
	
	this.init=function(){
		$(document).ready(function(){
			if ($(".sortable-widgets-container").length>0) {
				$(".sortable-widgets-container").sortable({
					handle:".widget-header",
					placeholder:"moving-widget-box",
					start: function(event,ui) {
						ui.placeholder.height(ui.helper.height()-4);
						ui.placeholder.width(ui.helper.width()-4);
					},
					stop: function(event,ui) {
						var _store=new Array();
						var _start_order=10;
						var _i=_start_order;
						$(".sortable-widgets-container").find(".single-widget-box").each(function(){
							var widget_name=$(this).attr("widget-name");
							if (typeof widget_name=="undefined") widget_name="";
							var widget_state=$(this).attr("widget-state");
							if (typeof widget_state=="undefined") widget_state="";							
							$(this).attr("widget-order",_i);
							var _store_row={};
							_store_row.name=widget_name;				
							_store_row.state=widget_state;	
							_store_row.order=_i;
							_store.push(_store_row);
							_i+=10;
						});
						$.post(base_url+"dashboard/main/rearrange",{data:_store});
					}
				});
				$(".sortable-widgets-container").disableSelection();
			}
		}.bind(this));
		$(document).on("click",".widget-collapse-button",function(){
			var _widget_name=$(this).closest(".single-widget-box").attr("widget-name");
			if (typeof _widget_name=="undefined") _widget_name="";
			var _widget_order=$(this).closest(".single-widget-box").attr("widget-order");
			if (typeof _widget_order=="undefined") _widget_order=10;			
			var _state=0;
			if ($(this).hasClass("closed")){
				$(this).removeClass("closed");
				_state=1;
				$(this).closest(".single-widget-box").attr("widget-state",_state);
				$(this).closest(".single-widget-box").find(".widget-content").stop().slideDown(300,function(){
					if (_widget_name!="") {
						$.post(base_url+"dashboard/main/changestate",{widget:_widget_name,state:_state,order:_widget_order});
					}				
				});
			} else {
				$(this).addClass("closed");
				_state=2;
				$(this).closest(".single-widget-box").attr("widget-state",_state);
				$(this).closest(".single-widget-box").find(".widget-content").stop().slideUp(300,function(){
					if (_widget_name!="") {
						$.post(base_url+"dashboard/main/changestate",{widget:_widget_name,state:_state,order:_widget_order});
					}				
				});
			}
		});
		$(document).on("click",".widget-close-button",function(){
			var _widget_name=$(this).closest(".single-widget-box").attr("widget-name");
			if (typeof _widget_name=="undefined") _widget_name="";	
			var _widget_order=$(this).closest(".single-widget-box").attr("widget-order");
			if (typeof _widget_order=="undefined") _widget_order=10;				
			var _state=0;
			$(this).closest(".single-widget-box").attr("widget-state",_state);		
			$(this).closest(".single-widget-box").stop().slideUp(300,function(){
				if (_widget_name!="") {
					$.post(base_url+"dashboard/main/changestate",{widget:_widget_name,state:_state,order:_widget_order});
				}				
			});
		});
		$(document).on("change","*[change-widget-state]",function(e){
			setTimeout(function(){
				var _widget_name=$(this).val();
				if ($("*[widget-name='"+_widget_name+"']").length>0) {
					var _widget_order=$("*[widget-name='"+_widget_name+"']").attr("widget-order");
					if (typeof _widget_order=="undefined") _widget_order=10;					
					if (this.checked) {			
						var _state=1;
						$("*[widget-name='"+_widget_name+"']").attr("widget-state",_state);		
						$("*[widget-name='"+_widget_name+"']").find(".widget-content").show();
						$("*[widget-name='"+_widget_name+"']").find(".widget-collapse-button").removeClass("closed");
						$("*[widget-name='"+_widget_name+"']").stop().slideDown(300,function(){
							$.post(base_url+"dashboard/main/changestate",{widget:_widget_name,state:_state,order:_widget_order});
						});					
					} else {
						var _state=0;
						$("*[widget-name='"+_widget_name+"']").attr("widget-state",_state);		
						$("*[widget-name='"+_widget_name+"']").stop().slideUp(300,function(){
							$.post(base_url+"dashboard/main/changestate",{widget:_widget_name,state:_state,order:_widget_order});
						});						
					}
				}
			}.bind(this),1);
		});
	}
	
	return this.init();

}
var dashboard=new Dashboard();