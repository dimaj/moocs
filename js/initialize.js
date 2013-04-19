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
        ]
	});
});
