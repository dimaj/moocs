<?php
require_once("config/config.php");
require_once("lib/database.php");

$API_VERSION = '0.0.1';

$database = new Database();

$status = 0;
$messages = array();
$data = $database->getData();
#$data = array('key' => 'value');

$object = array(
	'api_version' => $API_VERSION
	, 'status' => $status
	, 'messages' =>  $messages
	, 'data' => $data
	);

print json_encode($object);
?>