<?php
// Remove Height and Width Inline Styles from Drupal Images
function boo_preprocess_image(&$variables) {
  foreach (array('width', 'height') as $key) {
    unset($variables[$key]);
  }
}


// add global js file
drupal_add_js(drupal_get_path('theme', 'boo') . '/js/global.min.js');

      


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


function boo_function($filename) {
	$path = path_to_theme();
	$path .= "/functions/";
	$path .= $filename;
	include_once($path);
}     

function boo_form_search_block_form_alter(&$form, &$form_state) {
  $form['actions']['submit']['#type'] = 'image_button';
  $form['actions']['submit']['#src'] = drupal_get_path('theme', 'boo') . '/images/search.svg';
}


function boo_page_alter(&$page) {
  // kpr($page); //use this to find the item you want to remove - you need the devel running.
  // Remove the search form from the search results page.
  if (arg(0) == 'search') {
    if (!empty($page['content']['system_main']['search_form'])) {
      hide($page['content']['system_main']['search_form']);
    }
  }

}
?>