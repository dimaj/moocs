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
	
	function createSampleData() {
		$arr = array(
			"link" => "https://www.canvas.net/courses/precalculus-algebra",
		    "shortDesc" => "Students often encounter grave difficulty in calculus if their algebraic knowledge is insufficient. This course is designed to provide students with algebraic knowledge needed for success in a typical calculus course. We explore a suite of functions used in calculus, including polynomials (with special emphasis on linear and quadratic functions), rational functions, exponential functions, and logarithmic functions. Along the way, basic strategies for solving equations and inequalities are reinforced, as are strategies for interpreting and manipulating a variety of algebraic expressions. Students enrolling in the course are expected to have good number sense and to have taken an intermediate algebra course. The course will use online access to Wiley Plus, which is an online fee-based service.  Information about accessing and paying for the service will be available when the course starts. Students wanting to gain credit in for Math 111 at Ball State University will be able to take a proctored end-of-course exam. More details (including sites and costs) will be provided to course participants. ",
		    "site" => "Udacity",
		    "title" => "Precalculus Algebra 7",
		    "classImageURL" => "https://www.canvas.net/assets/courses/44/image.png",
		    "startDate" => "2013-05-13",
		    "endDate" => "2013-06-16",
		    "price" => "Free",
		    "longDesc" => "Students often encounter grave difficulty in calculus if their algebraic knowledge is insufficient. This course is designed to provide students with algebraic knowledge needed for success in a typical calculus course. We explore a suite of functions used in calculus, including polynomials (with special emphasis on linear and quadratic functions), rational functions, exponential functions, and logarithmic functions. Along the way, basic strategies for solving equations and inequalities are reinforced, as are strategies for interpreting and manipulating a variety of algebraic expressions. Students enrolling in the course are expected to have good number sense and to have taken an intermediate algebra course.\nThe course will use online access to Wiley Plus, which is an online fee-based service.  Information about accessing and paying for the service will be available when the course starts. Students wanting to gain credit in for Math 111 at Ball State University will be able to take a proctored end-of-course exam. More details (including sites and costs) will be provided to course participants. ",
		    "profName" => "John Lorch, Ph.D.",
		    "profImage" => "https://www.canvas.net/assets/courses/44/avatar.jpg",
		    "category" => "Science",
		    "status" => "Full",
		    "duration" => "5",
		    "video_link" => ""
		);
		
		return $arr;
	}
	
    private function getClasses($site) {
		$classes = array();
        print "getting classes...\n";
        $counter = 1;
		// loop through classes on the site
		foreach ($site->find('li data-ng-show=isCourseShown(\'[^A-Za-z0-9]\')') as $class) 
    	{
             print  $counter++ . "\n";
             foreach($class->find('a') as $course)
             {
                 print $course->href . "\n";
             }
             //print $class->find('a',0)->getAttribute('href');
             //$basic = $this->getBasicClassInfo($class, $site);

			//$detailed = $this->getDetailedDescription($basic["link"]);
			//$classInfo = array_merge($basic, $detailed);
			
			//array_push($classes, $classInfo);
	    }
		
		return $classes;
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
