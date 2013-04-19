<?php
/* File   : database.php
   Subject: CS160 MOOCs mashup
   Authors: Dmitry Jerusalimsky
   Version: 1.0
   Date   : Apr 12, 2013
   Description: Updates database with latest class information
*/

// require_once("config/config.php");

class Database {
	/**
		Creates an instance of the database class using configuration values from
		config.php file
	*/
	function __construct() {
		$this->checkConfig();
		$this->connectDB();
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
		mysql_select_db($GLOBALS['db']['db'])
			or die ("Could not select database '" . $GLOBALS['db']['db'] . ".\n");
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
		try {			
			mysql_query($query1);
			mysql_query($query2);
		}
		catch (MySQLException $err) {
		    $err->getMessage();
			echo $err;
		}
		catch (MySQLDuplicateKeyException $err) {
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

}
?>
