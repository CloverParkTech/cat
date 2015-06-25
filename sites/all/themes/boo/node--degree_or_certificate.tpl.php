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



  // set $degree_type variable depending on what degrees are selected
  //aat
  $fieldaat = field_get_items('node', $node, 'field_degree_type'); 
  $aat = $fieldaat[0]['value'];
  //aas-t
  $fieldaast = field_get_items('node', $node, 'field_aas_t_degree');     
  $aast = $fieldaast[0]['value'];

  //certificate
  $fieldcert = field_get_items('node', $node, 'field_certificate'); 
  $cert = $fieldcert[0]['value'];
  
  if($aat == 1 && $aast == 1) {
    $degree_type = 1;
    }
  elseif($aat == 1) {
    $degree_type = 2;
    }
  elseif($aast == 1) {
    $degree_type = 3;
    }
  elseif($cert == 1) {
    $degree_type = 4;
    }
  else {
    $degree_type = null;
    }
?>


  <div class="left-col">
     <div class="glance-wrapper">
      <h5>Degree Info at a Glance</h5>
      <dl>
        <?php if(isset($degree_type)): ?>
          <dt>Type</dt>
          <dd>
            <?php
            if($degree_type == 1) {
              echo "AAT and AAS-T Degree";
            }
            if($degree_type == 2) {
              echo "AAT Degree";
            }
            if($degree_type == 3) {
              echo "AAS-T Degree";
            }
            if($degree_type == 4) {
              echo "Certificate";
            }
    
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
    <?php print render($content['body']); ?>
 </div>

<?php
// output tables

$node = node_load($nid);
// set the index counter that we'll use for the classes output function
// sort of hacky. Shouldn't be using a global variable here.
$i = 0;

// give this function the name of an entity reference field that contains classes and elective clusters
// it outputs the whole table and popup boxes

  boo_function('classes_output.php');
  boo_function('degree_table_display.php');
  degree_table_display($node);
?>
</div>


<div class="right-col">
<?php boo_snippet('search.php'); 
  foreach($navnids as $navnid) {

    // display related degrees and certificates
  // check if node's content type is degree or certificate
    $navnode = node_load($navnid);
    $type =$navnode->type;
    // check if node is the current node we're on
    $q = 0;
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


