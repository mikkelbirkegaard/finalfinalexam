<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

// dependencies
wp_enqueue_script('slickjs');
wp_enqueue_style('slick');


$bg = 'white-theme';

$classes = [$bg];

?>

<section id="section-<?php echo get_row_index(); ?>" class="flexible-inner-section bbh-inner-section c2-content-slider has-padding <?php echo implode($classes, " "); ?>"  data-animation="fadeIn">
        <?php if( have_rows('slides') ): ?>
			<div class="grid-container">
				<div class="content-slider-wrap">
					<div class="content-slider">
			            <?php while ( have_rows('slides') ) : the_row();
							$image = get_sub_field( 'image' ) ? get_sub_field( 'image' )['ID'] : 0;
			                $content = get_sub_field( 'content' );
							$subHeading = get_sub_field( 'subheading' );
							$cta = get_sub_field( 'cta' );
			                ?>
							<div class="single-slide">
								<div class="inside-slide">
									<div class="flex-row">
										<div class="image-col">
                                            <?php if ($image): ?>
											    <img class="image-el lazyload lazypreload" <?php lazySrc($image); ?>>
                                            <?php endif; ?>

										</div>

										<div class="content-col">
											<div class="content">
												<?php if ($subHeading): ?>
													<h4 class="subheading"><?php echo $subHeading ?></h4>
												<?php endif; ?>
												<?php echo $content ?>
												<?php if ($cta): ?>
													<a href="<?php echo $cta['url'] ?>" target="<?php echo $cta['target'] ?>" class="cta-btn"><?php echo $cta['title'] ?></a>
												<?php endif; ?>
											</div>
										</div>
									</div>
								</div>
							</div>

			            <?php endwhile; ?>
					</div>
					<div class="dots-nav"></div>
				</div>
			</div>
        <?php endif; ?>
</section>
