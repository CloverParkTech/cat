 <?php

// this function outputs the degrees and classes (optional) organized by category
function toc_generator($classes = true) {

// run through all taxonomy terms, list them and their child degrees/certs
  $vid = 2;         
  $terms = taxonomy_get_tree($vid); 





boo_function('display_degrees.php');


  // function to display program titles and classes (optional). takes a $term object as parameter
  function boo_display_program_title($term) {
      echo "<h3>";
      echo $term->name;
      echo "</h3>";
  }

  // function to display link to class descriptions for each area
  function boo_display_classes_link($term, $url) {
      echo "<a class='homepage-area-link' href=\"";
      echo $url;
      echo "\">";
      echo "View All ";
      echo $term->name;
      echo " Courses";
      echo "</a>";   
  }

  foreach ($terms as $term) { 
    // get all the nodes with this tid.
    $degree_array = array();
    $degreenids = taxonomy_select_nodes($term->tid, false, false);
    foreach($degreenids as $degreenid) {
      $degreenode = node_load($degreenid);
      $type =$degreenode->type;
      if($type == 'degree_or_certificate') {
        $degree_array[$degreenid] = $degreenode->title;
      }
    }
      $path = taxonomy_term_uri($term);
      $url = url($path['path']);
      echo "<div class='homepage-area-wrapper'>";
      if($classes == true) {
        boo_display_program_title($term);
        boo_display_classes_link($term, $url);
        echo "<ul>";
        boo_display_degrees($degree_array);
        echo "</ul>";
      }
      else {
        if(!empty($degree_array)) {
          boo_display_program_title($term);
          echo "<ul>";
          boo_display_degrees($degree_array);
          echo "</ul>";
        }
      }
    echo "</div>";
  }
}
?>