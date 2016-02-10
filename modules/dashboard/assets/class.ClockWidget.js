if (typeof ClockWidget=="undefined") {
	ClockWidget=function(selector){
		
		this.ti=false;
		this.selector="";
		
		this.init=function(selector){
			this.selector=selector;
			if (this.ti!==false) clearInterval(this.ti);
			setInterval(function(){
				this.increaseTime();
			}.bind(this),1000);
			return this;
		}
		
		this.increaseTime=function(){
			var _cur_second=parseFloat($(this.selector).find("*[second-container]").html());
			var _cur_minute=parseFloat($(this.selector).find("*[minute-container]").html());
			var _cur_hour=parseFloat($(this.selector).find("*[hour-container]").html());
			var _cur_day=parseFloat($(this.selector).find("*[day-container]").html());
			var _cur_month=parseFloat($(this.selector).find("*[month-container]").html())-1;
			var _cur_year=parseFloat($(this.selector).find("*[year-container]").html());
			var _d=new Date(_cur_year,_cur_month,_cur_day,_cur_hour,_cur_minute,_cur_second,0);
			var _cur_time=_d.getTime()+1000;
			_d=new Date(_cur_time);
			$(this.selector).find("*[second-container]").html(_d.getSeconds().toString().length<2?"0"+_d.getSeconds().toString():_d.getSeconds().toString());
			$(this.selector).find("*[minute-container]").html(_d.getMinutes().toString().length<2?"0"+_d.getMinutes().toString():_d.getMinutes().toString());
			$(this.selector).find("*[hour-container]").html(_d.getHours().toString().length<2?"0"+_d.getHours().toString():_d.getHours().toString());
			$(this.selector).find("*[day-container]").html(_d.getDate().toString().length<2?"0"+_d.getDate().toString():_d.getDate().toString());
			$(this.selector).find("*[month-container]").html((parseFloat(_d.getMonth())+1).toString().length<2?"0"+(parseFloat(_d.getMonth())+1).toString():(parseFloat(_d.getMonth())+1).toString());
			$(this.selector).find("*[year-container]").html(_d.getFullYear());			
		}
		
		return this.init(selector);

	}
}