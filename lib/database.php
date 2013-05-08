<?php
/* File   : database.php
   Subject: CS160 MOOCs mashup
   Authors: Dmitry Jerusalimsky
   Version: 1.0
   Date   : Apr 12, 2013
   Description: Updates database with latest class information
*/

class Database {
	/**
		Creates an instance of the database class using configuration values from
		config.php file
	*/
	function __construct() {
		$this->moocs = "config/moocs.sql";
		$this->tracker = "config/course_meta.sql";
		$this->checkConfig();
		$this->connectDB();
		if ($this->checkDB()) {
			// add tables to db
			$this->setupDB();
		}
	}

	/**
		This function reads sql file with table definitions and executes read-in queries
	*/
	private function setupDB() {
		$err;
		// setup main DB
		$sql = explode(";",file_get_contents(realpath($this->moocs)));
		foreach ($sql as $query) {
			$query = trim($query);
			if (strlen($query) == 0) {
				continue;
			}
			
			mysql_query($query)
				or die ("Error while executing query '" . $query . "'..." . mysql_error() . "\n");
		}

		// setup CourseTracker DB
		$sql = explode(";",file_get_contents(realpath($this->tracker)));
		foreach ($sql as $query) {
			$query = trim($query);
			if (strlen($query) == 0) {
				continue;
			}
			
			mysql_query($query)
				or die ("Error while executing query '" . $query . "'..." . mysql_error() . "\n");
		}

		$query = "ALTER TABLE `course_data` ENGINE = MYISAM";
		mysql_query($query)
			or die("Error updating table course_data engine");

		$query = "ALTER TABLE `coursedetails` ENGINE = MYISAM";
		mysql_query($query)
			or die("Error updating table coursedetails engine");

		$query =
			"ALTER TABLE  `course_data` ADD FULLTEXT (
				`title`
				, `short_desc`
				, `long_desc`
			)";
		mysql_query($query)
			or die("Error adding course_data fulltext index");

