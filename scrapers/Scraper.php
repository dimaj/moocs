<?php

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
	function __construct($database) {
		$this->db = $database;
		$this->ids = $database->getClassesIDs();
	}
	
	/**
		Scrapes content from sites.
		param $limit Used to limit how many classes to scrape before stopping. Default value is -1 (Scrape everythign)
		returns Array of scraped data
	*/
	public function scrape($limit = -1) {
		try {
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
				
				$category = $classInfo->getCategory();
				$classInfo->setCategory(ucwords(strtolower($category))); //Camelcases category

				$id = $this->db->getClassID($classInfo);
				if (!$id) {
					// update database with current class's results
					$this->db->updateClass($classInfo);
					$this->db->updateMetadata($classInfo);
				}
				else {
					// remove element from the ids array
					$this->ids = array_diff($this->ids, array($id));
				}
				
				// increment counter
				$count++;
			}
		}
		catch (Exception $e) {
			print "There was an exception while trying to scrape data from " . $this->siteName . "\n";
			print "Exception details: " . $e->getMessage() . "\n";
		}
		
		return $this->ids;
	}
	
	protected function getBaseURL($url) {
		$components = parse_url($url);
		
		return "{$components['scheme']}://{$components['host']}";
	}
	

}
?>