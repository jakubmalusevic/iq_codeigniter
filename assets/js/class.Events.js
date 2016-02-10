Events=function(){

	this._opened_popup=false;
	this._last_opened=false;
	this._popups_history=new Array();

	this.init=function(){
		this.bindEvents();
	}
	
	this._loadPreviousPopup=function(){
		if (_events._popups_history.length>1 && _events._popups_history[_events._popups_history.length-2]!="") {
			setTimeout(function(){	
				_events._loadPopup(_events._popups_history[_events._popups_history.length-2]);
			},350);
		}		
	}
	
	this._loadPopup=function(_href){
		_events._last_opened=_href;
		_events._popups_history.push(_href);
		_events._opened_popup=$.magnificPopup.open({
			items:{
				src:_href
			},
			type:'ajax',
			mainClass:'anim-mfp-slide-bottom',
			removalDelay:300,
			closeOnBgClick:false,
			tLoading: '<span class="grey-middle-loader">'+lang['loading']+'</span>',
			callbacks:{
				ajaxContentAdded:function(){
					visualHelpers.init();
					_events.initDraggable();
					_hideable.init().bindEvents();
				},
				close:function() {
					_events._opened_popup=false;
				}
			}
		},0);	
	}
	
	this.bindEvents=function(){
		$(document).on("click",".modal-window",function(e){
			$("*[tooltip-text]").trigger("mouseleave");
			var _href=$(this).attr("href");
			var _is_local_path=false;
			if (_href.substr(0,1)=="#") _is_local_path=true;
			if (!_is_local_path) {
				var _timeout=0;
				if (_events._opened_popup!==false) {
					$.magnificPopup.close();
					_timeout=300;
				}
				setTimeout(function(){
					_events._loadPopup(_href);
				},_timeout);
				e.preventDefault();
				return false;
			}
		});
		$(document).on("click",".close-modal-window",function(e){
			$.magnificPopup.close();
			e.preventDefault();
			return false;
		});
		$(document).on("submit","*[validate-form='true']",function(e){
			if (typeof formValidator!="undefined") {
				$(this).data("valid","true");
				if (!formValidator.validate(this)) {
					$(this).data("valid","false");
					e.preventDefault();
					return false;
				}
			}
		});	
		$(document).on("submit","*[ajax-form='true']",function(e){
			var _form_valid=$(this).data("valid");
			if (typeof _form_valid=="undefined") _form_valid="true";
			if (_form_valid=="true") _form_valid=true;
			else _form_valid=false;
			if (_form_valid) {
				var _form_wrapper=$(this).closest(".ajax-form-wrapper");
				if (_form_wrapper.length>0) {
					var _form_loader=_form_wrapper.find(".ajax-form-loader");
					if (_form_loader.length==0) {
						_form_wrapper.append('<div class="ajax-form-loader"><span class="grey-middle-loader">'+lang['loading']+'</span></div>');
						_form_loader=_form_wrapper.find(".ajax-form-loader");
					}
					_form_loader.fadeTo(300,1);
				}
				var _callback=$(this).attr("callback");
				if (typeof _callback=="undefined") _callback="";
				var _form_options={
					success:function(response){
						if (_callback!="") {
							eval(_callback);
						}
						if (_form_loader.length>0) {
							_form_loader.fadeTo(300,0,function(){
								$(this).remove();
							});
						}
					}.bind(this)
				};
				$(this).ajaxSubmit(_form_options);
				e.preventDefault();
				return false;				
			}
		});			
		$(document).on("keyup","*[page-field='true']",function(e){
			if (e.keyCode==13) {
				var _default_value=$(this).attr("default-value");
				if (typeof _default_value=="undefined") _default_value="";
				_default_value=_default_value.replace(/[^0-9]/gi,"");
				if (_default_value=="") _default_value="1";
				var _value=$.trim($(this).val()).replace(/[^0-9]/gi,"");
				if (_value=="") _value=_default_value;
				if (parseFloat(_value)<1) _value="1";
				$(this).val(_value);
				if (_value!=_default_value){
					var _page_regexp=/(\?|&)page=([^\?&]+)/gi;
					var _href=location.href;
					if (_page_regexp.exec(_href)) {
						_href=_href.replace(_page_regexp,"\$1page="+(parseFloat(_value)-1));
					} else {
						_href=_href+(_href.indexOf("?")==-1?"?":"&")+"page="+(parseFloat(_value)-1);
					}	
					location.href=_href;				
				}
			}
		});			
		$(document).on("click",".checkbox-wrapper",function(e){
			$(this).find("input[type='checkbox']").trigger("click");
		});
		$(document).on("change","input[type='checkbox']",function(){
			var _checkbox_obj=this;
			setTimeout(function(){
				if (_checkbox_obj.checked) {
					$(_checkbox_obj).closest(".checkbox-wrapper").addClass("checkbox-wrapper-checked");
				} else {
					$(_checkbox_obj).closest(".checkbox-wrapper").removeClass("checkbox-wrapper-checked");
				}
			},1);
		});
		$(document).on("click","input[type='checkbox']",function(e){
			e.stopPropagation();
		});		
		$(document).on("focus","input[type='checkbox']",function(){
			$(this).closest(".checkbox-wrapper").addClass("checkbox-wrapper-focused");
		});
		$(document).on("blur","input[type='checkbox']",function(){
			$(this).closest(".checkbox-wrapper").removeClass("checkbox-wrapper-focused");
		});		
		$(document).on("click",".radio-wrapper",function(e){
			if ($(this).find("input[type='radio']").length>0) {
				$(this).find("input[type='radio']").trigger("click");
			}
		});
		$(document).on("click","input[type='radio']",function(e){
			e.stopPropagation();
		});
		$(document).on("change","input[type='radio']",function(){
			var _radio_obj=this;
			setTimeout(function(){
				if (_radio_obj.checked) {
					$(_radio_obj).closest(".radio-wrapper").addClass("radio-wrapper-checked");
				} else {
					$(_radio_obj).closest(".radio-wrapper").removeClass("radio-wrapper-checked");
				}
				var _name=$(_radio_obj).attr("name");
				if (typeof _name=="undefined") _name="";
				if (_name!="") {
					$("input[type='radio'][name='"+_name+"']").not(":checked").closest(".radio-wrapper").removeClass("radio-wrapper-checked");
				}				
			},1);
		});
		$(document).on("focus","input[type='radio']",function(){
			$(this).closest(".radio-wrapper").addClass("radio-wrapper-focused");
		});
		$(document).on("blur","input[type='radio']",function(){
			$(this).closest(".radio-wrapper").removeClass("radio-wrapper-focused");
		});	
		$(document).on("click",".file-wrapper",function(){
			$(this).find("input[type='file']").trigger("click");
		});
		$(document).on("click","input[type='file']",function(e){
			e.stopPropagation();
		});		
		$(document).on("change","input[type='file']",function(){
			var _value=$(this).val().split("/");
			var _filename=_value[_value.length-1];
			$(this).closest(".file-wrapper").find(".file-wrapper-file-name").html(_filename);
		});			
		$(document).on("click",".popup-action",function(e){
			$("*[tooltip-text]").trigger("mouseleave");
			var _href=$(this).attr("href");
			if (typeof _href=="undefined") _href="";
			var _popup_type=$(this).attr("popup-type");
			if (typeof _popup_type=="undefined") _popup_type="notification";
			if (_popup_type!="notification" && _popup_type!="warning" && _popup_type!="confirmation") _popup_type="notification";
			var _popup_title=$(this).attr("popup-title");
			if (typeof _popup_title=="undefined") _popup_title="";
			if (_popup_title=="") {
				if (_popup_type=="notification") _popup_title=lang['notification'];
				if (_popup_type=="warning") _popup_title=lang['warning'];
				if (_popup_type=="confirmation") _popup_title=lang['confirm'];
			}
			var _popup_message=$(this).attr("popup-message");
			if (typeof _popup_message=="undefined") _popup_message="";
			var _popup_buttons=$(this).attr("popup-buttons");
			if (typeof _popup_buttons=="undefined") _popup_buttons="";			
			_popup_buttons=_popup_buttons.split(",");
			var _buttons=new Array();
			for(var b=0;b<_popup_buttons.length;b++){
				var _temp=_popup_buttons[b].split(":");
				if (_temp.length>1) {
					var _button={};
					_button.title=_temp[1];
					_button.type=_temp[0];
					_button.params={};
					if (_temp[0]=="confirm") {
						_button.params['href']=_href;
					}
					_buttons.push(_button);
				}
			}	
			if (_buttons.length==0) {
				var _button={};
				_button.title=lang['close'];
				_button.type="close";
				_button.params={};
				_buttons.push(_button);
			}
			var _popup=new Popup({
				type:_popup_type,
				title:_popup_title,
				message:_popup_message,
				buttons:_buttons
			});
			e.preventDefault();
			return false;
		});
		$(document).on("mouseenter","*[tooltip-text]",function(){
			var _tooltip_text=$(this).attr("tooltip-text");
			if (typeof _tooltip_text=="undefined") _tooltip_text="";
			_tooltip_text=$.trim(_tooltip_text);
			if (_tooltip_text!="") {
				var _tooltip_id=$(this).attr("tooltip-id");
				if (typeof _tooltip_id=="undefined") {
					_tooltip_id="tooltip-"+(Math.floor(Math.random()*(10000))+1);
					$(this).attr("tooltip-id",_tooltip_id);
				}
				var _tooltip_box=$("#"+_tooltip_id);
				if (_tooltip_box.length==0) {
					_tooltip_html='\
						<div class="tooltip-wrapper" id="'+_tooltip_id+'"'+($(this).outerWidth()>280?'':' style="min-width:'+($(this).outerWidth()+20)+'px;"')+'>\
							<div class="tooltip-text">'+_tooltip_text+'</div>\
							<div class="tooltip-arrow"></div>\
						</div>\
					';
					$("body").append(_tooltip_html);
					var _direction=$("body").css("direction");
					if (typeof _direction=="undefined") _direction="ltr";
					if (_direction!="ltr" && _direction!="rtl") _direction="ltr";
					_tooltip_box=$("#"+_tooltip_id);
					var _left_offset=$(this).offset().left;
					var _top_offset=$(this).offset().top;
					if (_direction=="ltr") {
						_left_offset=_left_offset-10;
					} else {
						_left_offset=_left_offset-_tooltip_box.outerWidth()+$(this).outerWidth()+10;
					}
					if (_left_offset+_tooltip_box.outerWidth()>$(window).width()) _left_offset=$(window).width()-_tooltip_box.outerWidth()-5;
					if (_left_offset<0) _left_offset=5;
					_top_offset=_top_offset-_tooltip_box.outerHeight()-5;
					if (_top_offset<0) {
						_tooltip_box.addClass("inverted");
						_top_offset=$(this).offset().top + $(this).outerHeight() + 5;
					} else {
						_tooltip_box.removeClass("inverted");
					}
					_tooltip_box.css("left",_left_offset);
					_tooltip_box.css("top",_top_offset);
					var _arrow_offset=$(this).offset().left-_left_offset+$(this).outerWidth()/2-7;
					_tooltip_box.find(".tooltip-arrow").css("left",_arrow_offset);
				}
				setTimeout(function(){
					_tooltip_box.addClass("tooltip-wrapper-displayed");
				},10);
			}
		});
		$(document).on("mouseleave","*[tooltip-text]",function(){
			var _tooltip_id=$(this).attr("tooltip-id");
			if (typeof _tooltip_id!="undefined") {
				var _tooltip_box=$("#"+_tooltip_id);
				if (_tooltip_box.length>0) {
					_tooltip_box.removeClass("tooltip-wrapper-displayed");
					setTimeout(function(){
						$(this).remove();
					}.bind(_tooltip_box),200);
				} 
			}
		});
		$(document).on("click",".plus-minus-toggler",function(e){
			$(this).toggleClass("opened");
			var _related_to=$(this).attr("related-to");
			if (typeof _related_to=="undefined") _related_to="";
			if (_related_to!="") {
				if ($("#"+_related_to).length>0) {
					if ($(this).hasClass("opened")) {
						$("#"+_related_to).slideDown(300);
					} else {
						$("#"+_related_to).slideUp(300);
					}
				}
			}
			e.preventDefault();
			return false;
		});
		$(document).on("click",".language-wrapper",function(){
			$(this).toggleClass("active");
		});
		$(document).on("click","*[select-language]",function(){
			var _requested_lang=$(this).attr("select-language");
			if (typeof _requested_lang=="undefined") _requested_lang="";
			if (_requested_lang!="") {
				var _language_temporary_form='<form method="post" id="language-temporary-form"><input type="hidden" name="task" value="change-language"/><input type="hidden" name="language" value="'+_requested_lang+'" /></form>';
				if ($("body").find("#language-temporary-form").length>0) $("body").find("#language-temporary-form").remove();
				$("body").append(_language_temporary_form);
				$("#language-temporary-form").submit();
			}
		});
		$(document).on("click","*",function(e){
			if ($(e.target).closest(".language-wrapper").length==0) $(".language-wrapper.active").removeClass("active");
		});
		$(document).on("change","*[select-all]",function(){
			setTimeout(function(){
				var _select_all_id=$(this).attr("select-all");
				if (typeof _select_all_id=="undefined") _select_all_id="";
				if (_select_all_id!="" && !_select_all_checkbox_prevent) {
					if (this.checked) {
						$("*[select-all-child='"+_select_all_id+"']").each(function(){
							if (!this.checked) $(this).trigger("click");
						});
					} else {
						$("*[select-all-child='"+_select_all_id+"']").each(function(){
							if (this.checked) $(this).trigger("click");
						});					
					}
				}
			}.bind(this),1);
		});
		$(document).on("change","*[select-all-child]",function(){
			setTimeout(function(){
				var _select_all_id=$(this).attr("select-all-child");
				if (typeof _select_all_id=="undefined") _select_all_id="";
				var _selector=$("*[select-all='"+_select_all_id+"']");
				if (_select_all_id!="" && _selector.length>0) {
					var _total_checkboxes=0;
					var _checked_checkboxes=0;
					$("*[select-all-child='"+_select_all_id+"']").each(function(){
						_total_checkboxes++;
						if (this.checked) _checked_checkboxes++;
					});
					_select_all_checkbox_prevent=true;
					if (_checked_checkboxes>=_total_checkboxes) {
						if (!_selector[0].checked) _selector.trigger("click");
					} else {
						if (_selector[0].checked) _selector.trigger("click");
					}
					setTimeout(function(){_select_all_checkbox_prevent=false;},2);
				}
			}.bind(this),1);
		});		
		$(document).on("change","*[batch-handler]",function(){
			setTimeout(function(){
				var _batch_handler=$(this).attr("batch-handler");
				var _batch_related=$(this).attr("batch-related");
				if (typeof _batch_handler=="undefined") _batch_handler="";
				if (typeof _batch_related=="undefined") _batch_related="";
				if (_batch_handler!="" && _batch_related!="") {
					var _checked=0;
					$("*[batch-handler='"+_batch_handler+"']").each(function(){
						if (this.checked) _checked++;
					});
					if (_checked>0) {
						$("#"+_batch_related).stop().show(300);
					} else {
						$("#"+_batch_related).stop().hide(300);
					}
				}
			}.bind(this),2);
		});
		$(document).on("click","*[sort-column]",function(e){
			var _sort_column=$(this).attr("sort-column");
			if (typeof _sort_column=="undefined") _sort_column="";
			var _sort_direction=$(this).attr("sort-direction");
			if (typeof _sort_direction=="undefined") _sort_direction="desc";			
			if (_sort_direction=="desc") _sort_direction="asc";
			else _sort_direction="desc";
			if (_sort_column!="") {
				var _sort_column_regexp=/(\?|&)sort-column=([^\?&]+)/gi;
				var _sort_direction_regexp=/(\?|&)sort-direction=([^\?&]+)/gi;
				var _href=location.href;
				if (_sort_column_regexp.exec(_href)) {
					_href=_href.replace(_sort_column_regexp,"\$1sort-column="+_sort_column);
				} else {
					_href=_href+(_href.indexOf("?")==-1?"?":"&")+"sort-column="+_sort_column;
				}
				if (_sort_direction_regexp.exec(_href)) {
					_href=_href.replace(_sort_direction_regexp,"\$1sort-direction="+_sort_direction);
				} else {
					_href=_href+(_href.indexOf("?")==-1?"?":"&")+"sort-direction="+_sort_direction;
				}			
				location.href=_href;
			}
			e.preventDefault();
			e.stopPropagation();
			return false;
		});
	}
	
	this.initDraggable=function(){
		$("*[sortable-wrapper='true']").disableSelection();
		$("*[sortable-wrapper='true']").find(".draggable-row").each(function(){
			$(this).find("td").each(function(){
				var _width=$(this).width();
				$(this).width(_width);
			});
		});	
		$("*[sortable-wrapper='true']").sortable({
			placeholder:"moving-row-holder",
			start: function(event,ui) {
				var _height=ui.helper.height()+10;
				var _index=0;
				ui.helper.find("td").each(function(){
					var _class_name=$(this).attr("class");
					if (typeof _class_name=="indefined") _class_name="";
					if (_class_name!="") {
						ui.placeholder.find("td:eq("+_index+")").attr("class",_class_name);
					}
					_index++;
				});
				ui.placeholder.height(_height);
				var _onStart=ui.helper.closest("*[sortable-wrapper='true']").attr("onStart");
				if (typeof _onStart=="undefined") _onStart="";
				if (_onStart!="") {
					eval(_onStart);
				}
			},
			stop: function(event,ui) {
				var _onStop=ui.item.closest("*[sortable-wrapper='true']").attr("onStop");
				if (typeof _onStop=="undefined") _onStop="";
				if (_onStop!="") {
					eval(_onStop);
				}
			}
		});		
	}	

	return this.init();
}
var _events=new Events();
var _select_all_checkbox_prevent=false;