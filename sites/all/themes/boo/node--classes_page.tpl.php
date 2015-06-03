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
 

<ul class="styled-list">
<?php
$vid = 2;         
  $terms = taxonomy_get_tree($vid);    
 foreach ( $terms as $term ) { 
  $path = taxonomy_term_uri($term);
  $url = url($path['path']);
  echo "<li><a href=\"";
  echo $url;
   echo "\">";
  echo $term->name;
  echo "</a></li>";
 }
?>




  </div>
  <div class="right-col">
    <?php boo_snippet('search.php'); ?>
    

  <?php boo_snippet('sidebar-menu.php'); ?>


    <?php boo_snippet('lead-form.php'); ?>
    
  </div>


