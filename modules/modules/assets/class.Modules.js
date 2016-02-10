Modules=function(){

	this.rearrange=function(wrapper){
		wrapper=$(wrapper);
		if (wrapper.length>0) {
			var _store=new Array();
			var _start_order=wrapper.attr("start-order");
			if (typeof _start_order=="undefined") _start_order="10";
			_start_order=parseFloat(_start_order);
			var _i=_start_order;
			wrapper.find(".draggable-row").each(function(){
				if ($(this).find("*[order-cell='true']").length>0) {
					$(this).find("*[order-cell='true']").html(_i);
					var _order_id=$(this).find("*[order-cell='true']").attr("order-id");
					if (typeof _order_id=="undefined") _order_id="0";
				} else {
					var _order_id="0";
				}
				var _store_row={};
				_store_row.id=_order_id;				
				_store_row.order=_i;
				_store.push(_store_row);
				_i+=10;
			});
			$.post(base_url+"modules/listing/saveorder",{data:_store},function(){
				_navigation.reload();
			});
		}
	}
	
	$(document).on("keyup","*[tags-input]",function(e){
		var _tags_container=$(this).attr("tags-container");
		if (typeof _tags_container=="undefined") _tags_container="";
		var _tags_field_name=$(this).attr("tags-field-name");
		if (typeof _tags_field_name=="undefined") _tags_field_name="";		
		if (_tags_container!="" && _tags_field_name!="") {
			if (e.keyCode==13) {
				e.preventDefault();
				modules.addTag($(this).val(),_tags_container,_tags_field_name);
				$(this).val("");
			} else {
				var _tags=modules.parseTags($(this).val());
				if (_tags.length>0){
					$(this).val("");
					for(var _i in _tags) modules.addTag(_tags[_i],_tags_container,_tags_field_name);
				}
			}
		}
	});
	
	$(document).on("keydown","*[tags-input]",function(e){
		var _tags_container=$(this).attr("tags-container");
		if (typeof _tags_container=="undefined") _tags_container="";
		var _tags_field_name=$(this).attr("tags-field-name");
		if (typeof _tags_field_name=="undefined") _tags_field_name="";		
		if (_tags_container!="" && _tags_field_name!="") {
			if (e.keyCode==13) {
				e.preventDefault();
				modules.addTag($(this).val(),_tags_container,_tags_field_name);
				$(this).val("");
			} else {
				var _tags=modules.parseTags($(this).val());
				if (_tags.length>0){
					$(this).val("");
					for(var _i in _tags) modules.addTag(_tags[_i],_tags_container,_tags_field_name);
				}
			}
		}
	});	
	
	$(document).on("click","*[remove-tag]",function(){
		$(this).closest(".single-tag").remove();
	});
	
	this.addTag=function(value,tags_container,tags_field_name){
		var _html='';
		if ($.trim(value)!="") {
			_html='<span class="single-tag">'+$.trim(value)+'<i class="remove-tag-icon" remove-tag="true">&nbsp;</i><input type="hidden" name="'+tags_field_name+'" value="'+$.trim(value)+'" /></span>';
			$("#"+tags_container).append(_html);
		}
	}
	
	this.parseTags=function(value){
		var _return=new Array();
		if (value.indexOf(",")!=-1) {
			var _values=value.split(",");
			for(var _i=0;_i<_values.length;_i++){
				if ($.trim(_values[_i])!="") _return.push($.trim(_values[_i]));
			}
		}
		return _return;
	}	
	
	$(document).on("mouseenter","a.shop-module-box",function(e){
		$(this).find(".shop-module-box-cover").stop().fadeTo(300,1);
	});
	
	$(document).on("mouseleave","a.shop-module-box",function(e){
		$(this).find(".shop-module-box-cover").stop().fadeTo(300,0,function(){
			$(this).hide();
		});
	});	
	
	$(document).on("mousemove","*[rating-handler='true']",function(e){
		var _pos=e.pageX-$(this).offset().left;
		var _full_width=$(this).width();
		var _rating=parseFloat((5*_pos/_full_width).toFixed(0));
		var _c=1;
		$(this).find(".small-star-box").each(function(){
			if (_c<=_rating) $(this).find(".small-star-bg").css("width","100%");
			else $(this).find(".small-star-bg").css("width","0%");
			_c++;
		});
	});
	
	$(document).on("mouseout","*[rating-handler='true']",function(e){
		var _cur_rating=parseFloat($(this).find("input[type='hidden']:first").val());
		var _c=1;
		$(this).find(".small-star-box").each(function(){
			if (_c<=_cur_rating) $(this).find(".small-star-bg").css("width","100%");
			else $(this).find(".small-star-bg").css("width","0%");
			_c++;
		});		
	});
	
	$(document).on("mousedown","*[rating-handler='true']",function(e){
		var _pos=e.pageX-$(this).offset().left;
		var _full_width=$(this).width();
		var _rating=parseFloat((5*_pos/_full_width).toFixed(0));
		$(this).find("input[type='hidden']:first").val(_rating);
	});	

	return this;

}
var modules=new Modules();
