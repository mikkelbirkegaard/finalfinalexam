<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

$bg = get_sub_field( 'theme_color' ) ? get_sub_field( 'theme_color' ).'-theme' : 'gray-theme';
$classes = [$bg];

$icon = get_sub_field( 'icomoon' );
$content = get_sub_field( 'content' );

?>

<section id="section-<?php echo get_row_index(); ?>" class="flexible-inner-section bbh-inner-section c2-cta-banner has-padding <?php echo implode($classes, " "); ?>"  data-animation="fadeInUp">
    <div class="grid-container">
		<div class="flex-row wrap ai-center gutter-x3 section-row">
			<div class="left">
				<div class="flex-row jc-start ai-center">
					<?php if ($icon): ?>
						<span class="icon icomoon <?php echo $icon ?>"></span>
					<?php endif; ?>
					<div class="content">
						<?php echo $content; ?>
					</div>
				</div>
			</div>
			<div class="right">
				<?php
				// check if the repeater field has rows of data
				if( have_rows('cta_list') ): ?>
				    <div class="cta-list">
					    <?php while ( have_rows('cta_list') ) : the_row();
					        $content = get_sub_field('content');
							$icon = get_sub_field( 'icomoon' );
					        ?>
							<div class="flex-row jc-start ai-center cta-list-item">
								<?php if ($icon): ?>
									<span class="icon icomoon <?php echo $icon ?>"></span>
								<?php endif; ?>
								<div class="content">
									<?php echo $content; ?>
								</div>
							</div>
				        <?php endwhile; ?>
					</div>
				<?php endif; ?>
			</div>

		</div>
    </div>
</section>
