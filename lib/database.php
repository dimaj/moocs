<?php
/* File   : database.php
   Subject: CS160 MOOCs mashup
   Authors: Dmitry Jerusalimsky
   Version: 1.0
   Date   : Apr 12, 2013
   Description: Updates database with latest class information
*/

class Database {
	function __construct($db) {
	
	print_r ($db);
		if ($this->isNullOrEmpty($db['host'])) {
			die ("Hostname is required for database connetion.\nPlease configure it in config/config.php file.\n");
		}
		if ($this->isNullOrEmpty($db['user'])) {
			die ("Username is required for database connetion.\nPlease configure it in config/config.php file.\n");
		}
		if ($this->isNullOrEmpty($db['pass'])) {
			die ("Password is required for database connetion.\nPlease configure it in config/config.php file.\n");
		}
		if ($this->isNullOrEmpty($db['db'])) {
			die ("Database name is required for database connetion.\nPlease configure it in config/config.php file.\n");
		}
		
		// connect to database
		$this->conn = @mysql_connect($db['host'], $db['user'], $db['pass'])
			or die ("Could not connect to host '" . $db['host'] . "'.\n");
		mysql_select_db($db['db'])
			or die ("Could not select database '" . $db['db'] . ".\n");
			
		// clear table data
		mysql_query("TRUNCATE TABLE course_data");
		mysql_query("TRUNCATE TABLE coursedetails");
	}

	private function isNullOrEmpty($str) {
		return (!$str || strlen(trim($str)) === 0);
	}
	
	function updateClasses($classes) {
		foreach ($classes as $class) {
			$query1 = "INSERT INTO course_data VALUES ";
			$query1 .= "(";
			$query1 .= "DEFAULT,'" . mysql_real_escape_string($class["title"]) . "'";
			$query1 .= ",'" . mysql_real_escape_string($class["shortDsc"]) . "'";
			$query1 .= ",'" . mysql_real_escape_string($class["longDesc"]) . "'";
			$query1 .= ",'" . mysql_real_escape_string($class["link"]) . "'";
			$query1 .= ",'" . mysql_real_escape_string($class["video_link"]) . "'";
			$query1 .= ",'" . mysql_real_escape_string($class["startDate"]) . "'";
			$query1 .= ",'" . mysql_real_escape_string($class["duration"]) . "'";
			$query1 .= ",'" . mysql_real_escape_string($class["classImageURL"]) . "'";
			$query1 .= ",'" . mysql_real_escape_string($class["category"]) . "'";
			$query1 .= ",'" . mysql_real_escape_string($class["site"]) . "'";
			$query1 .= ")";

			$query2 = "INSERT INFO coursedetails VALUES ";
			$query2 .= "(";
			$query2 .= "DEFAULT,'" . mysql_real_escape_string($class['profName']) . "'";
			$query2 .= ",'" . mysql_real_escape_string($class['profImage']) . "'";
			$query2 .= ")";
			
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
}
?>
