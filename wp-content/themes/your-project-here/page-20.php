<?php
/* Startseite */
?>
<?php Starkers_Utilities::get_template_parts( array( 'parts/shared/html-header', 'parts/shared/header' ) ); ?>

<!-- Page Home Template -->
<?php if ( have_posts() ) : the_post(); ?>

<?php get_sidebar( 'Startseite 1' ); ?>

<?php endif; ?>

<?php Starkers_Utilities::get_template_parts( array( 'parts/shared/footer','parts/shared/html-footer' ) ); ?>