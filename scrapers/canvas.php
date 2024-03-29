<?php
/* 
   File   : canvas.php
   Subject: CS160 MOOCs mashup
   Authors: Dmitry Jerusalimsky
   Version: 1.0
   Date   : Apr 12, 2013
   Description: Scrapes class information from Canvas.net
 */

/**
	Class that implements scraper for Canvas.net
*/
class Canvas extends Scraper{

	/**
		Constructor of this class.
		param $classSearchStr Search string that separates each class from the main page
	*/
	function __construct($name, $website, $classSearchStr, $database) {
		parent::__construct($database);
		$this->url = $website;
		$this->siteName = $name;
		$this->mainPageSearchStr = $classSearchStr;
		$this->baseURL = $this->getBaseURL($website);
	}
	
	/**
		Gets class information by scraping the site
		param $class HTML block that contains class info from the main page
		return Array with class information
	*/
	protected function getClassInfo($class, $classInfoObj) {
		// get basic class information (from the main page)
		$this->getBasicClassInfo($class, &$classInfoObj);
		
		// get detailed class information (by navigating to class' page and scraping it)
		$this->getDetailedDescription(&$classInfoObj);
	}
	
	/**
		Gets basic class information
		param $class HTML block with current class from main page
		return Array with scraped information
	*/
	private function getBasicClassInfo($class, $classInfo) {
		// get the URL to the class details page
		$link = $class->find('div.learn-more-container a', 0)->getAttribute('href');
		// get short description
		$shortDesc = $class->find('p[class=last fineprint pad-box-mini top-rule-box featured-course-desc]', 0)->text();
		
		$classInfo->setCourseLink(trim($link));
		$classInfo->setShortDescription(trim($shortDesc));
		$classInfo->setSite($this->siteName);
	}
	
	/**
		Gets detailed class information
		param $classURL URL of the class that is to be scraped
		return Array with scraped information
	*/
	private function getDetailedDescription($classInfo) {
		$class = file_get_html($classInfo->getCourseLink());
		
		// page is broken up into 2 sections: top and bottom. Below are the HTML Nodes for Top section and bottom section
		$top = $class->find('section[id=main] div[class=gray-noise-box pad-box no-sides]', 0);
		$bottom = $class->find('section[id=main] div[class=light-bg pad-box no-sides top-rule-box]', 0);
		
		// get image url
		$imageURL = $this->getClassImageURL($top);
		
		// get course title
		$title = $top->find('div.course-detail-info h2', 0)->text();
		
		// start and end dates
		$startDate = $this->getStartDate($top);
		$endDate = $this->getEndDate($top);

		// get price of this class
		$price = $this->getClassPrice($top);

		// get full class description
		$description = $this->getFullDescription($bottom);
		
		// get prof name
		$profName = $this->getProfName($bottom);
		
		// get prof image
		$profImage = $this->getProfImage($bottom);
		
		// get class category
		$category = $this->getClassCategory($class);
		
		// get class status
		$isFull = $this->getClassStatus($top);
		
		// get class duration in weeks
		$duration = $this->getClassDuration($startDate, $endDate);
		
		$classInfo->setTitle(trim($title));
		$classInfo->setLongDescription(trim($description));
		$classInfo->setVideoLink("");
		$classInfo->setStartDate($startDate);
		$classInfo->setCourseLength($duration);
		$classInfo->setCategory("Science");
		$classInfo->setCourseImage($imageURL);
		$classInfo->setProfName($profName);
		$classInfo->setProfImage($profImage);
		
		#return $retVal;
	}
	
	/**
		Gets class category (hardcoded to Science)
		param $root Root element from which search is going to take place
		return Class category
	*/
	private function getClassCategory($root) {
		return "Science";
	}
	
	/**
		Gets Teacher's name
		param $root Root element from which search is going to take place
		return Name of the teacher
	*/
	private function getProfName($root) {
		$name = $root->find('div[class=instructor-bio] h3[class=last emboss-light]', 0);
		if (!$name) {
			return null;
		}
		
		return $name->text();
	}
	
	/**
		Gets teacher's image url
		param $root Root element from which search is going to take place
		return Teacher's image url
	*/
	private function getProfImage($root) {
		$image = $root->find('div[class=instructor-bio] img', 0);
		if (!$image) {
			return null;
		}
		
		return $this->url . $image->getAttribute('src');
	}
	
