<?php

/**
 * @file
 * Template for the homepage
 *
 * @see template_preprocess()
 * @see template_preprocess_node()
 * @see template_process()
 *
 * @ingroup themeable
 */
?>

  <div class="left-col">
  <h1>Clover Park Technical College Catalog</h1>
  <?php print render($content['body']); ?>



<h2 class="bar-heading">Academic Offerings</h2>
<?php
// run through all taxonomy terms, list them and their child degrees/certs
$vid = 2;         
  $terms = taxonomy_get_tree($vid);    
 foreach ( $terms as $term ) { 
  $path = taxonomy_term_uri($term);
  $url = url($path['path']);
  echo "<div class='homepage-area-wrapper'>";
  echo "<h3>";
  echo $term->name;
  echo "</h3>";
  echo "<a class='homepage-area-link' href=\"";
  echo $url;
   echo "\">";
   echo "View All ";
  echo $term->name;
  echo " Courses";
  echo "</a>";
// get all the nodes with this tid.
  $degreenids = taxonomy_select_nodes($term->tid, false, false);
  echo "<ul>";
  foreach($degreenids as $degreenid) {

    // display related degrees and certificates
  // check if node's content type is degree or certificate
    $degreenode = node_load($degreenid);
    $type =$degreenode->type;
    // check if node is the current node we're on
    
    if($type == 'degree_or_certificate') {

      echo "<li>";
      echo "<a href=\"";
      echo boo_url($degreenid);
      echo "\">";
      echo $degreenode->title;
      echo "</a>";
      echo "</li>";

    }


 }
 echo "</ul>";
 echo "</div>";
}


?>



<h2 class="bar-heading">Academic Information</h2>
<ul class="styled-list">
  <?php 
    $node = node_load($nid);
    $field = field_get_items('node', $node, 'field_academic_information_toc');

    foreach($field as $item) {
    $aca_page = $item['entity'];
 
    echo "<li>";
    echo "<a href=\"";
    echo boo_url($aca_page->nid);
    echo "\">";
    echo $aca_page->title;
    echo "</a>";
    echo "</li>";
    
}
//    $output = field_view_value('node', $node, 'field_another_entity_test', $field[$delta]);
  ?>
</ul>


  <h2 class="bar-heading">About Clover Park Technical College</h2>
<?php
// display menu that's being used for table of contents
$menu = menu_navigation_links('menu-about-pages-nav');
 print theme('links__menu_about-pages-nav', array('links' => $menu, 'attributes' => array('class' =>array('styled-list'))));
 ?>


  </div>
  <div class="right-col">
    

  <?php boo_snippet('sidebar-menu.php'); ?>
    <?php 
    $field = field_get_items('node', $node, 'field_sidebar');
    $output = field_view_value('node', $node, 'field_sidebar', $field[0]);
    print render($output);
    ?>

    <?php boo_snippet('lead-form.php'); ?>
    
  </div>


