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
<?php /* List all degrees and certificates here */ ?>
<ul class="styled-list">
<?php 
    $query = new EntityFieldQuery();
    $query->entityCondition('entity_type', 'node')
        ->entityCondition('bundle', 'degree_or_certificate')
        ->propertyCondition('status', 1);
        $result = $query->execute();
        if (!empty($result['node'])) {
          $nids = array_keys($result['node']);
          $nodes = node_load_multiple($nids);
          foreach($nodes as $node) {
            echo "<li><a href=\"";
            echo boo_url($node->nid);
            echo "\">";
            echo $node->title;
            echo "</a></li>";
          }
        }
?>
</ul>

  </div>
  <div class="right-col">
    <?php boo_snippet('search.php'); ?>
    

  <?php boo_snippet('sidebar-menu.php'); ?>

    <?php boo_snippet('lead-form.php'); ?>
    
  </div>


