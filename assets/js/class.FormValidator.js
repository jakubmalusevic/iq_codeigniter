FormValidator=function(){
	
	this.init=function(){
		return this;
	}
	
	this.validate=function(form){
		errors=0;
		_form=$(form);
		if(_form.length>0) {
			_form.find("*[required-field='true']").each(function(){
				var _validation_rules=formValidator.getValidationRules(this);
				if (!formValidator.validateField(this,_validation_rules)) {
					if ($(this).closest(".checkbox-wrapper").length==0 && $(this).closest(".radio-wrapper").length==0 && $(this).closest(".file-wrapper").length==0) $(this).addClass("error-field");
					else $(this).parent().addClass("error-field");
					errors++;
				} else {
					if ($(this).closest(".checkbox-wrapper").length==0 && $(this).closest(".radio-wrapper").length==0 && $(this).closest(".file-wrapper").length==0) $(this).removeClass("error-field");
					else $(this).parent().removeClass("error-field");
				}
			});
		}
		var _error_handler=_form.find("*[error-handler='true']");
		if (errors>0) {
			var _error_text=_form.attr("validation-error");
			if (typeof _error_text=="undefined") _error_text="";
			if (_error_handler.length>0 && _error_text!="") {
				if (_error_handler.css("display")!="none") {
					_error_handler.slideUp(0);		
				}
				_error_handler.html(_error_text);
				_error_handler.slideDown(300);				
			}
			return false;
		} else {
			if (_error_handler.length>0) {
				if (_error_handler.css("display")!="none") {
					_error_handler.slideUp(0);		
				}	
				_error_handler.html("");			
			}
			return true;
		}
	}
	
	this.validateField=function(obj,rules){
		var _form=$(obj).closest("form");
		var _validate=true;
		var result=true;
		var _value=$(obj).val();
		var _validate_on_checked=$(obj).attr("validate-on-checked");
		var _validate_on_selected=$(obj).attr("validate-on-selected");
		if (typeof _validate_on_checked=="undefined") _validate_on_checked="";
		if (typeof _validate_on_selected=="undefined") _validate_on_selected="";
		if (_validate_on_checked!="") {
			if ($("#"+_validate_on_checked).length>0) {
				if (!$("#"+_validate_on_checked)[0].checked) _validate=false;
			}
		}
		if (_validate_on_selected!="") {
			_validate_on_selected=_validate_on_selected.split(":");
			if (_validate_on_selected.length==2) {
				if ($("#"+_validate_on_selected[0]).length>0) {
					if ($("#"+_validate_on_selected[0]).val().toLowerCase()!=_validate_on_selected[1].toLowerCase()) _validate=false;
				}
			}
		}
		if (_validate) {
			for(var r=0;r<rules.length;r++){
				var rule=rules[r];
				
				if (typeof this.rules[rule.type]!="undefined") {
					if (!this.rules[rule.type](obj,_value,rule.params)) result=false;
				}
			 
			}
		}
		return result;
	}
	
	this.getValidationRules=function(obj){
		result=new Array();
		var _validation=$(obj).attr("validation");
		if (typeof _validation=="undefined") _validation="";
		if (_validation!="") {
			var _temp=_validation.split("]");
			for(var t=0;t<_temp.length;t++){
				if ($.trim(_temp[t].substr(1))!="") {
					var _rule={};
					var _rule_parts=_temp[t].substr(1).split(":");
					_rule.type=_rule_parts[0];
					_rule.params=new Array();
					if (_rule_parts.length>1) {
						for(var r=1;r<_rule_parts.length;r++) _rule.params.push(_rule_parts[r]);
					}
					result.push(_rule);
				}
			}
		}
		return result;
	}
	
	this.rules=new Array();
	
	this.rules["not-empty"]=function(obj,value,params){
		if ($.trim(value)=="") return false;
		return true;
	}
	this.rules["not-null"]=function(value,params){
		var _temp_value=value.replace(/[^\.0-9]/gi,"");
		if (_temp_value=="") _temp_value="0";
		_temp_value=parseFloat(_temp_value);
		if (_temp_value==0) return false;
		return true;
	}
	this.rules["positive-number"]=function(obj,value,params){
		var _temp_value=value.replace(/[^\.\-0-9]/gi,"");
		if (_temp_value=="") result=false;
		else {
			_temp_value=parseFloat(_temp_value);
			if (_temp_value<0) return false;
		}
		return true;
	}
	this.rules["negative-number"]=function(obj,value,params){
		var _temp_value=value.replace(/[^\.\-0-9]/gi,"");
		if (_temp_value=="") _temp_value="0";
		_temp_value=parseFloat(_temp_value);
		if (_temp_value>=0) result=false;
		return true;
	}
	this.rules["limited-number"]=function(obj,value,params){
		if (params.length>0) {
			var _from_value=false;
			var _to_value=false;
			if (params.length==1) {
				var _temp_param=params[0].replace(/[^\.\-0-9]/gi,"");
				if (_temp_param!="") {
					_from_value=parseFloat(_temp_param);
				}
		
			}
			if (params.length>1) {
				var _temp_param=params[1].replace(/[^\.\-0-9]/gi,"");
				if (_temp_param!="") {
					_to_value=parseFloat(_temp_param);
				}
		
			}		
			var _temp_value=value.replace(/[^\.\-0-9]/gi,"");
			if (_temp_value=="") _temp_value="0";
			_temp_value=parseFloat(_temp_value);								
			if ((_temp_value<_from_value && _from_value!==false) || (_temp_value>_to_value && _to_value!==false)) return false;
		}
		return true;
	}
	this.rules["email"]=function(obj,value,params){
		var _email_regex=/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;			
		if (!_email_regex.test($.trim(value))) return false;
		return true;
	}
	this.rules["match-field"]=function(obj,value,params){
		if (params.length>0) {
			var _compare_field_id=$.trim(params[0]);
			if (_compare_field_id!="") {
				if ($("#"+_compare_field_id).length>0) {
					var _compare_value=$("#"+_compare_field_id).val();
					if (_compare_value!=value) return false;
				}
			}
		}
		return true;
	}
	this.rules["checked"]=function(obj,value,params){
		if ($(obj).closest(".checkbox-wrapper").length>0) {
			if (!obj.checked) return false;
		}
		if ($(obj).closest(".radio-wrapper").length>0) {
			var _name=$(obj).attr("name");
			if (typeof _name=="undefined") _name="";
			if (_name!="") {
				var _checked=false;
				$("input[type='radio'][name='"+_name+"']").each(function(){
					if (this.checked) _checked=true;
				});
				if (!_checked) return false;
			}
		}	
		return true;
	}
	this.rules["extension"]=function(obj,value,params){
		if (params.length>0) {
			var _allowed_extensions=$.trim(params[0]).split(",");
			var _object_ext=$.trim(value).split(".");
			_object_ext=_object_ext[_object_ext.length-1].toLowerCase();
			if (_allowed_extensions.indexOf(_object_ext)==-1) return false;
		}
		return true;
	}
	this.rules["at-least-one-checked"]=function(obj,value,params){
		if (params.length>0) {
			var _param=$.trim(params[0]);
			if (_param!="") {
				var _found=false;
				var _find_validation="[at-least-one-checked:"+_param+"]";
				_form.find("*[validation][type='checkbox']").each(function(){
					var _field_validation=$(this).attr("validation");
					if (_field_validation.indexOf(_find_validation)!=-1 && this.checked) _found=true;
				});
				if (!_found) return false;
			}
		}
		return true;
	}
	
	return this.init();
}
var formValidator=new FormValidator();