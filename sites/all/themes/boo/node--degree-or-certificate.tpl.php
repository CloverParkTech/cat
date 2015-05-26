<?php

/**
 * @file
 * Default theme implementation to display a degree or certificate
 *
 * @see template_preprocess()
 * @see template_preprocess_node()
 * @see template_process()
 *
 * @ingroup themeable
 */



/**
* Let's set up some variables for use throughout the page
**/

 
// get the program taxonomy term for the current degree and use it to get an array of all the nodes in the same taxonomy.
  $items = field_get_items('node', $node, 'field_degree_program');
  $navtid = $items[0]['tid'];
  // returns node IDs for all nodes with the same program taxonomy term as this one
  $navnids = taxonomy_select_nodes($navtid);

// get the path for the program taxonomy term in question, so that we can link to all the class descriptions is this category.
  $current_cat = taxonomy_term_load($navtid);
  $path = taxonomy_term_uri($current_cat);
  $caturl = url($path['path']);
?>

<div class="grid">
  <div class="col17">
     <div class="glance-wrapper">
      <h5>Degree Info at a Glance</h5>
      <?php print render($content['field_type_of_degree']); ?>
      <?php print render($content['field_quarters']); ?>
      <?php print render($content['field_estimated_cost']); ?>
      <?php print render($content['field_admission_dates']); ?>
      <?php print render($content['field_degree_prereqs']); ?>
    </div>
  <div class="degree-body">
    <?php print render($content['body']); ?>
 </div>


  <h2>Classes in This Degree</h2>
  <table>
    <thead>
      <th>Course Number</th>
      <th>Class Title</th>
      <th>Credits</th>
    </thead>
  <?php 
    // display all the courses associated with this degree and their credit values
    $node = node_load($nid);
    $field = field_get_items('node', $node, 'field_classes_in_this_degree');
    $total_credits = 0;
    $total_credits_max = 0;
    // counter for js IDs
    $i = 0;
    foreach($field as $item) {
      $class = $item['entity'];
      echo "<tr>";
      echo "<td>";
      echo $class->title;
      // check for superscripts, e.g. DIV, CAP, etc.
      if ($class->field_capstone['und'][0]['value'] == 1) {
        echo "<sup>CAP</sup>";
      }
      if ($class->field_computer_literacy['und'][0]['value'] == 1) {
        echo "<sup>COM</sup>";
      }
      if ($class->field_diversity_requirement['und'][0]['value'] == 1) {
        echo "<sup>DIV</sup>";
      }
     echo "</td>";
    // there's a better way to access these. Once I have that, make this a function
    // adding IDs for javascript lightbox
    echo "<td>";
    echo "<span data-js=\"js-popup\" class=\"class-popup\" id=\"js-class-popup-";
    echo $i;
    echo "\">";
    echo $class->field_class_title['und'][0]['value'];

    echo "</span>";
        echo "<div class=\"class-popup-window\"  id=\"js-class-popup-window-";
    echo $i;
     echo "\">";
     echo "<div class=\"class-popup-window-inner\">";
     echo "<h2>";
   echo $class->title;
   echo "</h2>";
   echo "<dl>";
   echo "<dt>";
   echo "Item Number";
   echo "</dt>";
   echo "<dd>";
   echo $class->title;
   echo "</dd>";
   echo "<dt>";
   echo "Credits";
   echo "</dt>";
   echo "<dd>";
   echo $class->field_credits['und'][0]['value'];
     // check to see if there's a credit maximum on this class
      if ($class->field_credit_maximum['und'][0]['value']) {
        echo "-";
        echo $class->field_credit_maximum['und'][0]['value'];
        $total_credits_max += $class->field_credit_maximum['und'][0]['value'];
      }
      echo "</dd>";
   echo "<p>";

   echo $class->field_description['und'][0]['value'];
   echo "<a href=\"";


    echo $caturl;
    echo "\">";
    echo "View All"; 




     echo $current_cat->name; 
     echo "Classes";
     echo "</a>";

    echo "<div class=\"class-popup-window-close\" id=\"js-class-popup-window-close-";
    echo $i;
    echo "\">CLOSE</div>";
    echo "</div>";
    echo "</div>";
    
   
    
     echo "</td>";
    echo "<td>";
    echo $class->field_credits['und'][0]['value'];
     // check to see if there's a credit maximum on this class
      if ($class->field_credit_maximum['und'][0]['value']) {
        echo "-";
        echo $class->field_credit_maximum['und'][0]['value'];
        $total_credits_max += $class->field_credit_maximum['und'][0]['value'];
      }

     echo "</td>";
      echo "</tr>";
//    $output = field_view_value('node', $node, 'field_another_entity_test', $field[$delta]);

      // add the credits from this class to the total credits number
      $total_credits += $class->field_credits['und'][0]['value'];
      $i++;
    }

    $node = node_load($nid);
    $field = field_get_items('node', $node, 'field_elective_groups');
    foreach($field as $item) {


    $class = $item['entity'];
   
   // print_r($class);
    echo "<tr>";
    echo "<td>";
  //  echo $class->field_item_number['und'][0]['value'];
    echo "</td>";
    // there's a better way to access these. Once I have that, make this a function
    echo "<td>";
    echo $class->title;
    
     echo "</td>";
    echo "<td>";
    echo $class->field_elective_credits['und'][0]['value'];
     echo "</td>";
      echo "</tr>";
//    $output = field_view_value('node', $node, 'field_another_entity_test', $field[$delta]);

      // add the credits from this class to the total credits number
      $total_credits += $class->field_elective_credits['und'][0]['value'];
    }





    echo "<tr>";
    echo "<td></td>";
    echo "<td>";
    echo "Total Credits";
    echo "</td>";
    echo "<td>";
    echo $total_credits;
    if($total_credits_max !== 0) {
      $total_credits_max = $total_credits + $total_credits_max;
      echo "-";
      echo $total_credits_max;
    }

    echo "</td>";
    echo "</tr>";
  ?>

</table>
  </div>


<div class="col7">
<?php boo_snippet('search.php'); ?>
  <nav>
  <h3>Related Degrees & Certificates</h3>

  <ul>
  <?php 


  foreach($navnids as $navnid) {
  // check if node's content type is degree or certificate
    $navnode = node_load($navnid);
    $type =$navnode->type;
    // check if node is the current node we're on
    if($type == 'degree_or_certificate' && $navnid !== $nid) {
      echo "<li>";
      echo "<a href=\">";
      echo boo_url($navnid);
      echo "\">";
      echo $navnode->title;
      echo "</a>";
      echo "</li>";

    }




  }
  ?>

</ul>

<?php // probably a better way to get the taxonomy name 


?>
  <a href="<?php echo $caturl;?>">View All <?php echo $current_cat->name; ?> Classes</a>


  <?php boo_snippet('sidebar-menu.php'); ?>






  </nav>
</div>
</div>

<div class="datestamp-wrapper">
This page was last updated on <?php echo date("F d, Y", $node->revision_timestamp); ?>.
</div>


</div>
