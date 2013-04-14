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
require_once("lib/database.php");

global $database;

date_default_timezone_set("America/Los_Angeles");

// create list of scrapers
$scrapers = array(new Udacity(), new Canvas());
$database = new Database($database);

$classes = parseClasses($scrapers);

$database->updateClasses($classes);

function parseClasses($scrapers) {
	$classInfo = array();
	
	foreach ($scrapers as $scraper) {
		if ($scraper->scraperName === "Udacity") {
			print "Udacity... skipping....\n";
			continue;
		}
		$result = $scraper->scrape(&$classes);
		if ($result) {
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