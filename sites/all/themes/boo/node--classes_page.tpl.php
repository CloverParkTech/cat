<?php

/**
 * @file
 * Template for the page linking to all the individual class listings.
 * This runs through the programs taxonomy terms and links to class listings for each of them.
 *
 *
 *
 * @ingroup themeable
 */
?>

<div class="left-col">
  <ul class="styled-list">
  <?php
  $vid = 2; // vocabulary ID for programs vocabulary         
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
  </ul>
</div>
<div class="right-col">
  <?php boo_snippet('search.php'); ?>
  <?php boo_snippet('sidebar-menu.php'); ?>
  <?php boo_snippet('lead-form.php'); ?>  
</div>