<?php
class Monster  
{
	public $searchString;
	public $site;
	public $jobs;
	public function __construct($param)
	{
		$input_search = $param['input_search'];
		//make the site into a string like:
		//http://rss.jobsearch.monster.com/rssquery.ashx?q=Web%20<<<<<<$searchString>>>>>>>
		$this->site = "http://rss.jobsearch.monster.com/rssquery.ashx?q=${input_search}";
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
		for($i = 0, $j = 0; $j < $amount; $i++)
		{
			$record = $this->jobs->channel->item[$i];
			$title = $record->title;

			if (array_key_exists("$title", $jobArray)) {
				continue;
			}

			$jobArray["$title"] = $record;
			$j++;
		}

		return array_values($jobArray);
	}
}

$API_VERSION = '0.0.1';

$monster = new Monster($_REQUEST);

$status = 0;
$messages = array();
$data = $monster->getJobs();

$object = array(
	'api_version' => $API_VERSION
	, 'status' => $status
	, 'messages' =>  $messages
	, 'data' => $data
	);

print json_encode($object);
?>