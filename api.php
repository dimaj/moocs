<?php
require_once("config/config.php");

$DB_VERSION = '0.0.1';

$status = 0;
$messages = array();
$data = array('key' => 'value');

$object = array(
	'database_version' => $DB_VERSION
	, 'status' => $status
	, 'messages' =>  $messages
	, 'data' => $data
	);

print json_encode($object);
?>