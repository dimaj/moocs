<?php

/* 
 	This is a configuration file for the project that
 	tells main project which scrapers to include, database info
 	and other global settings
 */

$GLOBALS['db'] = array(
	"host" => "ip of the server, where mysql is installed",
	"user" => "username that has access to the database",
	"pass" => "password for that user",
	"db" => "name of the database, where data is stored"
);

$GLOBALS['scrapers'] = array();

$GLOBALS['scrapers']['canvas'] = array(
	"className" => "Canvas",
	"mainURL" => "https://www.canvas.net",
	"mainSearchString" => "div[class=block-box no-pad featured-course-outer-container]",
	"name" => "Canvas"
);

$GLOBALS['scrapers']['udacity']  = array(
	"className" => "Udacity",
	"mainURL" => "https://www.udacity.com/courses",
	"mainSearchString" => "li data-ng-show=isCourseShown(\'[^A-Za-z0-9]\')",
	"name" => "Udacity"
);

print "There are " . count($GLOBALS['scrapers']) . " scrapers found\n";