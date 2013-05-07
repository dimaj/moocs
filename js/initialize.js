$(function() {
    var show_search_results_view = function () {        
        $('#search_results_view')
            .siblings()
                .hide()
                .end()
            .show();

    };

    var show_home_view = function () {        
        $('#home_view')
            .siblings()
                .hide()
                .end()
            .show();

    };

    var show_course_results = function (param) {

        var course_list = param.course_list;
        var target = $('#search_results_table');

        target.dataTable({
            bDestroy: true
            , bJQueryUI: false
            , bPaginate: false
            , aaData: course_list
            , aoColumns: [
                {sTitle: 'Image', mData: function (source) {
                    var image;
                    image = '<img src="' + source.course_image + '" class="img-rounded" height="100" width="100">';
                    return image;
                }}
                , {sTitle: 'Title', mData: function (source) {
                    var title = source.title;
                    var link = source.course_link;
                    return '<a href=' + link + '>' + title + '</a>'
                }}
                , {sTitle: 'Category', mData: 'category'}
                , {sTitle: 'Start Date', mData: 'start_date'}
                , {sTitle: 'Course Length', mData: 'course_length'}
                , {sTitle: 'Instructor', mData: 'profname'}
                , {sTitle: 'Instructor Image', mData: function (source) {
                    return '<img src="' + source.profimage + '" class="img-rounded" height="100" width="100">';
                }}
                , {sTitle: 'Site', mData: 'site'}
            ]
            , 'oLanguage': {
                 "sSearch": "Filter records:"
            }
        });

        show_search_results_view();
    };

    var show_all_courses = function () {
        $.ajax({
            type: 'GET',
            url: 'GetData.php',
            dataType: 'json',
            success: function(response) {
                show_course_results({
                    'course_list': response.data
                });
            }
        });
    };

    var get_course_search_data = function (process) {
        $.ajax({
            type: 'GET'
            , url: 'get-type-ahead-data.php'
            , dataType: 'json'
            , success: function(response) {
                console.log(response);
                course_list = response.data;
                process(course_list)
            }
        });
        
        return;    
    };

    var search_for_courses = function () {
        $.ajax({
            type: 'GET',
            url: 'search-for-courses.php',
            dataType: 'json',
            success: function(response) {
                show_course_results({
                    'course_list': response.data
                });
            }
        });
    };

    var populate_new_courses_ticker = function (param) {
        var target_node = param.node;

        $.ajax({
            type: 'GET'
            , url: 'get-new-course-data.php'
            , dataType: 'json'
            , success: function(response) {
                var course_list = response.data;
                var new_course_scroll = $('<ul />');

                for (var i in course_list) {
                    course = course_list[i];
                    record = $('<li />');
                    record.html(course.title);
                    new_course_scroll.append(record);
                }

                target_node.append(new_course_scroll);
                new_course_scroll.liScroll();
            }
        });
    };

    var populate_trending_courses_ticker = function (param) {
        var target_node = param.node;

        $.ajax({
            type: 'GET'
            , url: 'get-featured-course-data.php'
            , dataType: 'json'
            , success: function(response) {
                var course_list = response.data;
                var new_course_scroll = $('<ul />');

                for (var i in course_list) {
                    course = course_list[i];
                    record = $('<li />');
                    record.html(course.title);
                    new_course_scroll.append(record);
                }

                target_node.append(new_course_scroll);
                new_course_scroll.liScroll();
            }
        });
    };

    $('#show_all_courses_button')
        .off('click')
        .click(function () {
            show_all_courses({
                'node' : $('#search_results_table')
            });
        });

    $('#input_search').typeahead({
        source: function (query, process) {
            get_course_search_data(process);
        }
    });

    $('#search_form')
        .off('submit')
        .submit(function () {
            var data = $('#search_form').serialize();

            $.ajax({
                'type': 'GET'
                , 'url': 'search-for-courses.php'
                , 'data': data
                , 'dataType': 'json'
                , 'success': function(response) {
                        show_course_results({
                            'course_list': response.data
                        });
                        console.log(response);
                    }
            });

            return false;
        });

    $('#advanced_search_button')
        .off('click')
        .click(function () {
            $('#advanced_search_parameters')
                .collapse('toggle');
        });

    populate_new_courses_ticker({
        'node' : $('div#new_courses_ticker')
    });

    populate_trending_courses_ticker({
        'node' : $('div#trending_courses_ticker')
    });

});