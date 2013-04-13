<?php
/*
	This class is responsible for scraping class information
	from Udacity.com
 */
class Udacity {
	function __construct() {
		$this->url = "https:/www.udacity.com";
		print "Class Udacity has been created.\n";
	}
	
	function scrape($classes) {
		print "In Udacity's scrape method.\n";
		$classes = array("Udacity 1", "Udacity 2");
		return true;
	}
}
?>