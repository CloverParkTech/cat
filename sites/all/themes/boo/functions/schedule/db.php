<?php

// credentials are stored in the Drupal settings file so that they're not overwritten when we move from dev to live site
   $settingspath = $_SERVER['DOCUMENT_ROOT'];
   $settingspath .= "/sites/default/settings.php";
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