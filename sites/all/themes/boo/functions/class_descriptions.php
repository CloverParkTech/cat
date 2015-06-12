<?php
// function that takes a taxonomy ID and outputs an alphabetized and formatted list of classes
function class_descriptions($tid) {
  $program_nodes = node_load_multiple(taxonomy_select_nodes($tid, false, false));
    if(!function_exists('cmp')) {
    function cmp($a, $b) {
        return strcmp($a->title, $b->title);
      }
  }
    usort($program_nodes, "cmp");


  $i = 0;
  foreach($program_nodes as $class) {
    if ($class->type == 'class') {
      echo "<h4 class=\"class-title\">";
      echo $class->field_class_title['und'][0]['safe_value'];
      echo "</h4>";
      echo "<div class=\"class-wrapper\">";
      
      echo "<dl><dt>Item Number</dt><dd>";
      echo $class->title;
      echo "</dd><dt>Credits</dt>";
      echo "<dd>";
      echo $class->field_credits['und'][0]['value'];
      echo "</dd></dl>";
      echo "<p>";
      // definitely a better way to access safe field values
      echo $class->field_description['und'][0]['value'];
      echo "</p>";
      if (isset($class->field_course_outcomes['und'][0]['value'])) {
      echo "<span class=\"course-outcome-title js-opener\" id=\"js-opener-";
      echo $i;
      echo "\">";
      echo "View Course Outcomes";
      echo "</span>";
       echo "<span class=\"course-outcome-text\" id=\"js-window-";
      echo $i;
      echo "\">";
      echo "<p>";
      echo $class->field_course_outcomes['und'][0]['value'];
      echo "</p>";
      echo "</span>";
    }
      
      echo "</div>";
      $i++;
      
    }





  }

  if($i == 0) {
      echo "<h5>No classes found</h5>";
    }
}

?>