var studentEvents = [];
(function($) {

    "use strict";

	var options = {
		events_url: '../../getStudentLogs.php',
		view: 'month',
		tmpl_path: 'tmpls/',
		holidays: {
			'08-03': 'International Women\'s Day',
			'25-12': 'Christmas\'s',
			'01-05': "International labor day"
		},
		first_day: 2,
		onAfterEventsLoad: function(events) {
			if(!events) {
				return;
			}
			$.each(events, function(key, val) {
        if($.inArray(this.id, studentEvents) == -1)
        $("#eventlist").append("<li><span class=\"pull-left event " + this.class + "\"></span><a data-event-id=\"" + this.id + "\" data-event-class=\"" + this.class + "\" class=\"event-item\">" + this.id + "</a></li>");
          studentEvents.push(this.id);
			});
		},
		onAfterViewLoad: function(view) {
			$('.page-header .date').html(this.getTitle());
			$('.btn-group button').removeClass('active');
			$('button[data-calendar-view="' + view + '"]').addClass('active');
		},
		classes: {
			months: {
				general: 'label'
			}
		}
	};

	var	 calendar = $('#calendar').calendar(options);
	$('.btn-group button[data-calendar-nav]').each(function() {
		var $this = $(this);
		$this.click(function() {
			calendar.navigate($this.data('calendar-nav'));
		});
	});

	$('.btn-group button[data-calendar-view]').each(function() {
		var $this = $(this);
		$this.click(function() {
			calendar.view($this.data('calendar-view'));
		});
	});

    $('#first_day').change(function(){
        calendar.setOptions({first_day: $(this).val()});
        calendar.view();
    });

    $('#language').change(function(){
        calendar.setLanguage($(this).val());
        calendar.view();
    });
}(jQuery));
