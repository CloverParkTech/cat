<?php

// credentials are stored in the Drupal settings file so that they're not overwritten when we move from dev to live site

// this is a super hack; document_root isn't properly set on the local server
	$currenturl = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
   $settingspath = $_SERVER['DOCUMENT_ROOT'];
	if (strpos($currenturl,'localhost') !== false) {
    	$settingspath .= "/catalog/sites/default/settings.php";
	} else {
    	$settingspath .= "/sites/default/settings.php";
	}
   include "$settingspath";



$scheduledbname = $schedule_database['database'];
$scheduledbuser = $schedule_database['username'];
$scheduledbpass = $schedule_database['password'];

$catbname = $databases['default']['default']['database'];
$catdbuser = $databases['default']['default']['username'];
$catdbpass = $databases['default']['default']['password'];

$dbcred = new PDO("mysql:host=localhost;dbname=$scheduledbname", $scheduledbuser, $scheduledbpass);
$dbcatcred = new PDO("mysql:host=localhost;dbname=$catbname;", $catdbuser, $catdbpass);
?>