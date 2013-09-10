(function ($){
	var manager = {
		init : function(options){
			return this.each(function(){
				var $this = $(this);
				var opts = $this.data("manager.settings");
				
				if(typeof(opts) == "undefined"){
					opts = $.extend( {}, $.fn.manager.defaults, options );
					$this.data("manager.settings", opts);
				}
				else{
					opts = $.extend( {}, opts, options );
					$this.data("manager.settings", opts);
				}
				$this.find("th input[type=\"checkbox\"]")//find checkbox in th
					.data("manager.target", $this)
					.on("change", function(){
						var $this = $(this);
						var master = $this.data("manager.target");
						var rows = master.find("td input[type='checkbox']");
						var attrs = [];
						rows.each(function(i, e){
							var $e = $(e);
							var row = $e.closest("tr");
							var attr = row.attr("data-manager-row");
							attrs.push(attr ? attr : row.index());
						});
						$this.data("manager.select", $this.prop('checked') == true ? true : false);
						rows
							.prop("checked", $(this).data("manager.select"))
							.each(function(){
								var $this = $(this);
								var master = $this.data("manager.target");
								var row = $this.closest("tr");
								var attr = row.attr("data-manager-row");
								$this.data("manager.select", $this.prop("checked") == true ? true : false);
								if($this.data("manager.select")){
									row.addClass(opts.selectedClass);
								}
								else{
									master.find("th [type='checkbox']").prop("checked", false); 
									row.removeClass(opts.selectedClass);
								}
							});
						if($this.data("manager.select"))
							opts.onMulitipleSelect.call(this, attrs);
					})
					.filter(":checked")
					.trigger("change")
					.closest("th")
					.addClass(opts.selectedClass)
				$this.find("td input[type=\"checkbox\"]")//find checkbox in rd
					.data("manager.target", $this)
					.on("change", function(){
						var $this = $(this);
						var master = $this.data("manager.target");
						var row = $this.closest("tr");
						var attr = row.attr("data-manager-row");
						$this.data("manager.select", $this.prop("checked") == true ? true : false);
						if($this.data("manager.select")){
							row.addClass(opts.selectedClass);
							opts.onSelect.call(this, attr ? attr : row.index());
						}
						else{
							master.find("th [type='checkbox']").prop("checked", false); 
							row.removeClass(opts.selectedClass);
						}
						update(opts, $this.data("manager.target"));
					})
					.filter(":checked")
					.closest("tr")
					.addClass(opts.selectedClass)
				update(opts, $this);
			});
		},
	}

	$.fn.manager = function(){
		var method = arguments[0];
		if(manager[method]){
			method = manager[method];
			arguments = Array.prototype.slice.call(arguments, 1);
		}
		else if(typeof(method) == "object" || !method){
			method = manager.init;
		}
		else{
			$.error("Method " + method + " doesn't exist for jQuery.manager");
			return this;
		}
		
		return method.apply(this, arguments);
	}

	$.fn.manager.defaults = {
	    selectedClass: "selected",
		onControlAction : function(name){},
		onSelect : function(s){},
		onNoneSelect : function(){},
		onSingleSelect : function(s) {},
		onMulitipleSelect : function(s) {}
	};

	function update(opts, target){
		var selected = target.find("td input[type='checkbox']:checked");
		var attrs = [];
		selected.each(function(i, e){
			var $e = $(e);
			var row = $e.closest("tr");
			var attr = row.attr("data-manager-row");
			attrs.push(attr ? attr : row.index());
		});
		if(selected.length == 0){
			opts.onNoneSelect.call(this);
		}
		else if(selected.length == 1){
			opts.onSingleSelect.call(this, attrs);
		}
		else if(selected.length >= 2){
			opts.onMulitipleSelect.call(this, attrs);
		}
	}
	
}(jQuery));
