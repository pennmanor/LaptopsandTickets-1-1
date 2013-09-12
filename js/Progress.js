function Progress(eleBar, eleProgress, eleContent, steps){
	this.eleBar = eleBar;
	this.eleProgress = eleProgress;
	this.eleContent = eleContent;
	this.steps = steps;
	
	this.progress = 0;
	this.current = 0;
	
	this.init = function(){
		$(this.eleContent).hide();
		this.progress = 100/this.steps;
		this.current = 0;
	}
	
	this.reset = function(){
		$(this.eleProgress).fadeIn();
		$(this.eleContent).hide();
		this.progress = 100/this.steps;
		this.step("reset");
	}
	
	this.check = function(){
		if(this.current >= 100){
			$(this.eleProgress).slideUp();
			$(this.eleContent).slideDown();
			setTimeout($.proxy(this.barPercent, this, 0), 600);
		}
	}
	this.barPercent = function(percent){
		$(this.eleBar).css("width",percent+"%");
	}
	this.step = function(dir){
		switch(dir){
		case -1:
		case "reset":
			this.current = 0;
			break;
		case 0:
		case "down":
			this.current -= this.progress;
			break;
		case 1:
		case "up":
			this.current += this.progress;
			break;
		case 2:
		case "complete":
			this.current = 100;
			break;
		default:
			this.current += this.progress;
			break;
		}
		
		this.barPercent(this.current);
		if(this.current >= 100){
			setTimeout($.proxy(this.check, this), 600);
		}
	}
}