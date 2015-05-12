<?php
// Remove Height and Width Inline Styles from Drupal Images
function boo_preprocess_image(&$variables) {
  foreach (array('width', 'height') as $key) {
    unset($variables[$key]);
  }
}




//custom function that echos a clean, relative URL given a node id

function boo_url($nodeid) {
	$options = array('absolute' => FALSE);
	$url = url('node/' . $nodeid, $options);
	echo $url;
}






        
?>

