Roles=function(){

	this._prevent_parent_role_actions_click=false;

	this.copyRole=function(obj){
		var _value=$(obj).val();
		if (typeof _value=="undefined") _value="";
		$.magnificPopup.close();
		setTimeout(function(){
			$.magnificPopup.open({
				items:{
					src:base_url+"users/roles/create/"+_value
				},
				type:'ajax',
				mainClass:'anim-mfp-slide-bottom',
				removalDelay:300,
				closeOnBgClick:false,
				tLoading: '<span class="grey-middle-loader">Loading</span>',
				callbacks:{
					ajaxContentAdded:function(){
						visualHelpers.init();
						_events.initDraggable();
						_hideable.init().bindEvents();
					}
				}
			},0);			
		},300);
	}
	
	this.checkAllActions=function(){
		setTimeout(function(){
			$(".permission-actions-block").each(function(){
				var _total_count=0;
				var _checked_count=0;
				$(this).find("input[type='checkbox']").each(function(){
					_total_count++;
					if (this.checked) _checked_count++;
				});
				var _target_elem=$(this).prev().find("input[type='checkbox']");
				if (_target_elem.length>0) {
					if (_checked_count>=_total_count){
						if (!_target_elem[0].checked) {
							roles._prevent_parent_role_actions_click=true;
							$(this).prev().find("input[type='checkbox']").click();
							roles._prevent_parent_role_actions_click=false;
						}
					} else {
						if (_target_elem[0].checked) {
							roles._prevent_parent_role_actions_click=true;
							$(this).prev().find("input[type='checkbox']").click();
							roles._prevent_parent_role_actions_click=false;
						}
					}
				}
			});
		},10);
	}
	
	this.checkRelatedActions=function(obj){
		if (!this._prevent_parent_role_actions_click) {
			obj=$(obj);
			if (obj.length>0) {
				var _target_wrapper=$(obj).closest(".permission-box").next();
				if (_target_wrapper.length>0) {
					_target_wrapper.find("input[type='checkbox']").each(function(){
						if (!this.checked && $(obj)[0].checked) $(this).click();
						else if (this.checked && !$(obj)[0].checked) $(this).click();
					});
				}
			}
		}
	}
	
	this.processExpandingTree=function(obj){
		if ($(obj).closest(".permission-box").not(obj).length>0) {
			console.log($(obj).closest(".permission-box"));
			this.processExpandingTree($(obj).closest(".permission-box"));
		}
	}

	return this;

}
var roles=new Roles();

var _process_expanding_tree=false;

formValidator.rules["roles-check-primary-action"]=function(obj,value,params){
	if ($("input#full_access[type='checkbox']").length>0) {
		if ($("input#full_access[type='checkbox']")[0].checked) return true;
	}
	if (params.length>0) {
		var _param=$.trim(params[0]);
		if (_param!="") {
			var _found=false;
			var _find_validation="[roles-check-primary-action:"+_param+"]";
			_form.find("*[validation][type='checkbox']").each(function(){
				var _field_validation=$(this).attr("validation");
				if (_field_validation.indexOf(_find_validation)!=-1 && this.checked) _found=true;
			});
			if (!_found) {
				if (_form.find("*[validation='"+_find_validation+"'][type='checkbox']:first").length>0 && !_process_expanding_tree) {
					_process_expanding_tree=true;
					var _sec_toggler=_form.find("*[validation='"+_find_validation+"'][type='checkbox']:first").closest(".permission-sections-block").find(".plus-minus-toggler:first");
					var _mod_toggler=_form.find("*[validation='"+_find_validation+"'][type='checkbox']:first").closest(".permission-module-block").find(".plus-minus-toggler:first");
					var _timeout=0;
					if (_mod_toggler.length>0) {
						if (!_mod_toggler.hasClass("opened")) {
							_mod_toggler.trigger("click");
							_timeout+=300;
						}
					}
					if (_sec_toggler.length>0) {
						if (!_sec_toggler.hasClass("opened")) {
							setTimeout(function(){
								_sec_toggler.trigger("click");
							},_timeout);
							_timeout+=300;
						}
					}
					setTimeout(function(){
						_process_expanding_tree=false;
					},_timeout);
				}
				return false;
			}
		}
	}
	return true;
}