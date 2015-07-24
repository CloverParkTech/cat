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
    <?php
      // We hide the comments and links now so that we can render them later.
      hide($content['comments']);
      hide($content['links']);
      print render($content);
    ?>
</div>
<div class="right-col">
  <h3>More Academic Info</h3>
  <?php
// display menu that's being used for table of contents
$menu = menu_navigation_links('menu-homepage-academic-pages');
 print theme('links__menu_homepage-academic-pages', array('links' => $menu, 'attributes' => array('class' =>array('styled-list'))));
?>
</div>