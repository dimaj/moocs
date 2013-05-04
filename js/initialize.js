$(function() {
	$('#input_search').typeahead({
		source: function (query, process) {
            get_course_search_data(process);
        }
	});

	var course_list = [];
	var target = $('#table');

    $.ajax({
        type: 'POST',
        url: 'GetData.php',
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


    var get_course_search_data = function (process) {
        $.ajax({
            type: 'POST',
            url: 'get-type-ahead-data.php',
            async: false,
            dataType: 'json',
            success: function(response) {
                console.log(response);
                course_list = response.data;
                process(course_list)
            }
        });
        
        return;    
    };

    var search_for_courses = function (param) {

    };

    var populate_new_course_ticker = function (param) {

    };

    var get_new_course_data = function () {

    };

    var populate_feature_course_ticker = function (param) {

    };

    var get_featured_course_data = function () {
        
    };

});