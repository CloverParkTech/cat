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

<?php $current_tid = $page['content']['system_main']['term_heading']['term']['#term']->tid; ?>
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




<?php // this is a sort of hacky way to do this, but I'm not quite sure how else to only access what I want.

	
	$program_nodes = node_load_multiple(taxonomy_select_nodes($current_tid, false, false, false));
	// $classes = $page['content']['system_main']['nodes'];
//	print_r($program_nodes);
	echo "<pre>";
	// print_r($program_nodes);
	echo "</pre>";
	// sort classes by item number
	function cmp($a, $b) {
		return strcmp($a->title, $b->title);
	}
	usort($program_nodes, "cmp");

	$i = 0;
	foreach($program_nodes as $class) {
		if ($class->type == 'class') {
			echo "<h4 class=\"class-title\">";
			echo $class->field_class_title['und'][0]['safe_value'];
			echo "</h4>";
			echo "<div class=\"class-wrapper\">";
			
			echo "<dl><dt>Item Number</dt><dd>";
			echo $class->title;
			echo "</dd><dt>Credits</dt>";
			echo "<dd>";
			echo $class->field_credits['und'][0]['value'];
			echo "</dd></dl>";
			echo "<p>";
			// definitely a better way to access safe field values
			echo $class->field_description['und'][0]['value'];
			echo "</p>";
			if ($class->field_course_outcomes[$node->language][0]['value']) {
			echo "<h5>";
			echo "Course Outcomes";
			echo "</h5>";
			echo "<p>";
			echo $class['#node']->field_course_outcomes['und'][0]['value'];
			echo "</p>";
		}
			
			echo "</div>";
			$i++;
			
		}





	}
if($i == 0) {
			echo "<h5>No classes found</h5>";
		}



//   ?>

</div>
<div class="right-col">
	
	<?php 
		$p = 0;
		
		foreach($program_nodes as $degreenode) {
			$type = $degreenode->type;
			if($type == 'degree_or_certificate') {
			if($p == 0) {
				echo "<h2 class=\"sidebar-top-header\">Degrees & Certificates in This Program</h2>";
				$p++;
				echo "<ul>";
			}
			echo "<li>";
			echo "<a href=\"";
			echo boo_url($degreenode->nid);
			echo "\">";
			echo $degreenode->title;
			echo "</a>";
			echo "</li>";

		}
	}
			?>
		</ul>

<?php boo_snippet('lead-form.php'); ?>

</div>
</div>


<?php boo_snippet('footer.php'); ?>