<?php
/*
	This class is responsible for scraping class information
	from Udacity.com
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
		// loop through classes on the site
		foreach ($site->find('li data-ng-show="isCourseShown') as $class) 
                    {
                        
			$basic = $this->getBasicClassInfo($class);

			$detailed = $this->getDetailedDescription($basic["link"]);
			$classInfo = array_merge($basic, $detailed);
			
			array_push($classes, $classInfo);
                    }
		
		return $classes;
	}
        
        private function getBasicClassInfo($class) {
                $link  = $class->find('style a', 0)->getAttribute('href');
            
		$shortDesc = $class->find('div class="crs-li-text"', 0)->text();
		
		$retVal = array(
			"link" => trim($link),
			"shortDesc" => trim($shortDesc),
			"site" => "Udacity"
		);
		
		return $retVal;
        }
}
?>
