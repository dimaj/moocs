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

date_default_timezone_set("America/Los_Angeles");

// create list of scrapers
$scrapers = array(
	new Canvas("div[class=block-box no-pad featured-course-outer-container]"),
	new Udacity("li data-ng-show=isCourseShown(\'[^A-Za-z0-9]\')")
);

// iterate over the scrapers and scrape data
foreach ($scrapers as $scraper) {
	$scraper->scrape();
}
	
?>