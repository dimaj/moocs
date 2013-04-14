<?php
/**
  This is the driver class for the project.
  It is responsible for scraping class information
  from included scrapers and updating database with
  newly found information.
*/

require_once("scrapers/canvas.php");
require_once("scrapers/udacity.php");
require_once("config/config.php");

date_default_timezone_set("America/Los_Angeles");

// create list of scrapers
$scrapers = array(new Udacity(), new Canvas());

$classes = parseClasses($scrapers);

updateDatabase($classes);

function parseClasses($scrapers) {
	$classInfo = array();
	
	for ($i = 0; $i < count($scrapers); $i++) {
		$curScraper = $scrapers[$i];
		$result = $curScraper->scrape(&$classes);
		if ($result) {
			print "Found " . count($classes) . " classes\n";	
			$classInfo = array_merge($classInfo, $classes);
		}
		else {
			// there was an error
		}
	}
	
	print "Total of " . count($classInfo) . " has been scraped.\n";
	
	return $classInfo;
}

function updateDatabase($classes) {
	// loop through $classes and update database
	print "Not yet implemented.\n";
}

?>