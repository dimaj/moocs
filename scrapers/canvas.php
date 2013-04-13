<?php
/*
	This class is responsible for scraping class information
	from Udacity.com
 */

class Canvas {
	function __construct() {
		$this->url = "https:/www.canvas.net";
		print "Class Canvas has been created.\n";
	}
	
	function scrape($classes) {
		print "In Canvas' scrape method.\n";
		$classes = array("Canvas 1", "Canvas 2");
		return true;
	}
}
?>