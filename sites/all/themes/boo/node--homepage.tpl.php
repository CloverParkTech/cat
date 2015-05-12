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

<h2>About Clover Park Technical College</h2>

  <ul>
  <?php 
    $node = node_load($nid);
    $field = field_get_items('node', $node, 'field_about_toc');

    foreach($field as $item) {
    $content_page = $item['entity'];
 
    echo "<li>";
    echo "<a href=\"";
    echo boo_url($content_page->nid);
    echo "\">";
    echo $content_page->title;
    echo "</a>";
    echo "</li>";
    // there's a better way to access these. Once I have that, make this a function
}
//    $output = field_view_value('node', $node, 'field_another_entity_test', $field[$delta]);
  ?>

</ul>

<h2>Degrees & Certificates</h2>
<?php /* List all degrees and certificates here */ ?>
<ul>
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


<h2>Course Descriptions</h2>
<ul>
<?php
$vid = 2;         
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

<h2>Academic Information</h2>


  </div>


</div>