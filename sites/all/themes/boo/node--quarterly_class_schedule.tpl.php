<?php

/**
 * @file
 * Template for the quarterly class schedule
 * This file pulls data from the sms registration system and merges it with class descriptions and program categories from the catalog
 *
 *
 * @ingroup themeable
 */


  //error reporting for dev stage
  ini_set('display_errors', 'On');
  error_reporting(E_ALL);


// include the database connection credentials
include "functions/schedule/db.php";
include "functions/schedule/buildwhere.php";
include "functions/schedule/printcourse.php";
include "functions/schedule/printtop.php";
include "functions/schedule/printbottom.php";
include "functions/schedule/formatdate.php";
include "functions/schedule/printform.php";
include "functions/schedule/printtimeoptions.php";
include "functions/schedule/printcreditsoptions.php";





print render($content['body']); 



// build the where query
$where = buildWhere();

// connect to the schedule database
try {
    $dbh = $dbcred;
  } 
catch(PDOException $e) {
    echo "Access to database denied.";
    die();
}

// connect to the catalog database
try {
    $dbhcat = $dbcatcred;
  } 
catch(PDOException $e) {
    echo "Access to database denied.";
    die();
}


// print the search form
echo '<div class="class-schedule-form">';
printForm($dbh, $dbhcat, $node);
echo '</div>';


// run the MYSQL query to get the combined class schedule data from the database
try {
  $stmt = $dbh->prepare("SELECT * FROM view_catalog " . $where . " ORDER BY field_class_title_value");
  $stmt->execute();
} catch(PDOException $e) {
  echo "Error with SQL.";
}



if ( !empty($_POST) ) {
  //if no results are found, print message. Else, print results.
  $last_row = null;
  if($stmt->rowCount() == 0) {
    echo "<h4>No results found.</h4>";
  } 
  else {
    //prints all courses (to show formatting)
    echo '<h2 class="grey-header">Search Results</h2>';
    // print special message for computer literacy course listing. Check for $_Post['program'] to prevent error message on page load.
    if(isset($_POST['program'])) {
    if($_POST['program'] == 112) {
      echo "<p><strong>The courses listed below are designated as meeting the Computer Literacy requirement as noted in the course descriptions of the current college catalog.</strong></p>";
      }
    if($_POST['program'] == 104) {
      echo "<p><strong>Advanced Learning Program English courses are available under English - Advanced Learning Placement.</strong></p>";
      }
    if($_POST['program'] == 115) {
      echo "<h2>ACCELERATED LEARNING (ALP)</h2>
          <h3>ENG 094 AND ENG 101</h3>
          <h4>What is ALP?</h4>
          <p>The Accelerated Learning Program (ALP) is designed for those who need or want to complete English 94 and English 101 in the same quarter. This program allows for a unique relationship in which students can help their fellow students not only succeed, but also advance in their education. Students pick one of the ENG 101 sections listed below and the ENG 094 section.</p>
      ";
      }   
    }
    while ($row = $stmt->fetchObject()) {
      $last_row = printCourse($row, $last_row, $dbh);
    }
    //close the last row
    printBottom($last_row);
  }
}


?>






