<?php
/**
 * The Template for displaying all single posts.
 *
 * @package GeneratePress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
$banner = get_field( 'article_banner_image', 'theme_blog_settings' );

get_header(); ?>
	<div id="primary" <?php generate_do_element_classes( 'content' ); ?>>
		<main id="main" <?php generate_do_element_classes( 'main' ); ?>>
			<?php
			if ($banner) : ?>
				<div id="blog-banner" class="lazyload" <?php lazyBg($banner['ID']) ?>>
				</div>
			<?php endif; ?>
			<?php
			/**
			 * generate_before_main_content hook.
			 *
			 * @since 0.1
			 */
			do_action( 'generate_before_main_content' );

			while ( have_posts() ) : the_post();

				?>
				<div id="bbh-content">
					<?php
					// Import variable
					$file_path = STYLESHEETPATH . '/include/flexible-content/import/';

					// check if the flexible content field has rows of data
					if( have_rows('article_sections') ): ?>

						<div class="flexible-field-wrapper">
					        <?php // loop through the rows of data
					        while ( have_rows('article_sections') ) : the_row();
							?>
							<?php

								// save layout name as var
					            $slug = get_row_layout();
								// check if layout exist in import folder
								if( file_exists( get_theme_file_path("/include/flexible-content/import/{$slug}.php") ) ) {
					        		include( get_theme_file_path("/include/flexible-content/import/{$slug}.php") );
					        	}

					        endwhile; // END while have_rows() ?>
						</div> <?php // END div.flexible-field-wrapper ?>
					<?php else :
					    // no layouts found
					endif;

					?>
				</div>
				<?php

			endwhile;

			/**
			 * generate_after_main_content hook.
			 *
			 * @since 0.1
			 */
			do_action( 'generate_after_main_content' );
			?>
		</main><!-- #main -->
	</div><!-- #primary -->

	<?php
	/**
	 * generate_after_primary_content_area hook.
	 *
	 * @since 2.0
	 */
	do_action( 'generate_after_primary_content_area' );

	generate_construct_sidebars();

get_footer();
