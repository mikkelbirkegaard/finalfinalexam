<?php /* Template Name: Vinskolen Registrer Bruger */?>
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

add_action( 'user_register', 'automatically_log_me_in' );

get_header(); ?>
	<div id="primary" <?php generate_content_class();?>>
		<main id="main" <?php generate_main_class(); ?>>
			<section class="flexible-inner-section bbh-inner-section wineschool-header">
			    <div class="grid-container">
			        <div class="row">
			            <div class="col-sm-12">
			                <div class="wineschool-top-header">
			                    <h1>Vinskolen</h1>
			                    <div class="backLink-img">
			                        <a class="backLink-wineschool" href="/vinskolen"><span class="arrow-left"></span> Tilbage</a>
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

					do_action( 'woocommerce_before_edit_account_form', 99 ); ?>
					<section class="flexible-inner-section bbh-inner-section wineschool-registrer">

						<!-- <div class="registration-comlplate">
							<?php if (isset($_POST['waitereregister'])) {

							  $getusername   =   sanitize_user( $_POST['username'] );
							  $getpassword  =   esc_attr( $_POST['password'] );
							  $getemail      =  $_POST['email'];
							  $gettype = 'waiter';
							  $getvinkortid      =  $_POST['vinkortid'];

							 registration_validation($getusername,$getpassword,$getemail,$getvinkortid);
							 if (empty( $reg_errors->errors )) {
							  wpdocs_custom_login($getusername,$getpassword);
							 }
							 if( is_wp_error( $reg_errors ) && ! empty( $reg_errors->errors ) ) {?>
							  <ul class="woocommerce-error" role="alert">
								  <?php
								  foreach ( $reg_errors->get_error_messages() as $error ) {
									  echo '<li>';
									  echo '<strong>FEJL</strong>: ';
									  echo $error . '<br/>';
									  echo '</li>';

								  }?>
							  </ul>
						  <?php }
						  if ( 1 > count( $reg_errors->get_error_messages() ) ) {

						  registration($getusername,$getpassword,$getemail,$gettype, $getvinkortid);

						  }} ?>
						</div> -->

					    <div class="grid-container">
					        <div class="row">
					            <div class="col-sm-12">

									<div class="registrer-container">
										<h2>Register bruger</h2>
									<!-- <form action="" method="post">
									      <div class="form-group">
									        <div class="input-group">
									          <span class="input-group-addon"><i class="ti-user"></i></span>
									          <input type="text" class="form-control" name="username" placeholder="Brugernavn" required>
									        </div>
									      </div>

									      <div class="form-group">
									        <div class="input-group">
									          <span class="input-group-addon"><i class="ti-email"></i></span>
									          <input type="text" name="email" class="form-control" placeholder="E-mail" required>
									        </div>
									      </div>

									      <div class="form-group">
									        <div class="input-group">
									          <span class="input-group-addon"><i class="ti-unlock"></i></span>
									          <input type="password" name="password"class="form-control" placeholder="Vælg adgangskode" required>
									        </div>
									      </div>
										  <div class="form-group activation-group">
									        <div class="input-group">
									          <span class="input-group-addon"><i class="ti-unlock"></i></span>
									          <input type="text" name="vinkortid"class="form-control" placeholder="Vinkort ID" required>
									        </div>
									      </div>

									      <button class="bbh-btn" type="submit" name="waitereregister">Opret bruger</button>


									    </form> -->
										<!-- <?php echo do_shortcode('[vinskolen-opret-bruger]') ?> -->
										<?php echo do_shortcode('[gravityform id="5" title="false" description="false"]'); ?>
									</div>
					            </div>
					        </div>
					    </div>
					</section>


					<?php
					//Include flexible content
					include(STYLESHEETPATH . '/include/flexible-content/flexible-content.php');
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
