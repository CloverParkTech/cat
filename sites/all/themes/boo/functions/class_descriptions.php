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

      // superscript
      if ($class->field_capstone[$class->language][0]['value'] == 1) {
            echo "<sup class=\"tooltip\" data-hover=\"CAP designates that this course meets the capstone requirement.\">CAP</sup>";
          }
      elseif ($class->field_computer_literacy[$class->language][0]['value'] == 1) {
            echo "<sup class=\"tooltip\" data-hover=\"CL designates that this course meets the computer literacy requirement.\">CL</sup>";
          }
      elseif ($class->field_diversity_requirement[$class->language][0]['value'] == 1) {
            echo "<sup class=\"tooltip\" data-hover=\"DIV designates that this course meets the diversity requirement.\">DIV</sup>";
          }
                   




      echo "</dd><dt>Credits</dt>";
      echo "<dd>";
      echo $class->field_credits['und'][0]['value'];
      echo "</dd></dl>";
      echo "<p>";
      // definitely a better way to access safe field values
      echo $class->field_description['und'][0]['value'];
      echo "</p>";
      if (isset($class->field_prerequisites['und'][0]['value'])) {
        echo "<h5>Prerequisites</h5>";
        echo "<p>";
        echo  $class->field_prerequisites['und'][0]['value'];
        echo "</p>";
      }

      if (isset($class->field_co_requisites['und'][0]['value'])) {
        echo "<h5>Co-requisites</h5>";
        echo "<p>";
        echo  $class->field_co_requisites['und'][0]['value'];
        echo "</p>";
      }

      if (isset($class->field_optional_notes['und'][0]['value'])) {
        echo "<h5>Notes</h5>";
        echo "<p>";
        echo  $class->field_optional_notes['und'][0]['value'];
        echo "</p>";
      }


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