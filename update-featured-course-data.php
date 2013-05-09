<?php
require_once("config/config.php");
require_once("lib/database.php");

$database = new Database();

$status = 0;
$messages = array();
$data = $database->updateFeaturedClass($_REQUEST);

$object = array(
	'api_version' => $API_VERSION
	, 'status' => $status
	, 'messages' =>  $messages
	, 'data' => $data
	);

print json_encode($object);
?>