Popup=function(params){
	
	this.init=function(params){
		if (typeof params=="undefined") params={};
		var _type="notification";
		var _title="";
		var _message="";
		var _buttons=new Array();
		if (typeof params.type!="undefined") _type=params.type;
		if (typeof params.title!="undefined") _title=params.title;
		if (typeof params.message!="undefined") _message=params.message;
		if (typeof params.buttons!="undefined") _buttons=params.buttons;
		var _popup_object=this;
		var _timeout=0;
		if (_events._opened_popup!==false) {
			$.magnificPopup.close();
			_timeout=300;
		}
		setTimeout(function(){		
			_events._opened_popup=$.magnificPopup.open({
				items:{
					src:_popup_object._generatePopup(_type,_title,_message,_buttons)
				},
				type:'inline',
				mainClass:'anim-mfp-slide-bottom',
				removalDelay:300,
				closeOnBgClick:false,
				callbacks:{
					close: function() {
						_events._opened_popup=false;
						if (_events._last_opened!==false) {
							setTimeout(function(){	
								_events._loadPopup(_events._last_opened);
							},300);
						}					
					}
				}
			},0);
		},_timeout);
		return this;
	}
	
	this._generatePopup=function(type,title,message,buttons){
		var _icon='';
		if (type=="warning") {
			_icon='<i class="typcn typcn-warning popup-icon red-icon"></i>';
		}
		if (type=="notification") {
			_icon='<i class="typcn typcn-info popup-icon green-icon"></i>';
		}
		if (type=="confirmation") {
			_icon='<i class="typcn typcn-input-checked popup-icon white-icon"></i>';
		}			
		var _buttons='';
		for(var b=0;b<buttons.length;b++){
			var _href="";
			var _onclick="";
			if (typeof buttons[b].params.href!="undefined") _href=buttons[b].params.href;
			if (typeof buttons[b].params.onclick!="undefined") _onclick=buttons[b].params.onclick;
			_buttons+='\
				<a href="'+(_href!=""?_href:"#")+'" class="button medium-button '+(buttons[b].type=="confirm"?"primary":"secondary")+'-button'+(buttons[b].type=="close"?" close-modal-window":"")+'"'+(_onclick!=""?' onclick="'+_onclick+'"':"")+'>\
					'+buttons[b].title+'\
				</a>\
			';
		}
		var _html='\
			<div class="modal-wrapper column-3" id="temporary-popup">\
				<div class="modal-header">\
					'+_icon+title+'\
				</div>\
				<div class="modal-content">\
					'+message+'\
				</div>\
				<div class="modal-footer">\
					'+_buttons+'\
				</div>\
			</div>\
		';
		return _html;
	}
	
	return this.init(params);
}