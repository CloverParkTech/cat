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
  <?php
  boo_function("toc_generator.php");
  toc_generator(false);
  ?>
</div>
<div class="right-col">
  <?php boo_snippet('search.php'); ?>
  <?php boo_snippet('sidebar-menu.php'); ?>
  <?php boo_snippet('lead-form.php'); ?>
 </div>


