<?php
/*
	This class is responsible for scraping class information
	from Udacity.com
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
// 		$startDate = $this->getStartDate($top);
// 		$endDate = $this->getEndDate($top);

		// get price of this class
// 		$price = $this->getClassPrice($top);

		// get full class description
		$description = $this->getFullDescription($bottom);
		
		// get prof name
		$profName = $this->getProfName($bottom);
		
		// get prof image
		$profImage = $this->getProfImage($bottom);
		
		$category = $this->getClassCategory($class);
		
		$retVal = array(
			"title" => $title,
			"classImageURL" => $imageURL,
// 			"startDate" => $startDate,
// 			"endDate" => $endDate,
// 			"price" => $price,
			"longDesc" => $description,
			"profName" => $profName,
			"profImage" => $profImage,
			"category" => $category			
		);
		
		return $retVal;
	}
	
	private function getClassCategory($root) {
		return "Science";
	}
	
	private function getProfName($root) {
		$instructor = $root->find('div[class=instructor-bio] h3[class=last emboss-light]', 0)->text();
		return $instructor;
	}
	
	private function getProfImage($root) {
		$image = $root->find('div[class=instructor-bio] img', 0)->getAttribute('src');
		return $this->url . $image;
	}
	
	private function getFullDescription($root) {
		$longDescNodes = $root->find('div[class=content-box first last] div[class=block-box two-thirds first-box] p');
		$longDesc = "";
		for ($i = 0; $i < count($longDescNodes); $i++) {
			$longDesc = $longDesc . $longDescNodes[$i]->text() . "\n";
		}
		return trim($longDesc);
	}
	
	private function getClassPrice($root) {
		$price = $root->find('div.course-detail-info p strong', 2)->text();
		return $price;
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
		// start and end dates
		$startDate = $root->find('div.course-detail-info p strong', 0)->text();
		if (!startDate) {
			return null;
		}
		$startDate = DateTime::createFromFormat("M d, Y+", $startDate)->format("Y-m-d");
		return $startDate;
	}
	
	private function getEndDate($root) {
		// start and end dates
		$endDate = $root->find('div.course-detail-info p strong', 1)->text();
		if (!endDate) {
			return null;
		}

		$endDate = DateTime::createFromFormat("M d, Y+", $endDate)->format("Y-m-d");
		return $endDate;
	}
}

?>