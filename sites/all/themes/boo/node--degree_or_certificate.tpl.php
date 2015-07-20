<?php

/**
 * @file
 * This is the degree and certificate template.
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
  $navnids = taxonomy_select_nodes($navtid, false, false);

// get the path for the program taxonomy term in question, so that we can link to all the class descriptions is this category.
  $current_cat = taxonomy_term_load($navtid);
  $path = taxonomy_term_uri($current_cat);
  $caturl = url($path['path']);





// conver the degree type fields into their proper names.

$fielddegree = field_get_items('node', $node, 'field_degree_type_new'); 
$degree_type_id = null;
foreach($fielddegree as $fielddegree_item) {
  $degree_type_id .= $fielddegree_item['value'];
}

/*
* 1 = aat
* 2 = aas-t  
* 12 = both aat and aast
* 3 = certificate
* 4 = dta/mrp
* 5 = bachelor's of science
*/
$degree_type_value = null;
switch($degree_type_id) {
  case 1:
    $degree_type_value = "AAT Degree";
    break;
  case 2:
    $degree_type_value = "AAS-T Degree";
    break;
  case 12:
    $degree_type_value = "AAT and AAS-T Degree";
    break;
  case 3:
    $degree_type_value = "Certificate";
    break;
  case 4:
    $degree_type_value = "DTA/MRP Degree";
    break;
  case 5:
    $degree_type_value = "Bachelor of Applied Science Degree";
    break;  
}
?>


<h4><?php echo $degree_type_value; ?></h4>


  <div class="left-col">
     <div class="glance-wrapper">
      <h5>Degree Info at a Glance</h5>
      <dl>
        <?php if(isset($degree_type_value)): ?>
          <dt>Type</dt>
          <dd>
            <?php
              echo $degree_type_value;
            ?>
          </dd>
        <?php endif; ?>
        <?php if(isset($content['field_quarters'])): ?>
          <dt>Estimated # of Quarters</dt>
          <dd><?php print render($content['field_quarters']); ?></dd>
        <?php endif; ?>
        <?php if(isset($content['field_estimated_cost'])): ?>  
          <dt>Estimated Cost</dt>
          <dd><?php print render($content['field_estimated_cost']); ?></dd>
         <?php endif; ?>  
        <?php if(isset($content['field_admission_dates'])): ?>   
          <dt>Admission Dates</dt>
          <dd><?php print render($content['field_admission_dates']); ?></dd>
        <?php endif; ?>
        <?php if(isset($content['field_degree_prereqs'])): ?>    
          <dt>Prerequisites</dt>
          <dd><?php print render($content['field_degree_prereqs']); ?></dd>
         <?php endif; ?> 
      </dl>

    </div>
  <div class="degree-body">
    <?php 
  $body = field_get_items('node', $node, 'body');
  print_r($body[0]['value']); 
    ; ?>
 </div>

<?php
// output tables

$node = node_load($nid);
// set the index counter that we'll use for the classes output function
// sort of hacky. Shouldn't be using a global variable here.
$i = 0;

// give this function the name of an entity reference field that contains classes and elective clusters
// it outputs the whole table and popup boxes

  
  boo_function('degree_table_display.php');
  degree_table_display($node);
?>
</div>


<div class="right-col">
<?php boo_snippet('search.php'); 

$q = 0;
  foreach($navnids as $navnid) {

    // display related degrees and certificates
  // check if node's content type is degree or certificate
    $navnode = node_load($navnid);
    $type =$navnode->type;
    // check if node is the current node we're on
    
    if($type == 'degree_or_certificate' && $navnid !== $nid) {
      if($q == 0) {
        echo "
        <h3>Related Degrees & Certificates</h3>
        <ul>";
      }
      echo "<li>";
      echo "<a href=\"";
      echo boo_url($navnid);
      echo "\">";
      echo $navnode->title;
      echo "</a>";
      echo "</li>";
      $q++;
    }
  }
  ?>
  <li>
    <a href="<?php echo $caturl;?>">View All <?php echo $current_cat->name; ?> Classes</a>
  </li>
</ul>


<?php boo_snippet('lead-form.php'); ?>
<?php boo_snippet('sidebar-menu.php'); ?>
  </div>
</div>

<div class="datestamp-wrapper">
This page was last updated on <?php echo date("F d, Y", $node->revision_timestamp); ?>.
</div>


