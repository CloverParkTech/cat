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
      // We hide the comments and links now so that we can render them later.
      hide($content['comments']);
      hide($content['links']);
      print render($content);
    ?>
</div>
<div class="right-col">
  <h3>Program Codes</h3>
  <p>Use these codes on the class schedule page to move individual courses to new categories.</p>
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
