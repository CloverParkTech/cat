<?php

// include the class tables function





// first pull the welcome message from the homepage

// load the homepage
$homepage = node_load(23);


// display homepage welcome content
echo "<div class=\"print-page\">";
echo "<p>";
echo $homepage->body[$node->language][0]['value'];
echo "</p>";
echo "</div>";



// degrees and certificates












echo "<h1>Degrees & Certificates</h1>";
    $query = new EntityFieldQuery();
    $query->entityCondition('entity_type', 'node')
        ->entityCondition('bundle', 'degree_or_certificate')
        ->propertyCondition('status', 1);
        $result = $query->execute();
        if (!empty($result['node'])) {
          $nids = array_keys($result['node']);
          $nodes = node_load_multiple($nids);
          foreach($nodes as $node) {
            echo "<div class=\"print-page\">";
            echo "<h2>";
            echo $node->title;
            echo "</h2>";          

            if (isset($node->field_quarters[$node->language][0]['safe_value'])) {
              echo "<p>";
              echo "<strong>Estimated number of quarters:</strong> ";
              echo $node->field_quarters[$node->language][0]['safe_value'];
              echo "</p>";
            }

            if(isset($node->field_estimated_cost[$node->language][0]['safe_value'])) {
              echo "<p>";
              echo "<strong>Estimated cost:</strong> "; 
              echo $node->field_estimated_cost[$node->language][0]['safe_value'];
              echo "</p>";
            }

            if (isset($node->field_admission_dates[$node->language][0]['safe_value'])) {
              echo "<p>";
              echo "<strong>Admission dates:</strong> "; 
              echo $node->field_admission_dates[$node->language][0]['safe_value'];
              echo "</p>";
            }

            if (isset($node->field_degree_prereqs[$node->language][0]['safe_value'])) {
              echo "<p>";
              echo "<strong>Prerquisites:</strong> "; 
              echo $node->field_degree_prereqs[$node->language][0]['safe_value'];
              echo "</p>";
            }

            if (isset($node->body[$node->language][0]['safe_value'])) {
              echo "<p>";
              echo $node->body[$node->language][0]['safe_value'];
              echo "</p>";
            }

            boo_function('classes_output.php');
            boo_function('degree_table_display.php');
            degree_table_display($node);
            echo "</div>";
          }
        }


echo "<h1>Course Descriptions</h1>";
boo_function('class_descriptions.php');
$vid = 2;         
$terms = taxonomy_get_tree($vid);    
foreach ( $terms as $term ) { 
  echo "<div class=\"print-page\">";
  echo "<h2>";
  echo $term->name;
  echo "</h2>";
  class_descriptions($term->tid);
  echo "</div>";
}


echo "<h1>Academic Information</h1>";

    $node = node_load(23);
    $field = field_get_items('node', $node, 'field_academic_information_toc');
    foreach($field as $item) {
      echo "<div class=\"print-page\">";
      $node = node_load($item['target_id']);
     // print_r($node);
      echo "<h2>";
      echo $node->title;
      echo "</h2>";
      echo "<p>";
      echo $node->body[$node->language][0]['safe_value'];
      echo "</p>";
      echo "</div>";
    }

    



?>