		$query =
			"ALTER TABLE  `coursedetails` ADD FULLTEXT (
				`profname`
			)";
		mysql_query($query)
			or die("Error adding coursedetails index");
	}
	
	/**
		Checks to see if all required tables are present
	*/
	private function checkDB() {
		$course_data = "select count(*) FROM information_schema.TABLES WHERE (TABLE_SCHEMA = '". $GLOBALS['db']['db'] . "') AND (TABLE_NAME = 'course_data')";
		$coursedetails = "select count(*) FROM information_schema.TABLES WHERE (TABLE_SCHEMA = '". $GLOBALS['db']['db'] . "') AND (TABLE_NAME = 'coursedetails')";
		$coursemeta = "select count(*) FROM information_schema.TABLES WHERE (TABLE_SCHEMA = '". $GLOBALS['db']['db'] . "') AND (TABLE_NAME = 'course_meta')";
		
		$c_dRes = mysql_query($course_data)
			or die("Error while checking existence of 'course_data' table..." . mysql_error() . "\n");
		$cdRes = mysql_query($coursedetails)
			or die("Error while checking existence of 'coursedetails' table..." . mysql_error() . "\n");
		$cmRes = mysql_query($coursemeta)
			or die("Error while checking existence of 'course_meta' table..." . mysql_error() . "\n");

		$c_dCount = mysql_result($c_dRes, 0);
		$cdCount = mysql_result($cdRes, 0);
		$cmCount = mysql_result($cmRes, 0);
		
		return ($c_dCount == 0 || $cdCount == 0 || $cmCount == 0);
	}
	
	/**
		Creates a table for tracking newly added courses
	*/
	function createCourseTrackingTable() {
		$sql = "";
	}
	
	/**
		Clears tables from selected database
	*/	
	function clearTables() {
		// clear table data
		mysql_query("TRUNCATE TABLE course_data");
		mysql_query("TRUNCATE TABLE coursedetails");
	}
	
	/**
		Connects to the database and selects a database
	*/
	private function connectDB() {
		$host = "";
		if (isset($GLOBALS['db']['port'])) {
			$host = "{$GLOBALS['db']['host']}:{$GLOBALS['db']['port']}";
		}
		else {
			$host = "{$GLOBALS['db']['host']}:3306";
		}
		// connect to database
		$this->conn = @mysql_connect($host, $GLOBALS['db']['user'], $GLOBALS['db']['pass'])
			or die ("Could not connect to host '" . $GLOBALS['db']['host'] . "'.\n");
		$isSelected = mysql_select_db($GLOBALS['db']['db']);
		if (!$isSelected) {
			// create database
			$query = "CREATE DATABASE IF NOT EXISTS " . $GLOBALS['db']['db'];
			$err;
			$this->executeQuery($query, &$err)
				or die ("Could not create database... " . $err . "\n");
		
			// select database
			mysql_select_db($GLOBALS['db']['db'])
				or die ("Could not select database..." . mysql_error());
		}
	}
	
	/**
		Checks configuration file to make sure that every required field is present
	*/
	private function checkConfig() {
		if ($this->isNullOrEmpty($GLOBALS['db']['host'])) {
			die ("Hostname is required for database connetion.\nPlease configure it in config/config.php file.\n");
		}
		if ($this->isNullOrEmpty($GLOBALS['db']['user'])) {
			die ("Username is required for database connetion.\nPlease configure it in config/config.php file.\n");
		}
		if ($this->isNullOrEmpty($GLOBALS['db']['pass'])) {
			die ("Password is required for database connetion.\nPlease configure it in config/config.php file.\n");
		}
		if ($this->isNullOrEmpty($GLOBALS['db']['db'])) {
			die ("Database name is required for database connetion.\nPlease configure it in config/config.php file.\n");
		}
	}

	/**
		Checks if current string is null or is empty
	*/
	private function isNullOrEmpty($str) {
		return (!$str || strlen(trim($str)) === 0);
	}
	
	/**
		Creates a query string to update course_data table
	*/
	private function constructCourseDataQuery($class) {
		$query = "INSERT INTO course_data VALUES ";
		$query .= "(";
		$query .= "(select count(*) from coursedetails)+1"; // we need to add 1 as this is the first query to be executed
		$query .= ",'" . $this->getMysqlString($class->getTitle()) . "'";
		$query .= ",'" . $this->getMysqlString($class->getShortDescription()) . "'";
		$query .= ",'" . $this->getMysqlString($class->getLongDescription()) . "'";
		$query .= ",'" . $this->getMysqlString($class->getCourseLink()) . "'";
		$query .= ",'" . $this->getMysqlString($class->getVideoLink()) . "'";
		$query .= ",'" . $this->getMysqlString($class->getStartDate()) . "'";
		$query .= ",'" . $this->getMysqlString($class->getCourseLength()) . "'";
		$query .= ",'" . $this->getMysqlString($class->getCourseImage()) . "'";
		$query .= ",'" . $this->getMysqlString($class->getCategory()) . "'";
		$query .= ",'" . $this->getMysqlString($class->getSite()) . "'";
		$query .= ")";
		
		return $query;
	}
	
	private function getMysqlString($str) {
		return mysql_real_escape_string($str);
	}
	
	/**
		Creates a query string to update coursedetails table
	*/
	private function constructCorseDetailsQuery($class) {
		$query = "INSERT INTO coursedetails VALUES ";
		$query .= "(";
		$query .= "(select count(*) from course_data)"; // we don't need +1 as previous query is executed first
		$query .= ",'" . $this->getMysqlString($class->getProfName()) . "'";
		$query .= ",'" . $this->getMysqlString($class->getProfImage()) . "'";
		$query .= ")";

		return $query;
	}
	
	/**
		Updates database by executing queries
	*/
	public function updateClass($class) {
		$query1 = $this->constructCourseDataQuery($class);
		$query2 = $this->constructCorseDetailsQuery($class);
		$err;
		if (!$this->executeQuery($query1, &$err)) {
			echo $err;
		}
		if (!$this->executeQuery($query2, &$err)) {
			echo $err;
		}
	}
	
	private function executeQuery($query, $error) {
		try {
			return mysql_query($query);
		}
		catch (MySQLException $err) {
		    $error = $err;
		    $err->getMessage();
			echo $err;
		}
		catch (MySQLDuplicateKeyException $err) {
		    $error = $err;
			// duplicate entry exception
			$err->getMessage();
			echo $err;
		}
	}

	/**
		Get Data 
	*/
	public function getData() {
		$query = "
			SELECT * FROM course_data
			LEFT JOIN coursedetails USING (id)
		";

		try {
			$result = mysql_query($query);
		}
		catch (MySQLException $err) {
		    $err->getMessage();
			echo $err;
		}
		
		$data = array();

		while ($row = mysql_fetch_assoc($result)) {
			array_push($data, $row);
		}

		return $data;
	}

	public function getNewClasses() {
		$todayArr = getDate();
		$days = $GLOBALS['newClassDuration'];
		$start = sprintf("%4d-%02d-%02d", $todayArr['year'], $todayArr['mon'], $todayArr['mday'] - $days);

		$sql="select * from course_meta join course_data on course_meta.cid = course_data.id where course_meta.date >= {$start}";

		$err = null;
		$results = $this->executeQuery($sql, &$err);
		$data = array();
		while ($row = mysql_fetch_assoc($results)) {
			array_push($data, $row);
		}
		
		return $data;
		
	}

	public function getClassID($course) {
		$sql = "select id from course_data where title='" . $course->getTitle() . "'";
		$err = null;
		$results = $this->executeQuery($sql, &$err);
		
		$values = mysql_fetch_assoc($results);
		$id = $values['id'];
		
		return ($id == null) ? null : $id;
		
	}
	
	public function getClassesIDs() {
		$sql = "select id from course_data";
		$err = null;
		$results = $this->executeQuery($sql, &$err);
		$ids = array();
		while ($row = mysql_fetch_assoc($results)) {
			array_push($ids, $row['id']);
		}
		
		return $ids;
	}
	
	public function clearOldClasses($ids) {
		foreach ($ids as $id) {
			$err = null;
			$sql1 = "delete from course_data where id='" . $id . "'";
			$sql2 = "delete from coursedetails where id='" . $id . "'";
			$sql3 = "delete from course_meta where cid='" . $id . "'";
			$this->executeQuery($sql1, &$err);
			$this->executeQuery($sql2, &$err);
		}
	}
	
	public function updateMetadata($class) {
		$todayArr = getDate();
		$date = sprintf("%4d-%02d-%02d", $todayArr['year'], $todayArr['mon'], $todayArr['mday']);
		$err = null;
		$classId = $this->getClassID($class);
		$insertSql = "insert into course_meta (`cid`, `date`) values ('" . $classId . "', '" . $date . "')";
		
		print "Date is: {$date}\nclass id is: {$classId}\nsql is: ${insertSql}\n";
		
		$this->executeQuery($insertSql, &$err);
	}
	/**
		Get Type Ahead Data 
	*/
	public function getTypeAheadData() {
		$query = "
			SELECT title FROM course_data
		";

		try {			
			$result = mysql_query($query);
		}
		catch (MySQLException $err) {
		    $err->getMessage();
			echo $err;
		}

		$data = array();

		while ($row = mysql_fetch_assoc($result)) {
			array_push($data, $row['title']);
		}

		return $data;
	}

	public function searchForClasses ($param) {
		$search_filter_array = array();

		if ($param['input_search']) {
			$input_search = mysql_real_escape_string($param['input_search']);
			array_push($search_filter_array
				, sprintf(
					"(MATCH (title, short_desc, long_desc)
						AGAINST ('%s')
					OR MATCH (coursedetails.profname)
						AGAINST ('%s'))"
					, $input_search
					, $input_search
					)
				);
		}

		if ($param['category']) {
			$category = mysql_real_escape_string($param['category']);
			array_push($search_filter_array, "(category = '{$category}')");
		}

		if ($param['site']) {
			$site = mysql_real_escape_string($param['site']);
			array_push($search_filter_array, "(site = '{$site}')");
		}

		if (count($search_filter_array)) {
			$search_filter = "WHERE" . join("AND", $search_filter_array);
		}

		$query = sprintf(
			"SELECT * FROM course_data
			LEFT JOIN coursedetails USING (id)
			%s
			"
			, $search_filter
			);

		try {
			$result = mysql_query($query);
		}
		catch (MySQLException $err) {
		    $err->getMessage();
			echo $err;
		}
		
		$data = array();

		while ($row = mysql_fetch_assoc($result)) {
			array_push($data, $row);
		}

		return $data;
	}

	public function getFeaturedClasses () {
		$data = array();

		return $data;
	}

	public function getClass ($param) {
		if (! array_key_exists('class_id', $param)) {
			return NULL;
		}

		$unsafe_class_id = $param['class_id'];
		$class_id = mysql_real_escape_string($unsafe_class_id);

		$query = "
			SELECT * FROM course_data
			LEFT JOIN coursedetails USING (id)
			WHERE id = $class_id
		";

		try {
			$result = mysql_query($query);
		}
		catch (MySQLException $err) {
		    $err->getMessage();
			echo $err;
		}
		
		$data = array();

		while ($row = mysql_fetch_assoc($result)) {
			array_push($data, $row);
		}

		return $data;
	}

	public function getSiteList ($param) {
		$query = "
			SELECT DISTINCT site FROM course_data
		";

		try {
			$result = mysql_query($query);
		}
		catch (MySQLException $err) {
		    $err->getMessage();
			echo $err;
		}
		
		$data = array();

		while ($row = mysql_fetch_assoc($result)) {
			array_push($data, $row['site']);
		}

		return $data;
	}

	public function getCategoryList ($param) {
		$query = "
			SELECT DISTINCT category FROM course_data
		";

		try {
			$result = mysql_query($query);
		}
		catch (MySQLException $err) {
		    $err->getMessage();
			echo $err;
		}
		
		$data = array();

		while ($row = mysql_fetch_assoc($result)) {
			array_push($data, $row['category']);
		}

		return $data;
	}
}
?>
