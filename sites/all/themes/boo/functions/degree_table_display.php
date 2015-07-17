<?php
// given a node object, displays the degree tables with appropriate headings.
// node object has to be of degree_or_certificate content type


function degree_table_display ($node) {


	// call the functions used in this function
	boo_function('classes_array.php');
	boo_function('table_output.php');
	boo_function('credits_sum.php');


	$classes_array_index = 0; // index used to track popup boxes
	$table_index= 0; // index used to track how many tables we've output
	$credits = 0; // set the counter we'll use for degree credits
	$max_credits = 0; // set the counter for max credits. This is the DIFFERENCE between the max credits and total credits.

	echo "<h2 class=\"bar-heading\">";
	echo "Degree Requirements";
	echo "</h2>";


	$classes_array = boo_classes_array('field_classes_in_this_degree', $node, $classes_array_index);
	$credits_array = boo_credits_sum($classes_array);
	boo_table_output($classes_array, $table_index, $credits_array[0], $credits_array[1]);
	
	// plus up the indexes
	$classes_array_index = end($classes_array)['index'] + 1;
	$table_index = 1;


	// display the second degree option, if it exists
	$degree_1_title = field_get_items('node', $node, 'field_degree_option_1_title'); 
	$degree_1_title_val = $degree_1_title[0]['value'];
	if($degree_1_title_val !== null) {
	  echo "<h2 class=\"bar-heading\">";
	  echo $degree_1_title_val;
	  echo "</h2>";

	  $degree_array_one = boo_classes_array('field_degree_option_1_courses', $node, $classes_array_index);
	  
	  boo_table_output($degree_array_one, $table_index, $credits_array[0], $credits_array[1]);

	  $classes_array_index = end($degree_array_one)['index'] + 1;
	  $table_index = 2;
	}


	// display the third degree option, if it exists
	$degree_2_title = field_get_items('node', $node, 'field_degree_option_2_title'); 
	$degree_2_title_val = $degree_2_title[0]['value'];
	if($degree_2_title_val !== null) {
	  echo "<h2 class=\"bar-heading\">";
	  echo $degree_2_title_val;
	  echo "</h2>";

	  $degree_array_two = boo_classes_array('field_degree_option_2_courses', $node, $classes_array_index);
	  boo_table_output($degree_array_two, $table_index, $credits_array[0], $credits_array[1]);
	}
}

?>