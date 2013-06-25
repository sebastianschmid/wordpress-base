<?php
/**
 * The Template for displaying all single posts
 *
 * Please see /external/starkers-utilities.php for info on Starkers_Utilities::get_template_parts()
 *
 * @package 	WordPress
 * @subpackage 	Starkers
 * @since 		Starkers 4.0
 */
?>
<?php Starkers_Utilities::get_template_parts( array( 'parts/shared/html-header', 'parts/shared/header' ) ); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

<article>
	<section class="description">
		<h2><?php the_title(); ?></h2>
		<?php // hue_the_symbol_img(); ?>

		<?php $fields = array( 'city', 'country', 'year' ); ?>
		<div class="meta">
			<?php echo types_render_field('city' , array() ); ?> / <?php echo types_render_field('country' , array() ); ?> / <?php echo types_render_field('year' , array() ); ?>
		</div>

		<div class="langswitch">
			<div class="border"></div>
		</div>
		<div class="text">
		<?php
			$fields = array( 'text-deutsch', 'text-english' );
			foreach ( $fields as $field ) : ?>
				<div class="<?php echo $field ?>">
					<?php echo types_render_field($field , array() ); ?>
				</div>
			<?php endforeach; ?>
		</div>

	</section>
	<div class="masonrize">
		<?php
		$imgs = hue_gallery_ids(get_the_id());
		$imgcounter = 0;
		$imgsizemap = array(
			1 => 'large',
			2 => 'medium',
			3 => 'thumbnail'
		);
		foreach ( $imgs as $img ) :
			?> <div class='imagewrapper'> <?php
			if ( $imgcounter < 3 )
				$imgcounter++;
			$size = $imgsizemap[$imgcounter];

			$image = wp_get_attachment_image_src( $img, $size);

			if ( $image ) :
				list($src, $width, $height) = $image;
				// $hwstring = image_hwstring($width, $height);
				$hwsting = "";
				$attr = array(
					'src' => $src,
					'class' => "attachment-$size"
				);
				$attr = array_map( 'esc_attr', $attr );

				foreach ( $imgsizemap as $imgsize ) :
					$image = wp_get_attachment_image_src( $img, $imgsize);
					$attr['data-size-'.$imgsize] = $image[0];
				endforeach;

				$html = rtrim("<img $hwstring");

				foreach ( $attr as $name => $value ) :
					$html .= " $name=" . '"' . $value . '"';
				endforeach;

				$html .= ' />';

				echo $html;
			endif;
		?> </div> <?php
		endforeach;
		?>
	</div>
</article>
<?php endwhile; ?>
<?php Starkers_Utilities::get_template_parts( array( 'parts/shared/footer','parts/shared/html-footer' ) ); ?>