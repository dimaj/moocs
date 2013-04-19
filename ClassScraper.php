<?php
/**
  This is the driver class for the project.
  It is responsible for scraping class information
  from included scrapers and updating database with
  newly found information.
*/

require_once("scrapers/Scraper.php");
require_once("scrapers/canvas.php");
require_once("scrapers/udacity.php");
require_once("config/config.php");
require_once("lib/database.php");

date_default_timezone_set("America/Los_Angeles");

// create list of scrapers
if (!isset($GLOBALS['scrapers'])) {
	die ("At least one scraper must be defined.");
}

$db = new Database();

$addedScrapers = array();

foreach ($GLOBALS['scrapers'] as $scraper) {
	$class = $scraper['className'];
	$searchString = $scraper['mainSearchString'];
	$website = $scraper['mainURL'];
	$name = $scraper['name'];
	
	$scraperObj = new $class($name, $website, $searchString, $db);
	array_push($addedScrapers, $scraperObj);
}

// Clear database tables
$db->clearTables();

// iterate over the scrapers and scrape data
foreach ($addedScrapers as $scraper) {
	$scraper->scrape();
}
	
?>