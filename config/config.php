<?php

/* 
 	This is a configuration file for the project that
 	tells main project which scrapers to include, database info
 	and other global settings
 */

$GLOBALS['db'] = array(
	"host" => "localhost"
	, "port" => "3306"
	, "user" => "cs160"
	, "pass" => "cs160_password"
	, "db" => "cs160"
	, "configFile" => "config/moocs.sql"
);

$GLOBALS['scrapers'] = array();

$GLOBALS['scrapers']['canvas'] = array(
	"className" => "Canvas"
	, "mainURL" => "https://www.canvas.net"
	, "mainSearchString" => "div[class=block-box no-pad featured-course-outer-container]"
	, "name" => "Canvas"
);

$GLOBALS['scrapers']['udacity']  = array(
	"className" => "Udacity"
	, "mainURL" => "https://www.udacity.com/courses"
	, "mainSearchString" => "ul[id=unfiltered-class-list] li"
	, "name" => "Udacity"
);
