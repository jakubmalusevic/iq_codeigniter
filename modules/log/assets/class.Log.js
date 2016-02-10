Log=function(){

	this.init=function(){
		this.bindEvents();
		return this;
	}

	this.deleteSelected=function(){
		var _to_delete=new Array();
		$("*[batch-handler='log-row']").each(function(){
			if (this.checked) _to_delete.push($(this).val());
		});
		if (_to_delete.length>0){
			$("body").prepend('<form method="post" action="'+base_url+'log/listing/batchdelete" id="delete-selected-form"></form>');
			for(var i=0;i<_to_delete.length;i++){
				$("#delete-selected-form").append('<input type="hidden" name="ids[]" value="'+_to_delete[i]+'" />');
			}
			$("#delete-selected-form").submit();
		}
	}
	
	this.bindEvents=function(){
		$(document).on("click","[log-show-more='true']",function(e){
			var _wrp=$(this).closest(".log-record-description");
			var _preview=_wrp.find(".log-description-preview");
			var _full=_wrp.find(".log-description-full");
			if (_wrp.length>0 && _preview.length>0 && _full.length>0) {
				_preview.slideUp(300);
				_full.slideDown(300);
				$(this).hide(300);
			}
			e.preventDefault();
			e.stopPropagation();
			return false;
		});
	}

	return this.init();

}
var log=new Log();