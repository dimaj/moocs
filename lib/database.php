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
		$this->clearTables();
	}

	/**
		Clears tables from selected database
	*/	
	private function clearTables() {
		// clear table data
		mysql_query("TRUNCATE TABLE course_data");
		mysql_query("TRUNCATE TABLE coursedetails");
	}
	
	/**
		Connects to the database and selects a database
	*/
	private function connectDB() {
		// connect to database
		$this->conn = @mysql_connect($GLOBALS['db']['host'], $GLOBALS['db']['user'], $GLOBALS['db']['pass'])
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
		$query .= "DEFAULT,'" . mysql_real_escape_string($class["title"]) . "'";
		$query .= ",'" . mysql_real_escape_string($class["shortDsc"]) . "'";
		$query .= ",'" . mysql_real_escape_string($class["longDesc"]) . "'";
		$query .= ",'" . mysql_real_escape_string($class["link"]) . "'";
		$query .= ",'" . mysql_real_escape_string($class["video_link"]) . "'";
		$query .= ",'" . mysql_real_escape_string($class["startDate"]) . "'";
		$query .= ",'" . mysql_real_escape_string($class["duration"]) . "'";
		$query .= ",'" . mysql_real_escape_string($class["classImageURL"]) . "'";
		$query .= ",'" . mysql_real_escape_string($class["category"]) . "'";
		$query .= ",'" . mysql_real_escape_string($class["site"]) . "'";
		$query .= ")";
		
		return $query;
	}
	
	/**
		Creates a query string to update coursedetails table
	*/
	private function constructCorseDetailsQuery($class) {
		$query = "INSERT INTO coursedetails VALUES ";
		$query .= "(";
		$query .= "DEFAULT,'" . mysql_real_escape_string($class['profName']) . "'";
		$query .= ",'" . mysql_real_escape_string($class['profImage']) . "'";
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
}
?>
