$(function() {
	alert("Stanford course count: " + $('table[id=course-listing-grid] tbody tr').not('[class=header-row]').length);
});