<?php 

// function that takes an array of classes produced by boo_classes_array and returns an array of credits and max credits.
// max credits is difference between credits and max credits.

function boo_credits_sum($classes_array) {

  foreach ($classes_array as $class) {
    $credits += $class['credits'];
    if(isset($class['creditsmax'])) {
      $creditsmax += ($class['creditsmax'] - $class['credits']);
    }
  }
 $credits_array = array($credits, $creditsmax);
 return $credits_array;
}

?>