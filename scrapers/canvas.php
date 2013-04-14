<?php
/* 
   File   : canvas.php
   Subject: CS160 MOOCs mashup
   Authors: Dmitry Jerusalimsky
   Version: 1.0
   Date   : Apr 12, 2013
   Description: Scrapes class information from Canvas.net
 */

include('lib/simple_html_dom.php');

class Canvas {
	function __construct() {
		$this->url = "https://www.canvas.net";
	}
	
	function scrape($classes) {
		$site = file_get_html($this->url);
		
		$classes = $this->getClasses($site);
		return true;
	}

	private function getClasses($site) {
		$classes = array();
		// loop through classes on the site
		foreach ($site->find('div[class=block-box no-pad featured-course-outer-container]') as $class) {
			$basic = $this->getBasicClassInfo($class);

			$detailed = $this->getDetailedDescription($basic["link"]);
			$classInfo = array_merge($basic, $detailed);
			
			array_push($classes, $classInfo);
		}
		
		return $classes;
	}
	
	private function getBasicClassInfo($class) {
		$link = $class->find('div.learn-more-container a', 0)->getAttribute('href');
		$shortDesc = $class->find('p[class=last fineprint pad-box-mini top-rule-box featured-course-desc]', 0)->text();
		
		$retVal = array(
			"link" => trim($link),
			"shortDesc" => trim($shortDesc),
			"site" => "Canvas"
		);
		
		return $retVal;
	}
	
	private function getDetailedDescription($classURL) {
		$class = file_get_html($classURL);
		$main = $class->find('section[id=main]');
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
		
		$category = $this->getClassCategory($class);
		
		$isFull = $this->getClassStatus($top);
		
		$duration = $this->getClassDuration($startDate, $endDate);
		$retVal = array(
			"title" => $title,
			"classImageURL" => $imageURL,
			"startDate" => $startDate,
			"endDate" => $endDate,
			"price" => $price,
			"longDesc" => $description,
			"profName" => $profName,
			"profImage" => $profImage,
			"category" => $category,
			"status" => $isFull,
			"duration" => $duration
		);
		
		return $retVal;
	}
	
	private function getClassCategory($root) {
		return "Science";
	}
	
	private function getProfName($root) {
		$name = $root->find('div[class=instructor-bio] h3[class=last emboss-light]', 0);
		if (!$name) {
			return null;
		}
		
		return $name->text();
	}
	
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
	
	private function getDatePriceBlock($root) {
		$block = $root->find('div.course-detail-info', 0);
		$block = $block->find('p', 0);
		$block = $block->find('strong');
		
		$block = $root->find('div.course-detail-info h4', 0);
		$block = $block->next_sibling();
		$block = $block->find('strong');
		
		return $block;
	}
	
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
	
	private function getClassStatus($root) {
		$status = $root->find('div.course-detail-info p[class=pad-box-micro center-box corner-box bevel-box alert-box corner-box emboss-light] strong', 0);
		return ($status) ? "Full" : "Available";
	}
	
	private function getClassImageURL($root) {
		// get image url
		$imageURL = $root->find('header.pad-box-micro div.ribbon-positioner div.featured-course-image span', 0)->getAttribute('style');
		preg_match( '/\((.*?)\)/', $imageURL, $match);
		if (isset($match[1])) {
			$imageURL = $this->url . $match[1];
		}
		
		return $imageURL;
	}
	
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
	
	private function formatDate($dateStr) {
		// make sure that date is in correct format
		preg_match( '/(\w+)\s*([0-9]{1,2}),\s*([0-9]{4})/', $dateStr, $match);
		if (count($match) === 4) {
			$dateStr = $match[1] . " ";
			$dateStr .= (strlen($match[2]) === 1) ? ("0".$match[2]) : $match[2];
			$dateStr .= ", " . $match[3];
			$retVal = DateTime::createFromFormat("M d, Y+", $dateStr)->format("Y-m-d");
			return $retVal;
		}
		return null;
	}
	
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

	private function getClassDuration($start, $end) {
		if (!$start || !$end) {
			return null;
		}
		
		$start = DateTime::createFromFormat("Y-m-d", $start);
		$end = DateTime::createFromFormat("Y-m-d", $end);
		$diff = $start->diff($end);
		$diff = round($diff->format("%R%a") / 7);
		return $diff;
	}
}

?>