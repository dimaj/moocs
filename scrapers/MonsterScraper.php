<?php
//include("Scraper.php");
/*
	This class is responsible for scraping class information
	from Udacity.com
 * 
 */
 //test site: "http://rss.jobsearch.monster.com/rssquery.ashx?q=Web%20computer-science"
//$job = simplexml_load_file('rssquery.xml');
//print $job->channel->item[1]->title;
class Monster  
{
	public $searchString;
	public $site;
	public $jobs;
	public function __construct()
	{
		//make the site into a string like:
		//http://rss.jobsearch.monster.com/rssquery.ashx?q=Web%20<<<<<<$searchString>>>>>>>
		$this->site = "http://rss.jobsearch.monster.com/rssquery.ashx?q=Web%20computer-science";
		$this->jobs = simplexml_load_file($this->site);
	}

	/**
	This method gets the first 5 jobs from the RSS feed
	*/
	function getJobs()
	{
		//array to hold job titles
		$jobArray = array();
		$amount = 5;
		for($i = 0, $j = 0; $i < $amount; $i++)
		{
			$title = $this->jobs->channel->item[$i]->title;
			print $i . " " . $title . "\n";
			
			
			//if the job title is already in the array, don't add it
			//and add one to the amount of jobtitles being looked at
			//DOESN'T TAKE CARE OF DUPLICATES YET FOR SOME REASON.
			if(in_array($title, $jobArray))
			{
				$amount++;
				print "duplicate: ". $title;
			}
			else 
			{
				$jobArray[$j++] = $title;	
			}	
		}
			$x = 1;
			print "\n\nnow printing array:\n";
			foreach ($jobArray as $jobTitles)
			{
				print $x++ . " " . $jobTitles . "\n";
			}
	}
}
	$monster = new Monster;
	$monster->getJobs();
?>