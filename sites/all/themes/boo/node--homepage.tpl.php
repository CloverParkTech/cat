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
boo_function("toc_generator.php");
toc_generator();

?>



<h2 class="bar-heading">Academic Information</h2>


<?php
// display menu that's being used for table of contents
$menu = menu_navigation_links('menu-homepage-academic-pages');
 print theme('links__menu_homepage-academic-pages', array('links' => $menu, 'attributes' => array('class' =>array('styled-list'))));
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


