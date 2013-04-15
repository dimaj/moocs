<?php
/*
	This class is responsible for scraping class information
	from Udacity.com
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 */
class Udacity {
	function __construct() {
		$this->url = "https://www.udacity.com/courses";
		$this->scraperName = "Udacity";
		print "Class Udacity has been created.\n";
	}
	
	function scrape($classes) {
		$site = file_get_html($this->url);
                
		$classes = $this->getClasses($site);
		return true;
	}
        
        private function getClasses($site) {
		$classes = array();
                print "getting classes...\n";
                $counter = 1;
		// loop through classes on the site
		foreach ($site->find('li data-ng-show=isCourseShown(\'[^A-Za-z0-9]\')') as $class) 
                    {
                    print  $counter++ . "\n";
                    //print $class->find('a',0)->getAttribute('href');
                        //$basic = $this->getBasicClassInfo($class, $site);

			//$detailed = $this->getDetailedDescription($basic["link"]);
			//$classInfo = array_merge($basic, $detailed);
			
			//array_push($classes, $classInfo);
                    }
		
		return $classes;
	}
        
        private function getBasicClassInfo($class, $site) {
                $link  = $class->childNodes(0)->getAttribute('href');
                print $link . "\n";
		$shortDesc = $class->find('div class="crs-li-text"', 0)->text();
		
		$retVal = array(
			"link" => trim($link),
			"shortDesc" => trim($shortDesc),
			"site" => "Udacity"
		);
                
                print "message" . $retVal[0] . "\n";
		
		return $retVal;
        }
        
        private function getDetailedDescription($classURL) {
		$class = file_get_html($classURL);
		$main = $class->find('section[id=main]');
		$top = $class->find('section[id=main] div[class=gray-noise-box pad-box no-sides]', 0);
		$bottom = $class->find('section[id=main] div[class=light-bg pad-box no-sides top-rule-box]', 0);
		
		
		// get image url
		$imageURL = $class->find('div class="crs-li-thumbnails" img',0)->getAttribute('src');
		
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
			"duration" => $duration,
			"video_link" => ""
		);
		
		return $retVal;
	}
	
}
?>
