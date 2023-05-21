<?php /* Template Name: Rediger Bruger */?>
<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package GeneratePress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header(); ?>
	<div id="primary" <?php generate_content_class();?>>
		<main id="main" <?php generate_main_class(); ?>>
			<section class="flexible-inner-section bbh-inner-section wineschool-header">
			    <div class="grid-container">
			        <div class="row">
			            <div class="col-sm-12">
			                <div class="wineschool-top-header">
			                    <h1>Profil</h1>
			                    <div class="backLink-img">
			                        <a class="backLink-wineschool" href="/vinskolen/indstillinger"><span class="arrow-left"></span> Tilbage</a>
			                    </div>
			                </div>
			            </div>
			        </div>
			    </div>
			</section>
			<?php
			/**
			 * generate_before_main_content hook.
			 *
			 * @since 0.1
			 */
			do_action( 'generate_before_main_content' );

			while ( have_posts() ) : the_post();  ?>

				<div class="bbh-outer-wrapper" id="bbh-content">
					<?php
					/**
					 * Edit account form
					 *
					 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-edit-account.php.
					 *
					 * HOWEVER, on occasion WooCommerce will need to update template files and you
					 * (the theme developer) will need to copy the new files to your theme to
					 * maintain compatibility. We try to do this as little as possible, but it does
					 * happen. When this occurs the version of the template file will be bumped and
					 * the readme will list any important changes.
					 *
					 * @see https://docs.woocommerce.com/document/template-structure/
					 * @package WooCommerce\Templates
					 * @version 3.5.0
					 */

					defined( 'ABSPATH' ) || exit;

					do_action( 'woocommerce_before_edit_account_form' ); ?>
					<section class="flexible-inner-section bbh-inner-section">
					    <div class="grid-container">
					        <div class="row">
					            <div class="col-sm-12">

									<form class="woocommerce-EditAccountForm edit-account" action="" method="post" <?php do_action( 'woocommerce_edit_account_form_tag' ); ?> >

										<?php do_action( 'woocommerce_edit_account_form_start' ); ?>

										<div class="clear"></div>

										<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
											<label for="account_display_name"><?php esc_html_e( 'Brugernavn', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
											<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_display_name" id="account_display_name" value="<?php echo esc_attr( $user->display_name ); ?>" />
										</p>
										<div class="clear"></div>

										<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
											<label for="account_email"><?php esc_html_e( 'Email address', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
											<input type="email" class="woocommerce-Input woocommerce-Input--email input-text" name="account_email" id="account_email" autocomplete="email" value="<?php echo esc_attr( $user->user_email ); ?>" />
										</p>

										<fieldset>
											<legend><?php esc_html_e( 'Password change', 'woocommerce' ); ?></legend>

											<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
												<label for="password_current"><?php esc_html_e( 'Nuværende adgangskode', 'woocommerce' ); ?></label>
												<input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_current" id="password_current" autocomplete="off" />
											</p>
											<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
												<label for="password_1"><?php esc_html_e( 'Ny adgangskode', 'woocommerce' ); ?></label>
												<input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_1" id="password_1" autocomplete="off" />
											</p>
											<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
												<label for="password_2"><?php esc_html_e( 'Confirm new password', 'woocommerce' ); ?></label>
												<input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_2" id="password_2" autocomplete="off" />
											</p>
										</fieldset>
										<div class="clear"></div>

										<?php do_action( 'woocommerce_edit_account_form' ); ?>

										<p class="btns-fields-wineschool-edit">
											<?php wp_nonce_field( 'save_account_details', 'save-account-details-nonce' ); ?>
											<button type="submit" class="woocommerce-Button button" name="save_account_details" value="<?php esc_attr_e( 'Save changes', 'woocommerce' ); ?>"><?php esc_html_e( 'Save changes', 'woocommerce' ); ?></button>
											<input type="hidden" name="action" value="save_account_details" />
										</p>

										<?php do_action( 'woocommerce_edit_account_form_end' ); ?>
									</form>

					            </div>
					        </div>
					    </div>
					</section>

					<?php do_action( 'woocommerce_after_edit_account_form' ); ?>

					<?php
					//Include flexible content
					include(STYLESHEETPATH . '/include/flexible-content/flexible-content.php');
					?>
				</div>

				<?php
				// If comments are open or we have at least one comment, load up the comment template.
				if ( comments_open() || '0' != get_comments_number() ) : ?>

					<div class="comments-area">
						<?php comments_template(); ?>
					</div>

				<?php endif;

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
