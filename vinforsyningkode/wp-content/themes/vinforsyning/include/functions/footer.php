<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;


/*========================================
=             footer semantics           =
========================================*/
add_action( 'generate_before_footer','bbh_footer_open_tag',1 );
function bbh_footer_open_tag(){
	$footerImage = get_field( 'footer_bg_img', 'theme_footer_settings' );
	$id = $footerImage ? $footerImage['ID'] : false;
    echo '<footer class="lazyload"'.LazyBg($id, false).'>';
}
add_action( 'generate_after_footer','bbh_footer_close_tag',50);
function bbh_footer_close_tag(){
    echo '</footer>';
}

/*=============================================
          = Custom footer bottom bar =
===============================================*/
add_action('generate_after_footer', 'bbh_custom_bottom_bar', 80);
function bbh_custom_bottom_bar() {
	$bottomMenu = get_field( 'nav_menu', 'theme_footer_settings' );
	$company_information = get_field( 'company_information', 'theme_footer_settings' );
	if(!$bottomMenu || $bottomMenu == 0){
		return;
	}
	?>

	<div class="footer-bottom-bar">
		<div class="grid-container flex-footer">
			<?php wp_nav_menu(array('depth' => 1, 'menu' => $bottomMenu)); ?>
			<div class="company-label">
                <?php if ($company_information): ?>
                    <p><?php echo $company_information; ?>&nbsp;<?php echo date('Y'); ?></p>
                <?php endif; ?>
			</div>
		</div>

	</div>

	<?php
};



/*=============================================
          = Add modal =
===============================================*/
add_action('wp_footer', 'bbh_video_modal', 10);
function bbh_video_modal() {
	?>
	<div id="bbh-modal">
		<div class="bbh-modal-box">
			<span class="close icon-st-close-01"></span>
			<div class="embed-responsive embed-responsive-16by9">
				<iframe src="" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen class="modal-frame"></iframe>
			</div>
		</div>
		<div id="bbh-modal--overlay">

		</div>
	</div>
	<?php
};

/*=============================================
  = add  call to action popups globally =
  made by Mikkel Christiansen
===============================================*/
add_action('generate_before_footer', 'bbh_popups', 10);
function bbh_popups() {
	?>
	<div id="call-to-action-popup" class="popup-overlay">
		<div class="popups-wrapper">
			<div class="close">
				<button class="btn-close" type="button" id="close-btn" data-dismiss="modal" aria-label="Close"></button>
			</div>
			<!-- video or images for the costumer form -->
			<div id="image-or-video-costumer" class="img-video-global">
				<?php if ( get_field( 'imageorvideo_first', 'options' ) ) : ?>
					<!-- check for video -->
					<?php
					$video_first = get_field( 'video_first', 'options' );
					if ( $video_first ) : ?>
						<video id="video-controls" class="video lazyload" src="<?php echo $video_first['url'] ?>" autoplay muted playsinline loop poster=""></video>
					<?php endif; ?>
					<!-- video check ends here -->
				<?php else: ?>
					<!-- check for images -->
					<?php
					$image_first = get_field( 'image_first', 'options' );
					if ( $image_first ) : ?>
						<img class="img lazyload" src="<?php echo $image_first['url'] ?>" alt="<?php echo $image_first['alt'] ?>">
					<?php endif; ?>
					<!-- image check ends here -->
				<?php endif; ?>
			</div>
			<!-- video or images for the get a call form -->
			<div id="image-or-video-call" class="img-video-global">
				<?php if ( get_field( 'imageorvideo', 'options' ) ) : ?>
					<!-- check for video -->
					<?php
					$video = get_field( 'video', 'options' );
					if ( $video ) : ?>
						<video id="video-controls" class="video lazyload" src="<?php echo $video['url'] ?>" autoplay muted playsinline loop poster=""></video>
					<?php endif; ?>
					<!-- video check ends here -->
				<?php else: ?>
					<!-- check for images -->
					<?php
					$image = get_field( 'image', 'options' );
					if ( $image ) : ?>
						<img class="img lazyload" src="<?php echo $image['url'] ?>" alt="<?php echo $image['alt'] ?>">
					<?php endif; ?>
					<!-- image check ends here -->
				<?php endif; ?>
			</div>
			<div class="group-forms">
				<div class="group-form-wrapper">
				  <div class="button-groups">
					<?php if ( $title_btn_become_customer = get_field( 'title_btn_become_customer', 'options' ) ) : ?>
					  <button id="customer-popup-btn" type="button" class="popup-btn active"><?php echo $title_btn_become_customer ?></button>
					<?php endif; ?>

					<?php if ( $title_btn_get_a_call = get_field( 'title_btn_get_a_call', 'options' ) ) : ?>
					  <button id="call-popup-btn" type="button" class="popup-btn"><?php echo $title_btn_get_a_call ?></button>
					<?php endif; ?>
				  </div>
				  <div id="customer-content" class="content-popup">
					<div class="form-text">
						<?php if ( $title_become_customer = get_field( 'title_become_customer', 'options' ) ) : ?>
						  <p class="title"><?php echo $title_become_customer ?></p>
						<?php endif; ?>
						<?php if ( $body_text_become_customer = get_field( 'body_text_become_customer', 'options' ) ) : ?>
							<div class="body-text">
								<?php echo $body_text_become_customer ?>
							</div>
						<?php endif; ?>
						<?php if ( $gf_become_customer = get_field( 'gf_become_customer', 'options' ) ) : ?>
							<?php echo do_shortcode('[gravityform id="' . $gf_become_customer . '" title="false" description="false" ajax="true"]'); ?>
						<?php endif; ?>
					</div>
				  </div>
				  <div id="call-content" class="content-popup">
					  <div class="form-text">
						  <?php if ( $title_get_a_call = get_field( 'title_get_a_call', 'options' ) ) : ?>
							<p class="title"><?php echo $title_get_a_call ?></p>
						  <?php endif; ?>
						  <?php if ( $body_get_a_call = get_field( 'body_get_a_call', 'options' ) ) : ?>
							  <div class="body-text">
							  	<?php echo $body_get_a_call ?>
							  </div>
						  <?php endif; ?>
						  <?php if ( $gf_get_a_call = get_field( 'gf_get_a_call', 'options' ) ) : ?>
							<?php echo do_shortcode('[gravityform id="' . $gf_get_a_call . '" title="false" description="false" ajax="true"]'); ?>
						  <?php endif; ?>
					  </div>
				  </div>
				</div>
			</div>
	  	</div>
	</div>
	<?php
};
