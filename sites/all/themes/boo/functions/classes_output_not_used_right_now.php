<?php

// give this the name of the field, the node object, an index counter set to 0, and whether we want the popup boxes



function boo_classes_output($field_name, $pagenode, $index, $popups = true) {
  $classes_array = boo_classes_array($field_name, $pagenode, $index);
  boo_table_output($classes_array);
}




// need to separate these functions into their own files





