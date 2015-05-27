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
<div class="grid">
  <div class="col17">
  <h2>Welcome to Clover Park Technical College</h2>
  <?php print render($content['body']); ?>
  <h2>About Clover Park Technical College</h2>
<?php
// display menu that's being used for table of contents
$menu = menu_navigation_links('menu-about-pages-nav');
 print theme('links__menu_about-pages-nav', array('links' => $menu));
 ?>


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
<ul>
  <?php 
    $node = node_load($nid);
    $field = field_get_items('node', $node, 'field_academic_information_toc');

    foreach($field as $item) {
    $aca_page = $item['entity'];
 
    echo "<li>";
    echo "<a href=\"";
    echo boo_url($aca_page->nid);
    echo "\">";
    echo $aca_page->title;
    echo "</a>";
    echo "</li>";
    // there's a better way to access these. Once I have that, make this a function
}
//    $output = field_view_value('node', $node, 'field_another_entity_test', $field[$delta]);
  ?>
</ul>
  </div>
  <div class="col7">
    <?php boo_snippet('search.php'); ?>

    <?php 
    $field = field_get_items('node', $node, 'field_sidebar');
    $output = field_view_value('node', $node, 'field_sidebar', $field[0]);
    print render($output);
    ?>
    <?php boo_snippet('sidebar-menu.php'); ?>
  </div>

</div>
</div>