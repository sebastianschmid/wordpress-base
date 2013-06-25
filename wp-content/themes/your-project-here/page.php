<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * Please see /external/starkers-utilities.php for info on Starkers_Utilities::get_template_parts()
 *
 * @package 	WordPress
 * @subpackage 	Starkers
 * @since 		Starkers 4.0
 */
?>
<?php Starkers_Utilities::get_template_parts( array( 'parts/shared/html-header', 'parts/shared/header' ) ); ?>

<?php if ( have_posts() ) : the_post(); ?>
<?php
	
	$toppost_id = $post->post_parent;
	// echo $toppost_id;
	if ( $toppost_id == 0 )
		$toppost_id = $post->ID;
	// echo $toppost_id;
	
	$toppost = get_post( $toppost_id );
	$menuitems = get_posts( array(
		'numberposts'		=>	99,
		'orderby'			=>	'menu_order',
		'order'				=>	'ASC',
		// 'include'			=>	$toppost_id,
		'post_type'			=>	'page',
		'post_parent'		=>	$toppost_id,
		'post_status'		=>	'publish' )
	);
	array_unshift($menuitems, $toppost); // put toppost at front of menuitems
?>
<ul class="contact">
<?php foreach ( $menuitems as $menuitem ) : ?>
	<li class="small">
		<a href="<?php echo get_permalink( $menuitem->ID ); ?>">
			<?php MultiPostThumbnails::the_post_thumbnail(get_post_type(), 'hover-image', $menuitem->ID, 'thumbnail');?>
			<?php echo get_the_post_thumbnail( $menuitem->ID, 'thumbnail', array ( "class" => "hover-image" ) ); ?>
			
			<span class="title"><?php echo $menuitem->post_title; ?></span>
		</a>
	</li>
<?php endforeach; ?>
</ul>

<article>
	<h2><?php the_title(); ?></h2>
	<?php the_content(); ?>
</article>
<?php endif; ?>

<?php Starkers_Utilities::get_template_parts( array( 'parts/shared/footer','parts/shared/html-footer' ) ); ?>