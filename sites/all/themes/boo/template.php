<?php
// Remove Height and Width Inline Styles from Drupal Images
function boo_preprocess_image(&$variables) {
  foreach (array('width', 'height') as $key) {
    unset($variables[$key]);
  }
}


// add global js file
function boo_preprocess_node(&$variables) {
	// add global js
      drupal_add_js(drupal_get_path('theme', 'boo') . '/js/global.min.js');

      }


      //custom function that echos a clean, relative URL given a node id
function boo_url($nodeid) {
	$options = array('absolute' => FALSE);
	$url = url('node/' . $nodeid, $options);
	echo $url;
}

// function to include php files from the snippets folder
function boo_snippet($filename) {
	$path = path_to_theme();
	$path .= "/snippets/";
	$path .= $filename;
	include($path);
}      
?>