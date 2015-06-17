<?php
// given a node object, displays the degree tables with appropriate headings.
// node object has to be of degree_or_certificate content type

function degree_table_display ($node) {
echo "<h2 class=\"bar-heading\">";
echo "Degree Requirements";
echo "</h2>";
boo_classes_output('field_classes_in_this_degree', $node);
$degree_1_title = field_get_items('node', $node, 'field_degree_option_1_title'); 
$degree_1_title_val = $degree_1_title[0]['value'];
if($degree_1_title_val !== null) {

  echo "<h2 class=\"bar-heading\">";
  echo $degree_1_title_val;
  echo "</h2>";
  boo_classes_output('field_degree_option_1_courses', $node);

}
$degree_2_title = field_get_items('node', $node, 'field_degree_option_2_title'); 
$degree_2_title_val = $degree_2_title[0]['value'];
if($degree_2_title_val !== null) {

  echo "<h2 class=\"bar-heading\">";
  echo $degree_2_title_val;
  echo "</h2>";
  boo_classes_output('field_degree_option_2_courses', $node);
}
}

?>