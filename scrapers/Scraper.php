<?php

require_once("lib/database.php");
include('lib/simple_html_dom.php');
require_once("ClassInfo.php");

/**
	This abstract class tells other scrapers how they should "behave"
	by specifying methods that they have to implement.
	This class is also responsible for executing the scrape and updating
	database with aquired results
*/
abstract class Scraper {

	/**
		Method that has to be implemented that does all the scraping
	*/
	abstract protected function getClassInfo($class, $classInfoObj);

	/**
		Default constructor
	*/
	function __construct() {
		$this->db = new Database();
	}
	
	/**
		Scrapes content from sites.
		param $limit Used to limit how many classes to scrape before stopping. Default value is -1 (Scrape everythign)
		returns Array of scraped data
	*/
	public function scrape($limit = -1) {
		$site = file_get_html($this->url);
		
		if (!$limit) {
			$limit = -1;
		}
		
		$count = 0;
		foreach ($site->find($this->mainPageSearchStr) as $class) {
			$classInfo = new ClassInfo();
			// if we are over our limit, abort
			if (($limit !== -1) && ($count >= $limit)) {
				break;
			}
			
			// get class information
			$this->getClassInfo($class, &$classInfo);
			if (!$classInfo) {
				continue;
			}

			// update database with current class's results
			$this->db->updateClass($classInfo);

			// increment counter
			$count++;
		}
		
		return true;
	}
}
?>