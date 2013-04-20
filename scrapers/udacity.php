<?php
/*
	This class is responsible for scraping class information
	from Udacity.com
 * 
 */
class Udacity extends Scraper {
	function __construct($name, $website, $classSearchStr, $database) {
		parent::__construct($database);
		$this->url = $website;
		$this->siteName = $name;
		$this->mainPageSearchStr = $classSearchStr;
		$this->baseURL = $this->getBaseURL($website);
	}
	
	function getClassInfo($class, $classInfoObj) {
		$this->getBasicClassInfo($class, &$classInfoObj);
		$this->getDetailedDescription(&$classInfoObj);
	}
	
    private function getBasicClassInfo($class, $classInfo) {
		//TODO: need to figure out why this does not work right now
    	$style = $class->getAttribute('style');
    	if ($style && ($style === "display: none'")) {
    		$classInfo = null;
    		print "Exiting out.\n";
    		return;
    	}
    	
    	$link = $class->find('a', 0)->getAttribute('href');

		$thumbnail = $class->find('div[class=crs-li-thumbnails] img', 0)->getAttribute('src');
		
		$mainTitle = $class->find('div[class=crs-li-name-and-tags] div[class=crs-li-name] div', 0)->text(); 
		$subTitle = $class->find('div[class=crs-li-name-and-tags] div[class=crs-li-name] div', 1)->text();
		$title = "{$mainTitle}: {$subTitle}";
		
		$category = $class->find('div[class=crs-li-name-and-tags] div[class=crs-li-tags] div', 0)->text();
		
		$shortDesc = $class->find('div[class=crs-li-text]', 0)->text();
		
		$classInfo->setCourseLink("{$this->baseURL}{$link}");
		$classInfo->setTitle($title);
		$classInfo->setSite($this->siteName);
		$classInfo->setCategory($category);
		$classInfo->setShortDescription(trim($shortDesc));
		$classInfo->setCourseImage($thumbnail);
	}
        
    private function getDetailedDescription($classInfo) {
		$class = file_get_html($classInfo->getCourseLink());
		
		$vidID = $class->find('div[class=scale-media]', 0)->first_child()->getAttribute('video-id');
		$video = "http://www.youtube.com/watch?v={$vidID}";

		$desc = $class->find('div.sum-need-get', 0)->plaintext;
		
		$instName = $class->find('div.inst-bio h5', 0)->text();		
		$instImg = $class->find('div.inst-bio img', 0)->getAttribute('src');



		$classInfo->setVideoLink($video);
		$classInfo->setCourseLength("");
		$classInfo->setStartDate("");
		$classInfo->setLongDescription(trim($desc));
		$classInfo->setProfName(trim($instName));
		$classInfo->setProfImage($instImg);
	}
	
}
?>
