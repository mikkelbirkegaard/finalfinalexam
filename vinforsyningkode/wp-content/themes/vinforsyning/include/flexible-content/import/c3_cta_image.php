<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

$bg = 'white-theme';
$image = get_sub_field( 'image' );
$classes = [$bg];


?>

<section id="section-<?php echo get_row_index(); ?>" class="flexible-inner-section bbh-inner-section c3-cta-img has-padding <?php echo implode($classes, " "); ?>"  data-animation="fadeIn">
    <div class="grid-container">
        <div class="background-box">
			<?php if ($image): ?>
				<div class="image-wrap">
					<img class="lazyload" alt="<?php echo $image['alt'] ?>" <?php lazySrc($image['ID']); ?>>
				</div>
			<?php endif; ?>
			<?php if( have_rows('cta_cols') ): ?>
				<div class="cta-cols">
					<div class="flex-row jc-center ai-top gutter-x2">

					    <?php while ( have_rows('cta_cols') ) : the_row();
					        $content = get_sub_field('content');
					        ?>
					        <div class="cta-col content">
								<?php echo $content ?>
							</div>
					    <?php endwhile; ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
    </div>
</section>