	private function getFullDescription($root) {
		$longDescNodes = $root->find('div[class=content-box first last] div[class=block-box two-thirds first-box] p');
		$longDesc = "";
		for ($i = 0; $i < count($longDescNodes); $i++) {
			$longDesc = $longDesc . $longDescNodes[$i]->text() . "\n";
		}
		return trim($longDesc);
	}
	
	/**
		Gets HTML Node that contains both class price and class dates
		param $root Root element from which search is going to take place
		return HTML Node
	*/
	private function getDatePriceBlock($root) {
		$block = $root->find('div.course-detail-info', 0);
		$block = $block->find('p', 0);
		$block = $block->find('strong');
		
		$block = $root->find('div.course-detail-info h4', 0);
		$block = $block->next_sibling();
		$block = $block->find('strong');
		
		return $block;
	}
	
	/**
		Gets class price (Free or its price)
		param $root Root element from which search is going to take place
		return Class price
	*/
	private function getClassPrice($root) {
		$price = $this->getDatePriceBlock($root);
		$retVal = "";
		if (count($price) === 3) {
			$retVal = $price[2]->text();
		}
		else {
			$retVal = $price[1]->text();
		}
		return $retVal;
	}
	
	/**
		Gets class status (e.g. Full or Available)
		param $root Root element from which search is going to take place
		return Class status
	*/
	private function getClassStatus($root) {
		$status = $root->find('div.course-detail-info p[class=pad-box-micro center-box corner-box bevel-box alert-box corner-box emboss-light] strong', 0);
		return ($status) ? "Full" : "Available";
	}
	
	/**
		Gets class image url
		param $root Root element from which search is going to take place
		return URL of the image for the class
	*/
	private function getClassImageURL($root) {
		// get image url
		$imageURL = $root->find('header.pad-box-micro div.ribbon-positioner div.featured-course-image span', 0)->getAttribute('style');
		preg_match( '/\((.*?)\)/', $imageURL, $match);
		if (isset($match[1])) {
			$imageURL = $this->url . $match[1];
		}
		
		return $imageURL;
	}
	
	/**
		Gets class start date
		param $root Root element from which search is going to take place
		return Class start date
	*/
	private function getStartDate($root) {
		$block = $this->getDatePriceBlock($root);

		$date = $block[0]->text();
		$pos = strpos($date, "available");
		if ($pos !== false) {
			// remove everything before 'available' including 'available'
			$date = substr($date, $pos + strlen("available"));
		}
		
		$retVal = $this->formatDate(trim($date));
		return $retVal;
	}
	
	/**
		Formats date from what website is showing to Y-m-d (e.g. 2013-04-15)
		param $dateStr String representation of date to be formatted
		return Date in correct format
	*/
	private function formatDate($dateStr) {
		$month = array('Jan' => 1, 'Feb' => 2, 'Mar' => 3, 'Apr' => 4
				, 'May' => 5, 'Jun' => 6, 'Jul' => 7, 'Aug' => 8
				, 'Sep' => 9, 'Oct' => 10, 'Nov' => 11, 'Dec' => 12);
		// make sure that date is in correct format
		preg_match( '/(\w+)\s*([0-9]{1,2}),\s*([0-9]{4})/', $dateStr, $match);
		if (count($match) === 4) {
			$retVal = sprintf("%4d-%02d-%02d", intval($match[3]), $month[$match[1]], intval($match[2]));
			return $retVal;
		}
		return null;
	}
	
	/**
		Gets class end date
		param $root Root element from which search is going to take place
		return Class end date
	*/
	private function getEndDate($root) {
		$block = $this->getDatePriceBlock($root);
		// start and end dates
		if (count($block) === 3) {
			$date = trim($block[1]->text());
			$retVal = $this->formatDate($date);
			return $retVal;
		}
		
		return null;
	}

	/**
		Compute class duration
		param $start Class start date
		param $end Class end date
	*/
	private function getClassDuration($start, $end) {
		if (!$start || !$end) {
			return null;
		}
		
		$start = new DateTime($start);
		$end = new DateTime($end);
		$diff = round(abs($end->format('U') - $start->format('U')) / (60*60*24*7));
		return $diff;
	}
}

?>