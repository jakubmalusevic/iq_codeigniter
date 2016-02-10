$(document).ready(function(){
	$(".alert").each(function(){
		if ($(this).closest(".modal-content").length==0) {
			$(this).append('<a href="#" class="notification-close-button">x</a>');
		}
	});
	setTimeout(function(){
		$(".alert").fadeTo(300,0,function(){
			$(this).remove();
			if ($(".notifications-content-wrapper").find(".alert").length==0) $(".notifications-content-wrapper").remove();
		});
	},10000);
});
$(document).on("click",".notification-close-button",function(e){
	$(this).closest(".alert").fadeTo(300,0,function(){
		$(this).remove();
		if ($(".notifications-content-wrapper").find(".alert").length==0) $(".notifications-content-wrapper").remove();
	});
	e.preventDefault();
	e.stopPropagation();
	return false;
});