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
  <table>
  <?php
  $vid = 2; // vocabulary ID for programs vocabulary         
  $terms = taxonomy_get_tree($vid);    
  foreach ( $terms as $term ) { 
    echo "<tr>";
  	echo "<td>";
    print_r($term->tid);
    echo "</td>";
    echo "<td>";
    print_r($term->name);
    echo "</td>";
    echo "</tr>";
 }
 ?>
</table>
</div>
<div class="right-col">
 
</div>