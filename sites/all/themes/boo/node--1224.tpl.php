<?php

/**
 * @file
 * This file is for the Program Codes page. It lists the program codes for editing the class schedule.
 *
 *
 *
 * @ingroup themeable
 */
echo "<h1>HEHHHHH</h1>";
?>

<div class="left-col">
  <?php
  $vid = 2; // vocabulary ID for programs vocabulary         
  $terms = taxonomy_get_tree($vid);    
  foreach ( $terms as $term ) { 
  	echo "<pre>";
    print_r($term->tid);
    echo "</pre>";
    echo "<pre>";
    print_r($term->name);
    echo "</pre>";
 }
 ?>
</div>
<div class="right-col">
 
</div>