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
	function __construct($name, $website, $classSearchStr) {
		parent::__construct();
		$this->url = $website;
		$this->scraperName = $name;
		$this->mainPageSearchStr = $classSearchStr;
	}
	
	function getClassInfo($class, $classInfoObj) {
		return $this->createSampleData();
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
