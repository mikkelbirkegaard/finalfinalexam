<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

add_action('init', 'bbh_remove_post_editor_support', 99);
function bbh_remove_post_editor_support() {
	remove_post_type_support('post','editor');
};

/*=====================================================================
  = Remove page title from blog - We are using description instead =
=======================================================================*/

add_action('wp', 'bbh_remove_blog_page_title', 10);
function bbh_remove_blog_page_title() {
	if(is_post_type_archive('post') || is_category() || true){
		remove_action( 'generate_archive_title', 'generate_archive_title' );
	}
};


add_action('generate_archive_title', 'bbh_custom_archive_title_from_description', 10);
function bbh_custom_archive_title_from_description() {

	if( empty( term_description() )){
		return;
	}
	?>
	<header class="page-header">
		<div class="grid-container">
			<?php
			/**
			 * generate_before_archive_title hook.
			 *
			 * @since 0.1
			 */
			do_action( 'generate_before_archive_title' );
			?>
			<?php
			/**
			 * generate_after_archive_title hook.
			 *
			 * @since 0.1
			 *
			 * @hooked generate_do_archive_description - 10
			 */
			do_action( 'generate_after_archive_title' );
			?>
		</div>
	</header><!-- .page-header -->
	<?php
};
