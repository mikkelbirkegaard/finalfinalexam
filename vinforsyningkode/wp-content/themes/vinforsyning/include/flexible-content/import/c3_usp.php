<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<section id="section-<?php echo get_row_index(); ?>" class="flexible-inner-section bbh-inner-section c3-usp has-padding"  data-animation="fadeIn">
    <div class="grid-container">
        <?php if( have_rows('usp_list') ): ?>
			<div class="usp-list flex-row gutter-x2 ai-top jc-between">
	            <?php while ( have_rows('usp_list') ) : the_row();
	                $icon = get_sub_field( 'icomoon' ) ?: 'icon-st-question-mark-circle';
					$heading = get_sub_field( 'heading' );
					$text = get_sub_field( 'content' );
                    $lottie_path = get_stylesheet_directory_uri() . "/assets/json/".$icon.".json";
	                ?>
					<div class="single-usp">
						<?php if ($icon): ?>
                            <?php if (strpos($icon, 'lottie') === 0): ?>
                                <lottie-player class="icon" src="<?php echo $lottie_path ?>"
                                 background="transparent"  speed="1"  style="width: 100px; height: 100px;"  loop autoplay></lottie-player>
                            <?php else: ?>
                                <span class="icon icomoon <?php echo $icon ?>"></span>
                            <?php endif; ?>
						<?php endif; ?>
						<div class="content">
							<?php if ($heading): ?>
								<h4 class="heading"><?php echo $heading ?></h4>
							<?php endif; ?>
							<?php echo $text; ?>
						</div>
					</div>
				<?php endwhile; ?>
			</div>
        <?php endif; ?>
    </div>
</section>
