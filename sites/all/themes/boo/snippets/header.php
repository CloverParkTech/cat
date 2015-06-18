<div class="catalog-header-wrapper">
	<div class="container">
	<header class="catalog-header">
		<?php global $base_url; ?>
	<a href="<?php echo base_path(); ?>"><img src="<?php echo $base_url; ?>/<?php echo drupal_get_path('theme', 'boo'); ?>/images/logo.png" class="catalog-header-logo" /></a>
	  <a href="<?php echo base_path(); ?>" class="catalog-header-title">Academic Catalog</a>
	

	</header>
	</div>
</div>
<nav class="header-nav">
	<div class="container">
	<?php
// display main nav menu. yes, it's spelled wrong
$menu = menu_navigation_links('menu-main-navigatoin');
 print theme('links__menu_main-navigatoin', array('links' => $menu, 'attributes' => array('class' =>array('header-nav-ul'))));
 ?>


	<div class="search-box">
		<?php $form = drupal_get_form('search_block_form', TRUE); ?>
		<?php print render($form); ?>
	</div>
	
	</div>
</nav>
