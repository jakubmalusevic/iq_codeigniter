Users=function(){

	this.switchView=function(view){
		if (view=="reset") {
			$("#login-form").slideUp(300);
			$("#reset-form").slideDown(300);
		}
		if (view=="login") {
			$("#login-form").slideDown(300);
			$("#reset-form").slideUp(300);
		}		
	}
	
	this.resetFormSent=function(data){
		data=$.parseJSON(data);
		if (data.result=="error") {
			$("#reset-form").find("[error-handler='true']").hide();
			$("#reset-form").find("[error-handler='true']").html(data.message);
			$("#reset-form").find("[error-handler='true']").slideDown(300);			
		} else {
			$("#reset-form").find("[error-handler='true']").hide();
			$("#reset-form-control").slideUp(300);
			$("#reset-from-state").html(data.message);
			$("#reset-form").find("[type='submit']").hide();
		}
	}
	
	this.newpasswordFormSent=function(data){
		data=$.parseJSON(data);
		if (data.result=="error") {
			$("#reset-form").find("[error-handler='true']").hide();
			$("#reset-form").find("[error-handler='true']").html(data.message);
			$("#reset-form").find("[error-handler='true']").slideDown(300);			
		} else {
			$("#reset-form").find("[error-handler='true']").hide();
			$("#reset-form-control").slideUp(300);
			$("#reset-from-state").html(data.message);
			$("#reset-form").find("[type='submit']").hide();
		}
	}
	
	return this;

}
var users=new Users();