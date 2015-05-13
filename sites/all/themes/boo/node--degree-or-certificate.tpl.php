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
?>
<div id="node-<?php print $node->nid; ?>" class="<?php print $classes; ?> clearfix"<?php print $attributes; ?>>


  <?php print render($title_prefix); ?>
  <?php if (!$page): ?>
    <h1<?php print $title_attributes; ?>><a href="<?php print $node_url; ?>"><?php print $title; ?></a></h1>
  <?php endif; ?>
  <?php print render($title_suffix); ?>

  <?php if ($display_submitted): ?>
    <div class="submitted">
      <?php print $submitted; ?>
    </div>
  <?php endif; ?>


  <nav>
  <h3>Related Degrees & Certificates</h3>

  <ul>
  <?php 

  // lists all degrees & certificates in the same program, as determined by their taxonomy.
  $items = field_get_items('node', $node, 'field_degree_program');
  $navtid = $items[0]['tid'];
  // returns node IDs for all nodes with the same program taxonomy term as this one
  $navnids = taxonomy_select_nodes($navtid);
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



  


  // display node's title and url in nav



  }
  ?>

</ul>



  </nav>

  <div class="content"<?php print $content_attributes; ?>>
  <div class="body">
    <?php print render($content['body']); ?>
 </div>

 <div class="glance">
<?php print render($content['field_type_of_degree']); ?>
<?php print render($content['field_quarters']); ?>
<?php print render($content['field_estimated_cost']); ?>
<?php print render($content['field_admission_dates']); ?>
<?php print render($content['field_degree_prereqs']); ?>
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
    $field = field_get_items('node', $node, 'field_another_entity_test');
    $total_credits = 0;
    $total_credits_max = 0;
    foreach($field as $item) {


    $class = $item['entity'];
   
   // print_r($class);
    echo "<tr>";
    echo "<td>";
    echo $class->field_item_number['und'][0]['value'];
    echo "</td>";
    // there's a better way to access these. Once I have that, make this a function
    echo "<td>";
    echo $class->title;
    
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
    <?php
      // We hide the comments and links now so that we can render them later.
     // hide($content['comments']);
     // hide($content['links']);
     // print render($content);
    ?>
  </div>


</div>
