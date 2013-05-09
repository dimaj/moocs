$(function() {
    $.fn.serializeObject = function() {
        var o = Object.create(null),
            elementMapper = function(element) {
                element.name = $.camelCase(element.name);
                return element;
            },
            appendToResult = function(i, element) {
                var node = o[element.name];

                if ('undefined' != typeof node && node !== null) {
                    o[element.name] = node.push ? node.push(element.value) : [node, element.value];
                } else {
                    o[element.name] = element.value;
                }
            };

        $.each($.map(this.serializeArray(), elementMapper), appendToResult);
        return o;
    };

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
            , 'aaSorting': [[1, 'asc']]
            , aoColumns: [
                {
                    sTitle: 'Image'
                    , mData: function (source) {
                            var image;
                            image = '<img src="' + source.course_image + '" class="img-rounded" height="100" width="100">';
                            return image;
                        }
                }
                , {
                    sTitle: 'Title'
                    , mData: function (source) {
                            var title = source.title;
                            var link = source.course_link;
                            return '<a href=' + link + '>' + title + '</a>'
                        }
                }
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

    var show_job_results = function (param) {
        console.log(param);

        var job_list = param.job_list;
        var target = $('#job_results_table');

        target.dataTable({
            bDestroy: true
            , bJQueryUI: false
            , bFilter: false
            , bInfo: false
            , bPaginate: false
            , aaData: job_list
            , aoColumns: [
                {sTitle: 'Jobs', mData: function (source) {
                    var title = source.title;
                    var link = source.link;
                    return '<a href=' + link + '>' + title + '</a>'
                }}
            ]
        });
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
//                console.log(response);
                course_list = response.data;
                process(course_list)
            }
        });
        
        return;    
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
                    record = 
                        $('<li />')
                            .html(course.title);
                    new_course_scroll.append(record);
                }

                target_node.append(new_course_scroll);
                new_course_scroll.liScroll();
            }
        });
    };

    var populate_input_category = function (param) {
        var target_node = param.node;

        $.ajax({
            type: 'GET'
            , url: 'get-category-list.php'
            , dataType: 'json'
            , success: function(response) {
                var category_list = response.data;
                var input_select =
                    $('<select />')
                        .attr('name', 'category');

                record = 
                    $('<option />')
                        .html('--')
                        .val('')
                        ;
                        
                input_select.append(record);

                for (var i in category_list) {
                    category = category_list[i];
                    record = 
                        $('<option />')
                            .html(category)
                            .val(category);
                    input_select.append(record);
                }

                target_node.append(input_select);
            }
        });
    };

    var populate_input_site = function (param) {
        var target_node = param.node;

        $.ajax({
            type: 'GET'
            , url: 'get-site-list.php'
            , dataType: 'json'
            , success: function(response) {
                var site_list = response.data;
                var input_select =
                    $('<select />')
                        .attr('name', 'site');

                record = 
                    $('<option />')
                        .html('--')
                        .val('')
                        ;

                input_select.append(record);

                for (var i in site_list) {
                    site = site_list[i];
                    record = 
                        $('<option />')
                            .html(site)
                            .val(site);
                    input_select.append(record);
                }

                target_node.append(input_select);
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
        .submit(function (e) {
            e.preventDefault();

            var data = $('#search_form').serializeObject();
            console.log(data);

            if (/^\s*$/.test(data['input_search'])
                && /^\s*$/.test(data['category'])
                && /^\s*$/.test(data['site'])) {
                return;
            }

            console.log(data);

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

            $.ajax({
                'type': 'GET'
                , 'url': 'scrapers/MonsterScraper.php'
                , 'data': data
                , 'dataType': 'json'
                , 'success': function(response) {
                        show_job_results({
                            'job_list': response.data
                        });
                        console.log(response);
                    }
            });
            return;
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

    populate_input_category({
        'node' : $('div#input_category')
    });

    populate_input_site({
        'node' : $('div#input_site')
    });
});