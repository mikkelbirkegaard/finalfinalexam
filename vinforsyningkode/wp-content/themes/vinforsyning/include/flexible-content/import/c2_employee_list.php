<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

$bg = get_sub_field( 'theme_color' ) ? get_sub_field( 'theme_color' ).'-theme' : 'gray-theme';

$classes = [$bg];

$content = get_sub_field( 'content' );
$employees = get_sub_field( 'employees' );

?>

<section id="section-<?php echo get_row_index(); ?>" class="flexible-inner-section bbh-inner-section c2-employee-list has-padding <?php echo implode($classes, " "); ?>"  data-animation="fadeInUp">
    <div class="grid-container">
		<div class="section-row flex-row ai-center wrap gutter-x3">
			<div class="left">
		        <div class="content">
					<?php echo $content; ?>
				</div>
			</div>
			<?php if ($employees): ?>
				<div class="right">
					<div class="employee-list flex-row wrap gutter-x2 ai-top jc-start">
					<?php foreach ($employees as $post):
						setup_postdata($post);
						if (file_exists(get_stylesheet_directory() . '/template-parts/employee-card.php')) {
							include( get_stylesheet_directory() . '/template-parts/employee-card.php');
						}
					endforeach; ?>
					<?php wp_reset_postdata(); ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
    </div>
</section>
