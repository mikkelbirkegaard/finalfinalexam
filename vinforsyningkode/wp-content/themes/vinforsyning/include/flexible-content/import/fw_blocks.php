<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

$bg = get_sub_field( 'theme_color' ) ? get_sub_field( 'theme_color' ).'-theme' : 'white-theme';
$cta = get_sub_field( 'show_cta' );
$hasCta = $cta ? 'has-cta' : '';
$ctaContent = get_sub_field( 'cta_text' );
$ctaLinks = get_sub_field( 'cta_links' );
$ctaLinkCount = $ctaLinks && count($ctaLinks) > 1 ? 'multiple' : 'single';
$mobileOrder = get_sub_field( 'order_mobile' );
$desktopOrder = get_sub_field( 'order_desktop' );
$classes = [$bg, $hasCta, 'mobile-'.$mobileOrder, 'desktop-'.$desktopOrder];
$content = get_sub_field( 'content' );
$image = get_sub_field( 'image' );
$imageId = $image ? $image['ID'] : false;
$imageSrc = $image ? $image['sizes']['large'] : '';
$showList = get_sub_field( 'show_list' );
$img_size = get_sub_field('img_size');

?>
<section id="section-<?php echo get_row_index(); ?>" class="flexible-inner-section bbh-inner-section fw-text-image-blocks no-padding <?php echo implode($classes, " "); ?>"  data-animation="fadeInUp">
	<div class="flex-row jc-stretch ai-stretch">

		<div class="content-col">
			<div class="inside-content">
				<div class="content">
					<?php echo $content; ?>
					<?php if ($showList && have_rows('bullet_list')): ?>
						<div class="bullet-list">
						    <?php while ( have_rows('bullet_list') ) : the_row();
						        $heading = get_sub_field( 'heading' );
								$bulletContent = get_sub_field( 'content' );
						        ?>
						        <div class="bullet-list-item">
									<?php echo $bulletContent; ?>
								</div>
						    <?php endwhile; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<div class="image-col lazyload <?php if ($img_size == true) { echo 'contain'; } ?>" data-bgset="<?php echo $imageSrc ?>">
			<?php if ($imageId): ?>
				<img alt="<?php echo $image['alt'] ?>" data-sizes="auto" class="mobile-img lazyload" <?php lazySrc($imageId); ?>>
			<?php endif; ?>
			<?php if ($cta): ?>
				<div class="cta-box">
					<div class="content">
						<?php echo $ctaContent; ?>
					</div>
					<?php if (have_rows('cta_links')): ?>
						<div class="cta-links <?php echo $ctaLinkCount; ?>">
							<?php while( have_rows('cta_links')): the_row();
								$link = get_sub_field( 'link' );
								?>
								<a href="<?php echo $link['url'] ?>" target="<?php echo $link['target'] ?>" class="link"><?php echo $link['title'] ?></a>
							<?php endwhile; ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>

	</div>
</section>
