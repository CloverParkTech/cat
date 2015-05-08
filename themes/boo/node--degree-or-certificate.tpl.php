<?php

/**
 * @file
 * Default theme implementation to display a degree or certificate
 *
 * @see template_preprocess()
 * @see template_preprocess_node()
 * @see template_process()
 *
 * @ingroup themeable
 */
?>
<div id="node-<?php print $node->nid; ?>" class="<?php print $classes; ?> clearfix"<?php print $attributes; ?>>


  <?php print render($title_prefix); ?>
  <?php if (!$page): ?>
    <h1<?php print $title_attributes; ?>><a href="<?php print $node_url; ?>"><?php print $title; ?></a> TEST</h1>
  <?php endif; ?>
  <?php print render($title_suffix); ?>

  <?php if ($display_submitted): ?>
    <div class="submitted">
      <?php print $submitted; ?>
    </div>
  <?php endif; ?>

  <div class="content"<?php print $content_attributes; ?>>
  <h2>Classes in This Degree</h2>
  <table>
  <?php 
    $node = node_load($nid);
    $field = field_get_items('node', $node, 'field_another_entity_test');
    $class = $field[0]['entity'];
   // print_r($class);
    echo "<tr>";
    echo "<td>";
    echo $class->title;
    echo "</td>";
    // there's a better way to access these. Once I have that, make this a function
    echo "<td>";
    echo $class->field_item_number['und'][0]['value'];
     echo "</td>";
    echo "<td>";
    echo $class->field_credits['und'][0]['value'];
     echo "</td>";
      echo "</tr>";
//    $output = field_view_value('node', $node, 'field_another_entity_test', $field[$delta]);
  ?>

</table>
    <?php
      // We hide the comments and links now so that we can render them later.
     // hide($content['comments']);
     // hide($content['links']);
     // print render($content);
    ?>
  </div>


</div>
