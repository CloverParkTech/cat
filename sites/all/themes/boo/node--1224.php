<?php

/**
 * @file
 * This file is for the Program Codes page. It lists the program codes for editing the class schedule.
 *
 *
 *
 * @ingroup themeable
 */
?>

<div class="left-col">
  <?php
  $vid = 2; // vocabulary ID for programs vocabulary         
  $terms = taxonomy_get_tree($vid);    
  foreach ( $terms as $term ) { 
  	echo "<pre>";
    print_r($term);
    echo "</pre>";
 }
 ?>
</div>
<div class="right-col">
 
</div>