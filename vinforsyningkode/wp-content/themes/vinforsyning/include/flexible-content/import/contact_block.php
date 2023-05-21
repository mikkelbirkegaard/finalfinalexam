<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

$flex_reverse = get_sub_field('flex_reverse');

// Left
$left_column = get_sub_field('left_column');
$image_left = get_sub_field( 'image' );
$bg_left = get_sub_field( 'theme_color_left' ) ? get_sub_field( 'theme_color_left' ).'-theme' : 'white-theme';
$content_left = get_sub_field( 'content_left' );
$contact_content_left = get_sub_field('contact_content');

// Right
$right_column = get_sub_field('right_column');
	if ($right_column == "img") {
		$image_right = get_sub_field( 'image' );
	}
	else {
		$bg_right = get_sub_field( 'theme_color_right' ) ? get_sub_field( 'theme_color_right' ).'-theme' : 'white-theme';
	}
$content_right = get_sub_field( 'content_right' );
$contact_content_right = get_sub_field('contact_content');

?>
<section id="section-<?php echo get_row_index(); ?>" class="flexible-inner-section bbh-inner-section contact-block"  data-animation="fadeInUp">
	<div class="contact-box-flex-container <?php if ($flex_reverse == true) { echo "reverse-flex-container"; } ?>">
		<div class="content-column-left <?php if ($left_column == "img"){ echo "lazyload bg-img"; } else { echo $bg_left; } ?>" data-bgset="<?php if ($left_column == "img"){ echo $image_left['sizes']['medium']; }?>">
			<div class="inside-content <?php if ($left_column == "contact_small"){ echo "contact-form-small"; } elseif($left_column == "contact_big"){ echo "contact-form-big"; } ?> ">
				<?php if ($left_column == "text"): ?>
					<?php echo $content_left ?>
				<?php elseif($left_column == "contact_small"): ?>
					<div class="contact-form-small">
						<?php echo $contact_content_left ?>
						<?php echo do_shortcode('[gravityform id="1" ajax="true"]'); ?>
					</div>
				<?php elseif($left_column == "contact_big"): ?>
					<div class="contact-form-big">
						<?php echo $contact_content_left ?>
						<?php echo do_shortcode('[gravityform id="2" ajax="true"]'); ?>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<div class="content-column-right <?php if ($right_column == "img"){ echo "lazyload bg-img"; } else { echo $bg_right; } ?>" data-bgset="<?php if ($right_column == "img"){ echo $image_right['sizes']['medium']; }?>">
			<div class="inside-content <?php if ($right_column == "contact_small"){ echo "contact-form-small"; } elseif($right_column == "contact_big"){ echo "contact-form-big"; } ?> ">
				<?php if ($right_column == "text"): ?>
					<?php echo $content_right ?>
				<?php elseif($right_column == "contact_small"): ?>
						<?php echo $contact_content_right ?>
						<?php echo do_shortcode('[gravityform id="1" ajax="true"]'); ?>
				<?php elseif($right_column == "contact_big"): ?>
						<?php echo $contact_content_right ?>
						<?php echo do_shortcode('[gravityform id="2" ajax="true"]'); ?>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
