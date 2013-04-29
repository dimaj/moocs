$(function() {
	var API = 'GetData.php';	
	var course_list = [];
	var target = $('#table');

    $.ajax({
        type: 'POST',
        url: API,
        async: false,
        dataType: 'json',
        success: function(response) {
        	course_list = response.data;
        }
    });

    var course_trend = [];
    var new_course = [];
    var course;

    for (var i in course_list) {
    	course = course_list[i];
    	if (course.site === 'Canvas') {
    		course_trend.push(course);
    	}
    	if (course.site === 'Udacity') {
    		new_course.push(course);
    	}
    }

    var course_trend_scroll = $('<ul />');
    var new_course_scroll = $('<ul />');

    var record;

    for (var i in course_trend) {
    	course = course_trend[i];
    	record = $('<li />');
    	record.html(course.title);
    	course_trend_scroll.append(record);
    }

    for (var i in new_course) {
    	course = new_course[i];
    	record = $('<li />');
    	record.html(course.title);
    	new_course_scroll.append(record);
    }

	$("div#course_trend_ticker").append(course_trend_scroll);
	$("div#course_trend_ticker ul").liScroll();
	$("div#new_course_ticker").append(new_course_scroll);
	$("div#new_course_ticker ul").liScroll();
});