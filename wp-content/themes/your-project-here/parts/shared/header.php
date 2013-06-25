<header>
	<h1><a href="<?php echo home_url(); ?>">Döllmann</a></h1>
	<?php // bloginfo( 'description' ); ?>
	<?php // get_search_form(); ?>
	<?php wp_nav_menu(array(
		'theme_location' => 'primary',
		'container' => 'nav'
	)); ?>
	<div class="address"><?php echo wpautop( get_option('address') ); ?></div>
</header>
<section class="main">