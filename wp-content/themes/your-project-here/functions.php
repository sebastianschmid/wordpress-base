<?php
	/**
	 * Starkers functions and definitions
	 *
	 * For more information on hooks, actions, and filters, see http://codex.wordpress.org/Plugin_API.
	 *
	 * @package 	WordPress
	 * @subpackage 	Starkers
	 * @since 		Starkers 4.0
	 */



require_once( 'external/starkers-utilities.php' );

add_theme_support('post-thumbnails');

register_nav_menus(array('primary' => 'Primary Navigation'));

add_action( 'wp_enqueue_scripts', 'starkers_script_enqueuer' );

add_filter( 'body_class', array( 'Starkers_Utilities', 'add_slug_to_body_class' ) );

function starkers_script_enqueuer() {
	wp_register_script( 'site', get_template_directory_uri().'/js/site.js', array( 'jquery' ) );
	wp_enqueue_script( 'site' );

	wp_register_style( 'screen', get_stylesheet_directory_uri().'/style.css', '', '', 'screen' );
	wp_enqueue_style( 'screen' );

	wp_register_style( 'screen-less', get_stylesheet_directory_uri().'/style.less', '', '', 'screen' );
	wp_enqueue_style( 'screen-less' );

}


/* remove 28px top margin if admin bar is visible */
add_action( 'admin_bar_init', 'hue_remove_bump');
function hue_remove_bump() {
	remove_action( 'wp_head', '_admin_bar_bump_cb' );
}


/* add widget function for start page */
add_action( 'widgets_init', 'hue_register_sidebars' );

function hue_register_sidebars() {
	register_sidebar(array('name'=>'Start Page'));
}

/* second Featured Image (for hover)
************************************/
if (class_exists('MultiPostThumbnails')) {
	new MultiPostThumbnails(
		array(
			'label' => 'Hover Image',
			'id' => 'hover-image',
			'post_type' => 'page'
		)
	);
}

/* patch the default filter for category archives, showing only posts */
add_filter('pre_get_posts', 'query_post_type');
function query_post_type($query) {
	if( is_category() || is_tag() ) {
		$post_type = array('projects', 'nav_menu_item');
		$query->set('post_type',$post_type);
		return $query;
	}
}

/* Custom callback for outputting comments */
function starkers_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment; 
	?>
	<?php if ( $comment->comment_approved == '1' ): ?>	
	<li>
		<article id="comment-<?php comment_ID() ?>">
			<?php echo get_avatar( $comment ); ?>
			<h4><?php comment_author_link() ?></h4>
			<time><a href="#comment-<?php comment_ID() ?>" pubdate><?php comment_date() ?> at <?php comment_time() ?></a></time>
			<?php comment_text() ?>
		</article>
	<?php endif;
}

/* set crop for medium and large images sizes */
if(false === get_option("medium_crop")) {
	add_option("medium_crop", "1");
} else {
	update_option("medium_crop", "1");
}

if(false === get_option("large_crop")) {
	add_option("large_crop", "1");
} else {
	update_option("large_crop", "1");
}

/* displaying the symbols and helper functions for that */
function hue_has_symbol() {
	return ( types_render_field('symbol', array() ) !== 'no symbol' );
}

function hue_get_the_symbol() {
	return get_stylesheet_directory_uri().'/img/symbols/'.types_render_field('symbol' , array( 'raw' => 'true' ))."-".types_render_field('symbol-color' , array()).".png";
}

function hue_the_symbol_img() {
	if ( hue_has_symbol() )
		echo '<img src="'.hue_get_the_symbol().'" class="symbol" alt="">';
}

/* Add top level category to body class */
add_filter('body_class','hue_top_cat_body_class');

function hue_top_cat_body_class( $classes ) {
	if( is_single() ) :
		global $post;
		$cats = get_the_category( $post->ID );
		if( count( $cats ) > 1 ) {
			$classes[] = 'genericClass';
		}
		else {
			$cat_anc = get_ancestors( $cats[0]->term_id, 'category' );
			$top_cat = array_merge( array($cats[0]->term_id), $cat_anc );
			$top_cat = array_pop( $top_cat );
			$classes[] = get_category($top_cat)->slug;
		}
	elseif( is_category() ) :
		$cat_anc = get_ancestors( get_query_var('cat'), 'category' );
		$top_cat = array_merge( array(get_query_var('cat')), $cat_anc );
		$top_cat = array_pop( $top_cat );
		$classes[] = get_category($top_cat)->slug;
	endif;

	return $classes;
}



/* custom global fieldz! */
add_action('admin_menu', 'hue_global_fields_interface');

function hue_global_fields_interface() {
	add_options_page('Global Settings', 'Global Settings', '8', 'functions', 'hue_global_fields');
}

function hue_global_fields() {
	?>
	<div class='wrap'>
	<h2>Global Settings</h2>
	<form method="post" action="options.php">
	<?php wp_nonce_field('update-options') ?>

	<p><strong>Address:</strong><br />
	<textarea name="address" cols="100%" rows="7"><?php echo get_option('address'); ?></textarea></p>

	<p><input type="submit" name="Submit" value="Update Options" /></p>

	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="page_options" value="address" />

	</form>
	</div>
	<?php
}