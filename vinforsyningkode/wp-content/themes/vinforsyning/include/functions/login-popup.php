<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;


add_action('wp_footer', 'bbh_login_popup');
function bbh_login_popup(){
	if(is_user_logged_in()){
		return;
	}
	$error = isset($_GET['login']) && $_GET['login'] == 'failed';
	$class = $error ? 'open' : ''; // check if we're reloading a failed attempt

	$options = 'theme_login_settings';
	$subheading = get_field( 'subheading', $options );
	$heading = get_field( 'heading', $options );
	$signupText = get_field( 'signup_text', $options );
	$cta = get_field( 'signup_cta', $options );
	global $wp;
	?>
	<div id="login-popup-overlay" class="<?php echo $class; ?>"></div>
	<div id="login-popup" class="<?php echo $class; ?>">
		<span class="close icon icomoon icon-st-close-02"></span>
		<div class="inside-popup">
			<div class="login-wrap">
				<?php if ($subheading): ?>
					<h5 class="subheading">
						<?php echo $subheading; ?>
					</h5>
				<?php endif; ?>
				<?php if ($heading): ?>
					<?php echo $heading ?>
				<?php endif; ?>
				<?php
				/* wp_login_form(
					array(
						'echo' => true,
						'redirect' => home_url($wp->request),
						'remember' => false,
						'label_username' => __('Indtast brugernavn','bbh'),
						'label_password' => __('Indtast password','bbh'),
					)
				);
				*/

				?>
				<form id="loginform" name="loginform" action="<?php echo wp_login_url(home_url($wp->request)) ?>" method="POST">
					<?php if ($error): ?>
						<p class="error-message">
							<?php _e('Der var en fejl med din indtastning.', 'bbh'); ?>
							<br>
							<?php _e('Tjek dine loginoplysninger og prøv venligst igen.', 'bbh'); ?>
						</p>
					<?php endif; ?>
					<p class="login-username">
						<label for="user_login">
							<?php _e('E-mail', 'bbh'); ?><sup class="required">*</sup>
						</label>
						<input required type="text" id="user_login" class="input" name="log" value="" placeholder="<?php esc_attr_e('Indtast emailadresse', 'bbh'); ?>" input-mode="email">
					</p>

					<p class="login-password">
						<label for="user_pass">
							<?php _e('Adgangskode', 'bbh'); ?><sup class="required">*</sup>
						</label>
						<input required type="password" name="pwd" id="user_pass" class="input" value="" placeholder="<?php esc_attr_e('Indtast adgangskode', 'bbh'); ?>"  autocomplete="off">
					</p>
					<?php do_action( 'login_form' ) ?>
					<p class="login-submit">
						<input type="submit" value="<?php _e('Log ind', 'bbh'); ?> ">
					</p>

					<input type="hidden" name="popup_form" value="1">

				</form>

				<div class="forgot-password">
					<?php echo sprintf(
						'<a href="%s">%s</a>',
						wp_lostpassword_url(),
						__('Glemt dit password?', 'bbh')
					); ?>
				</div>
				<div class="signup">
					<div class="flex-row jc-end ai-center">
						<?php if ($signupText): ?>
							<div class="signup-text">
								<?php echo wpautop($signupText); ?>
							</div>
						<?php endif; ?>
						<?php if ($cta): ?>
							<div class="cta">
								<a href="<?php echo $cta['url'] ?>" target="<?php echo $cta['target'] ?>" class="bbh-btn"><?php echo $cta['title'] ?></a>
								<br>
								<a class="bbh-btn" href="/min-konto">Registrer bruger</a>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php
}

add_action( 'wp_login_failed', 'bbh_prevent_login_fail_rediect' );  // hook failed login

function bbh_prevent_login_fail_rediect( $username ) {
   $referrer = $_SERVER['HTTP_REFERER'];  // where did the post submission come from?
   // if there's a valid referrer, and it's not the default log-in screen
   if ( !empty($referrer) && !strstr($referrer,'wp-login') && !strstr($referrer,'wp-admin') ) {
      wp_redirect( $referrer . '?login=failed' );  // let's append some information (login=failed) to the URL for the theme to use
      exit;
   }
}

/*********************************************
	Redirect for users regestratinon in
*********************************************/
function wpse_19692_registration_redirect() {
    return home_url( '/shop/' );
}
add_filter( 'registration_redirect', 'wpse_19692_registration_redirect' );
