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
<div class="grid">
	<div class="col15">




<?php // this is a sort of hacky way to do this, but I'm not quite sure how else to only access what I want.

	$classes = $page['content']['system_main']['nodes'];
	echo "<pre>";
	//print_r($classes);
	echo "</pre>";
	// sort classes by item number
	function cmp($a, $b) {
		return strcmp($a['#node']->title, $b['#node']->title);
	}
	usort($classes, "cmp");

	$i = 0;
	foreach($classes as $class) {
		if ($class['#node']->type == 'class') {
			echo "<h4 class=\"class-title\">";
			echo $class['#node']->field_class_title['und'][0]['safe_value'];
			echo "</h4>";
			echo "<div class=\"class-wrapper\">";
			
			echo "<dl><dt>Item Number</dt><dd>";
			echo $class['#node']->title;
			echo "</dd><dt>Credits</dt>";
			echo "<dd>";
			echo $class['#node']->field_credits['und'][0]['value'];
			echo "</dd></dl>";
			echo "<p>";
			// definitely a better way to access safe field values
			echo $class['#node']->field_description['und'][0]['value'];
			echo "</p>";
			if ($class['#node']->field_course_outcomes[$node->language][0]['value']) {
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
<div class="col9">
	
	<?php 
		$p = 0;
		$current_tid = $page['content']['system_main']['term_heading']['term']['#term']->tid; 
		$navnids = taxonomy_select_nodes($current_tid, false, false, false);
		foreach($navnids as $degreenid) {
			$degreenode = node_load($degreenid);
			$type = $degreenode->type;
			if($type == 'degree_or_certificate') {
			if($p == 0) {
				echo "<h2 class=\"sidebar-top-header\">Degrees & Certificates in This Program</h2>";
				$p++;
			}
			echo "<p>";
			echo "<a href=\"";
			echo boo_url($degreenid);
			echo "\">";
			echo $degreenode->title;
			echo "</a>";
			echo "</p>";

		}
	}
			?>


</div>
</div>
</div>

<?php boo_snippet('footer.php'); ?>