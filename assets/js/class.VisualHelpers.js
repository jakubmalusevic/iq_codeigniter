VisualHelpers=function(){	

	this.init=function(){
		var _checkboxes=$("input[type='checkbox']");
		var _radios=$("input[type='radio']");
		var _files=$("input[type='file']");
		if (_checkboxes.length>0) {
			_checkboxes.each(function(){
				visualHelpers.replaceCheckbox(this);
			});
		}
		if (_radios.length>0) {
			_radios.each(function(){
				visualHelpers.replaceRadio(this);
			});
		}
		if (_files.length>0) {
			_files.each(function(){
				visualHelpers.replaceFile(this);
			});
		}
		this.initSortColumns();
		this.initDatepickers();
	}

	this.initDatepickers=function(){
		$(".datepicker-field").datepicker({dateFormat:"dd/mm/yy"});
	}	
	
	this.initSortColumns=function(){
		var _sort_column_regexp=/(\?|&)sort-column=([^\?&]+)/gi;
		var _sort_direction_regexp=/(\?|&)sort-direction=([^\?&]+)/gi;
		var _sort_column=(_sort_column_regexp.exec(location.href)||new Array("","",""))[2];
		var _sort_direction=(_sort_direction_regexp.exec(location.href)||new Array("","","asc"))[2];
		if (_sort_column!="" && _sort_direction!="") {
			$("*[sort-column='"+_sort_column+"']").attr("sort-direction",_sort_direction);
		}
	}
	
	this.replaceCheckbox=function(obj){
		var _obj=$(obj);
		if (_obj.length>0) {
			if (!_obj.parent().hasClass("checkbox-wrapper")) {
				var _disabled=_obj.attr("disabled");
				if (typeof _disabled=="undefined") _disabled="false";
				if (_disabled=="false") _disabled=false;
				else _disabled=true;
				var _tooltip_text=_obj.attr("tooltip-text");
				if (typeof _tooltip_text=="undefined") _tooltip_text="";
				_tooltip_text=$.trim(_tooltip_text);
				_obj.wrap('<div class="checkbox-wrapper'+(_disabled?' disabled':'')+'"'+(_tooltip_text!=""?' tooltip-text="'+_tooltip_text+'"':'')+'></div>');
				if (obj.checked) $(_obj).parent().addClass("checkbox-wrapper-checked");
			}
		}
	}
	
	this.replaceRadio=function(obj){
		var _obj=$(obj);
		if (_obj.length>0) {
			if (!_obj.parent().hasClass("radio-wrapper")) {
				var _disabled=_obj.attr("disabled");
				if (typeof _disabled=="undefined") _disabled="false";
				if (_disabled=="false") _disabled=false;
				else _disabled=true;
				var _tooltip_text=_obj.attr("tooltip-text");
				if (typeof _tooltip_text=="undefined") _tooltip_text="";
				_tooltip_text=$.trim(_tooltip_text);							
				_obj.wrap('<div class="radio-wrapper'+(_disabled?' disabled':'')+'"'+(_tooltip_text!=""?' tooltip-text="'+_tooltip_text+'"':'')+'></div>');
				if (obj.checked) $(_obj).parent().addClass("radio-wrapper-checked");
			}
		}
	}	
	
	this.replaceFile=function(obj){
		var _obj=$(obj);
		if (_obj.length>0) {
			if (!_obj.parent().hasClass("file-wrapper")) {
				var _obj_height=$(obj).css("height");
				_obj.wrap('<div class="file-wrapper full-width" style="height:'+_obj_height+';"></div>');
				_obj.after('<div class="file-wrapper-file-name-box"><div class="file-wrapper-file-name">- '+lang['select_file']+' -</div></div>');
			}
		}		
	}

	return this;
}
visualHelpers=new VisualHelpers();