<div class="search-wrapper">
	<h3>Search the Catalog</h3>
	<?php $form = drupal_get_form('search_block_form', TRUE); ?>
	<?php print render($form); ?>
</div>