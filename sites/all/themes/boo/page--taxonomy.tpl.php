<?php

/**
 * @file
 * Default theme implementation to display a single Drupal page.
 *
 * The doctype, html, head and body tags are not in this template. Instead they
 * can be found in the html.tpl.php template in this directory.
 *
 * Available variables:
 *
 * General utility variables:
 * - $base_path: The base URL path of the Drupal installation. At the very
 *   least, this will always default to /.
 * - $directory: The directory the template is located in, e.g. modules/system
 *   or themes/bartik.
 * - $is_front: TRUE if the current page is the front page.
 * - $logged_in: TRUE if the user is registered and signed in.
 * - $is_admin: TRUE if the user has permission to access administration pages.
 *
 * Site identity:
 * - $front_page: The URL of the front page. Use this instead of $base_path,
 *   when linking to the front page. This includes the language domain or
 *   prefix.
 * - $logo: The path to the logo image, as defined in theme configuration.
 * - $site_name: The name of the site, empty when display has been disabled
 *   in theme settings.
 * - $site_slogan: The slogan of the site, empty when display has been disabled
 *   in theme settings.
 *
 * Navigation:
 * - $main_menu (array): An array containing the Main menu links for the
 *   site, if they have been configured.
 * - $secondary_menu (array): An array containing the Secondary menu links for
 *   the site, if they have been configured.
 * - $breadcrumb: The breadcrumb trail for the current page.
 *
 * Page content (in order of occurrence in the default page.tpl.php):
 * - $title_prefix (array): An array containing additional output populated by
 *   modules, intended to be displayed in front of the main title tag that
 *   appears in the template.
 * - $title: The page title, for use in the actual HTML content.
 * - $title_suffix (array): An array containing additional output populated by
 *   modules, intended to be displayed after the main title tag that appears in
 *   the template.
 * - $messages: HTML for status and error messages. Should be displayed
 *   prominently.
 * - $tabs (array): Tabs linking to any sub-pages beneath the current page
 *   (e.g., the view and edit tabs when displaying a node).
 * - $action_links (array): Actions local to the page, such as 'Add menu' on the
 *   menu administration interface.
 * - $feed_icons: A string of all feed icons for the current page.
 * - $node: The node object, if there is an automatically-loaded node
 *   associated with the page, and the node ID is the second argument
 *   in the page's path (e.g. node/12345 and node/12345/revisions, but not
 *   comment/reply/12345).
 *
 * Regions:
 * - $page['help']: Dynamic help text, mostly for admin pages.
 * - $page['highlighted']: Items for the highlighted content region.
 * - $page['content']: The main content of the current page.
 * - $page['sidebar_first']: Items for the first sidebar.
 * - $page['sidebar_second']: Items for the second sidebar.
 * - $page['header']: Items for the header region.
 * - $page['footer']: Items for the footer region.
 *
 * @see template_preprocess()
 * @see template_preprocess_page()
 * @see template_process()
 * @see html.tpl.php
 *
 * @ingroup themeable
 */
?>

<header>
  Clover Park Technical College Academic Catalog
</header>
<nav>
    <a href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>" rel="home" id="logo">
       Homepage
    </a> 

    <?php
    // not working yet
     if ($breadcrumb): ?>
      <div id="breadcrumb"><?php print $breadcrumb; ?></div>
    <?php endif; ?>
</nav>



<h1><?php 
// this is probably the worst way to do this. will fix later
echo $page['content']['system_main']['term_heading']['term']['#term']->name; 
?> Classes</h1>
<div class="content">


<?php // this is a sort of hacky way to do this, but I'm not quite sure how else to only access what I want.
	$classes = $page['content']['system_main']['nodes'];

	foreach($classes as $class) {
		if ($class['#node']->type == 'class') {
			echo "<h4>";
			echo $class['#node']->title;
			echo "</h4>";
			echo "<p>";
			echo $class['#node']->field_item_number['und'][0]['safe_value'];
			echo "</p>";
			echo "<p>";
			echo $class['#node']->field_credits['und'][0]['value'];
			echo "</p>";
			echo "<h5>";
			echo "Course Outcomes";
			echo "</h5>";
			echo "<p>";
			echo $class['#node']->field_course_outcomes['und'][0]['value'];
			echo "</p>";
			echo "<p>";
			// definitely a better way to access safe field values
			echo $class['#node']->field_description['und'][0]['value'];
			echo "</p>";
			echo "<pre>";
			// print_r($class['#node']);
			echo "</pre>";
		}




	}



//   ?>

</div>

<div>
	<h2>Degrees & Certificates in This Program</h2>
	<?php 
		foreach($classes as $class) {
		if ($class['#node']->type == 'degree_or_certificate') {
			echo "<p>";
			echo "<a href=\"";
			echo boo_url($class['#node']->nid);
			echo "\">";
			echo $class['#node']->title;
			echo "</a>";
			echo "</p>";

		}
	}
			?>


</div>

<footer>
&copy; <?php echo date("Y") ?> Clover Park Technical College
</footer>
