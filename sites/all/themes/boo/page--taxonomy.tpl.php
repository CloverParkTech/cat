<?php

/**
 * @file
 * Default theme implementation to display a single Drupal page.
 *
 * The doctype, html, head and body tags are not in this template. Instead they
 * can be found in the html.tpl.php template in this directory.
 *
 * This is the taxonomy page, which is being used to display class descriptions.
 *
 * @ingroup themeable
 */
?>

<?php boo_snippet('header.php'); ?>

<?php $current_tid = $page['content']['system_main']['term_heading']['term']['#term']->tid; 
?>
<div class="container">
	<h1><?php 
// this is probably the worst way to do this. will fix later
echo $page['content']['system_main']['term_heading']['term']['#term']->name; 
?> Classes</h1>
<?php
echo "<div class=\"breadcrumb-wrapper\">";
print render($page['breadcrumb']);
echo "</div>";
?>
	<div class="left-col">




<?php 
	
boo_function('class_descriptions.php');



class_descriptions($current_tid);




boo_function('display_degrees.php');

//   ?>

</div>
<div class="right-col">
	
	<?php 
		$p = 0;
		$degree_array = array();
		$program_nodes = node_load_multiple(taxonomy_select_nodes($current_tid, false, false));
		foreach($program_nodes as $degreenode) {
			$type = $degreenode->type;
			if($type == 'degree_or_certificate') {
				$degree_array[$degreenode->nid] = $degreenode->title;
			}
		}	

		if(!empty($degree_array)) {
 		 echo "<h2 class=\"sidebar-top-header\">Degrees & Certificates in This Program</h2>";
   		 echo "<ul>";
      		boo_display_degrees($degree_array);
    		echo "</ul>";

}	



			?>
		</ul>

<?php boo_snippet('lead-form.php'); ?>

</div>
</div>


<?php boo_snippet('footer.php'); ?>