<?php

// give this the name of the field, the node object, an index counter set to 0, and whether we want the popup boxes

  function boo_classes_output($field_name, $pagenode, $popups = true) {
  // set the variables used throughout as counters
  $total_credits = 0;
  $max_credits = 0;
  $max_credits_total = 0;
  global $i;

  // set the array that we'll use to store all the data from classes
  $classes = array();
  $field = field_get_items('node', $pagenode, $field_name);
 // print_r($field);

  foreach($field as $item) {
    $class_id = $item['target_id'];
    
    $subnode = node_load($class_id);
   
  


    if($subnode->type == 'elective_cluster') {
      $classes[$i]['index'] = $i;
      $classes[$i]['item'] = null;
      $classes[$i]['title'] = $subnode->title;
      $classes[$i]['credits'] = $subnode->field_total_credits[$pagenode->language][0]['value'];
      $classes[$i]['superscript'] = null;
    
      // create the sub array of classes assigned to the elective cluster
      $j = 0;
      
      foreach($subnode->field_elective_group[$pagenode->language] as $sub_class) {
        // load the fields associated with the field group
        $entity = entity_load('field_collection_item', array($sub_class['value']));
        
          $k = 0;
          foreach($entity as $sub_entity) {
            if (isset($sub_entity->field_description_sub_elective[$pagenode->language][0]['safe_value'])) {
              $classes[$i]['sub_elective_group'][$j]['description'] = $sub_entity->field_description_sub_elective[$pagenode->language][0]['safe_value'];
            }
              
               // load each of the class nodes listed in this elective group
               
              foreach($sub_entity->field_courses_sub as $sub_courses) {
                foreach($sub_courses as $sub_course) {
                  $sub_course_nid = $sub_course['target_id'];
                   $classes[$i]['sub_elective_group'][$j]['sub_courses'][$k]['nid'] = $sub_course_nid;
                 $sub_course_node = node_load($sub_course_nid);



                 //  $sub_course_node = $node_load($sub_course['target_id']);

                  if (isset($sub_course_node->title)) {
                    $classes[$i]['sub_elective_group'][$j]['sub_courses'][$k]['item'] = $sub_course_node->title;
                  }
                  if (isset($sub_course_node->field_credits[$pagenode->language][0]['value'])) {
                    $classes[$i]['sub_elective_group'][$j]['sub_courses'][$k]['credits'] = $sub_course_node->field_credits[$pagenode->language][0]['value'];
                  }
                  if (isset($sub_course_node->field_credit_maximum[$pagenode->language][0]['value'])) {
                   $classes[$i]['sub_elective_group'][$j]['sub_courses'][$k]['creditsmax'] = $sub_course_node->field_credit_maximum[$pagenode->language][0]['value'];
                  }
                  if (isset($sub_course_node->field_class_title[$pagenode->language][0]['value'])) {
                   $classes[$i]['sub_elective_group'][$j]['sub_courses'][$k]['title'] = $sub_course_node->field_class_title[$pagenode->language][0]['value'];
                 }
                   // set superscript value
                    if ($sub_course_node->field_capstone[$pagenode->language][0]['value'] == 1) {
                      $classes[$i]['sub_elective_group'][$j]['sub_courses'][$k]['superscript'] = "<sup class=\"tooltip\" data-hover=\"CAP designates that this course meets the capstone requirement.\">CAP</sup>";
                    }
                    elseif ($sub_course_node->field_computer_literacy[$pagenode->language][0]['value'] == 1) {
                      $classes[$i]['sub_elective_group'][$j]['sub_courses'][$k]['superscript'] = "<sup class=\"tooltip\" data-hover=\"CL designates that this course meets the computer literacy requirement.\">CL</sup>";
                    }
                    elseif ($sub_course_node->field_diversity_requirement[$pagenode->language][0]['value'] == 1) {
                      $classes[$i]['sub_elective_group'][$j]['sub_courses'][$k]['superscript'] = "<sup class=\"tooltip\" data-hover=\"DIV designates that this course meets the diversity requirement.\">DIV</sup>";
                    }
                    else {
                      $classes[$i]['sub_elective_group'][$j]['sub_courses'][$k]['superscript'] = null;
                    }
                            


                  $k++;
                }
              }
              $j++;


        }
      }
    }

    

    if($subnode->type == 'class') {
      $classes[$i]['index'] = $i;
      $classes[$i]['item'] = $subnode->title;
      $classes[$i]['title'] = $subnode->field_class_title[$pagenode->language][0]['value'];
      $classes[$i]['credits']= $subnode->field_credits[$pagenode->language][0]['value'];
      $classes[$i]['description'] = $subnode->field_description[$pagenode->language][0]['safe_value'];
      if (isset($subnode->field_credit_maximum[$pagenode->language][0]['value'])) {
        $classes[$i]['creditsmax'] = $subnode->field_credit_maximum[$pagenode->language][0]['value'];
      }

      // set superscript value
      if ($subnode->field_capstone[$pagenode->language][0]['value'] == 1) {
        $classes[$i]['superscript'] = "<sup class=\"tooltip\" data-hover=\"CAP designates that this course meets the capstone requirement.\">CAP</sup>";
      }
      elseif ($subnode->field_computer_literacy[$pagenode->language][0]['value'] == 1) {
        $classes[$i]['superscript'] = "<sup class=\"tooltip\" data-hover=\"CL designates that this course meets the computer literacy requirement.\">CL</sup>";
      }
      elseif ($subnode->field_diversity_requirement[$pagenode->language][0]['value'] == 1) {
        $classes[$i]['superscript'] = "<sup class=\"tooltip\" data-hover=\"DIV designates that this course meets the diversity requirement.\">DIV</sup>";
      }
      else {
        $classes[$i]['superscript'] = null;
      }

    }
    $i++;
  
  }



// use our global counter to determine credit totals and if we should display the total technical credits
  echo "<table class=\"degree-table\"><thead><th>Course Number</th><th>Class Title</th><th>Credits</th></thead>";
    global $a;
    global $technical_credits;
    global $max_credits_total;
    if ($a == 0) {
        $technical_credits = $total_credits;
      }
    if ($a > 0) {
      echo "<tr><td>&nbsp;</td><td>";
      echo "Technical Course Requirements";
      echo "</td><td>";
      echo $technical_credits;
      if($max_credits_total > $technical_credits) {
        echo "-";
        echo $max_credits_total;
      }
      echo "</td></tr>";
    }
    // output start of table 
    


    foreach($classes as $class_item) {
      echo "<tr class=\"class-popup\" id=\"js-class-popup-";
      echo $class_item['index'];
      echo "\">";
      echo "<td>";
      if(isset($class_item['item'])) {
        echo $class_item['item'];
      }
        if($class_item['superscript']) {
     
          echo $class_item['superscript'];
    
        }
      echo "</td>";
      echo "<td>";
      if (isset($class_item['title'])) {
        echo $class_item['title'];
      }
      echo "</td>";
      echo "<td>";
      if (isset($class_item['credits'])) {
        echo $class_item['credits'];
      }
      if(isset($class_item['creditsmax'])) {
          echo "-";
          echo $class_item['creditsmax'];
        }
      echo "</td>";
      echo "</tr>";
      $total_credits += $class_item['credits'];
      if (isset($class_item['creditsmax'])) {
        $max_credits += $class_item['creditsmax'];
      }

    }

     echo "<tr><td>&nbsp;</td><td>";
     
     

      if ($a == 0) {
        echo "Technical Credits";
      }
      else {
        echo "Total Credits";
      }
      echo "</td><td>";
      if ($a == 0) {
        echo $total_credits;
        if($max_credits > 0) {
          echo "-";
          $max_credits_total = $total_credits + $max_credits;
          echo $max_credits_total;
        }
      }
      if ($a > 0) {
        echo $total_credits + $technical_credits;
        if ($max_credits_total > 0) {
          echo "-";
          echo $max_credits_total + $technical_credits;
        }


      }
      // set the technical credits value if this is our first time through.
      if ($a == 0) {
        $technical_credits = $total_credits;
      }
      $a++;
       echo "</td></tr>";
  echo "</table>";

  // Now for the popup divs 
  if($popups == true) {

    foreach($classes as $class_item) {
      echo "<div class=\"class-popup-window\" id=\"js-class-popup-window-";
      echo $class_item['index'];
      echo "\">";
      echo "<div class=\"class-popup-window-inner\">";
      echo "<h4 class=\"class-title\">";
      if (isset($class_item['title'])) {
        echo $class_item['title'];
      }
      echo "</h4>";
      echo "<div class=\"class-popup-wrapper\">";
      echo "<dl>";
      if(isset($class_item['item'])) {
        echo "<dt>Item #</dt>";
        echo "<dd>";
        echo $class_item['item'];
      }  
      if (isset($class_item['superscript'])) {
        echo $class_item['superscript'];

      }
      echo "</dd>";
      echo "<dt>Total Credits</dt>";
      echo "<dd>";
      echo $class_item['credits'];
      if(isset($class_item['creditsmax'])) {
        echo "-";
        echo $class_item['creditsmax'];
      }
      echo "</dd>";
      echo "</dl>";


      echo "<p>";
      if (isset($class_item['description'])) {
        echo $class_item['description'];
      }
      echo "</p>";
      echo "<div class=\"popup-tables-wrapper\">";
      // output descriptions and tables for electives

      if(isset($class_item['sub_elective_group'])) {
        //count the number of tables in this array. If there are more than two, we apply the small-table class
        $count = count($class_item['sub_elective_group']);
        foreach ($class_item['sub_elective_group'] as $sub_sub_elective_group) {
          echo "<div class=\"popup-table-item\">";
          echo "<h5>";
          if (isset($sub_sub_elective_group['description'])) {
            echo $sub_sub_elective_group['description'];
          }
          echo "</h5>";
            echo "<table class=\"degree-table";
            if($count > 2) {
              echo " table-small";
            }
            echo "\">";
            foreach($sub_sub_elective_group['sub_courses'] as $sub_sub_courses) {
              echo "<tr>";
              echo "<td>";
                echo $sub_sub_courses['item'];
                if($sub_sub_courses['superscript']) {
                  echo $sub_sub_courses['superscript'];
                }
                echo "</td>";
                echo "<td>";
                echo $sub_sub_courses['title'];
                echo "</td>";
                echo "<td>";
                if (isset($sub_sub_courses['credits'])) {
                  echo $sub_sub_courses['credits'];
                }
                if(isset($sub_sub_courses['creditsmax'])) {
                   echo "-";
                  echo $sub_sub_courses['creditsmax'];
                 }
                echo "</td>";
                
              echo "</tr>";
            }
            echo "</table>";
            echo "</div>";

        }
      }
      echo "</div>";
        
      echo "</div>";
      echo "<div class=\"class-popup-window-close\" id=\"js-popup-window-close-";
      echo $class_item['index'];
      echo "\">";
      echo "CLOSE";
      echo "</div></div></div>";

  }
}
}

