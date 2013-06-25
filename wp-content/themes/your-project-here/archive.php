<?php
/**
 * The template for displaying Archive pages.
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * Please see /external/starkers-utilities.php for info on Starkers_Utilities::get_template_parts() 
 *
 * @package 	WordPress
 * @subpackage 	Starkers
 * @since 		Starkers 4.0
 */
?>
<?php Starkers_Utilities::get_template_parts( array( 'parts/shared/html-header', 'parts/shared/header' ) ); ?>
<?php 
// global $query_string;
// query_posts($query_string . "&post_type=any");
?>
<div class="masonrize">
	<?php $page = (get_query_var('paged')) ? get_query_var('paged') : 1;
		if ( $page == 1 ) :
	?>
	<article class="medium description">
		<?php echo category_description(); ?>
	</article>
<?php endif; ?>
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
	<?php $size = types_render_field( 'size', array() ); ?>
	<article class="<?php echo $size; ?>">
		<a href="<?php esc_url( the_permalink() ); ?>" title="Permalink to <?php the_title(); ?>" rel="bookmark">
		<?php $imgs = hue_gallery_ids(get_the_id());
			if ($imgs) :
				echo wp_get_attachment_image( $imgs[0], types_render_field( 'size', array() ) );
			else :
				echo '<div class="filler"></div>';
			endif;
		?>
		<h2><?php the_title(); ?></h2>
		<?php hue_the_symbol_img(); ?>
		</a>
	</article>
<?php endwhile; ?>
</div>
<div class="loadmore"><?php echo get_next_posts_link('Load More...'); ?></div>
<?php endif; ?>

<?php Starkers_Utilities::get_template_parts( array( 'parts/shared/footer','parts/shared/html-footer' ) ); ?>