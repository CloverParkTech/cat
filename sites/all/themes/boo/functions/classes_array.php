 <?php

// creates an array of class data that's used for the class tables on degrees and for the popupwindow
// there's definitely some duplication here that could better be put into functions
function boo_classes_array($field_name, $pagenode, $index) {
  // set the variables used throughout as counters
  $total_credits = 0;
  $max_credits = 0;
  $i = $index;

  // set the array that we'll use to store all the data from classes
  $classes = array();
  $field = field_get_items('node', $pagenode, $field_name);



  foreach($field as $item) {
    $class_id = $item['target_id'];
    
    $subnode = node_load($class_id);
   

    // process for elective clusters
    if($subnode->type == 'elective_cluster') {
      $classes[$i]['index'] = $i;
      $classes[$i]['item'] = null;
      $classes[$i]['title'] = $subnode->title;
      $classes[$i]['credits'] = $subnode->field_total_credits[$pagenode->language][0]['value'];
      $classes[$i]['creditsmax'] = $subnode->field_total_credits_max[$pagenode->language][0]['value'];
      $classes[$i]['superscript'] = null;
      $classes[$i]['description'] = $subnode->field_description_aat_elective[$pagenode->language][0]['value'];
    
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

    
    // process for classes
    if($subnode->type == 'class') {
      $classes[$i]['index'] = $i;
      $classes[$i]['item'] = $subnode->title;
      $classes[$i]['title'] = $subnode->field_class_title[$pagenode->language][0]['value'];
      $classes[$i]['credits']= $subnode->field_credits[$pagenode->language][0]['value'];
      $classes[$i]['description'] = $subnode->field_description[$pagenode->language][0]['value'];
      if (isset($subnode->field_prerequisites[$pagenode->language][0]['value'])) {
         $classes[$i]['prereqs'] = $subnode->field_prerequisites[$pagenode->language][0]['value'];
      }
      if (isset($subnode->field_co_requisites[$pagenode->language][0]['value'])) {
         $classes[$i]['coreqs'] = $subnode->field_co_requisites[$pagenode->language][0]['value'];
      }

       if (isset($subnode->field_optional_notes[$pagenode->language][0]['value'])) {
         $classes[$i]['notes'] = $subnode->field_optional_notes[$pagenode->language][0]['value'];
      }
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
  return $classes;

}
?>