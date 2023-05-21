<!-- /*===============================================
=          made by Mikkel Christiansen           =
===============================================*/ -->
<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

$content = get_sub_field( 'content' );
$btn = get_sub_field('btn');
$employees = get_sub_field( 'employee' );

?>

<section id="section-<?php echo get_row_index(); ?>" class="article-text flexible-inner-section article-section bbh-inner-section article-employee" >
	<div class="grid-container">
		<div class="employee-flex ">
			<div class="left">
		        <div class="content">
					<h3 class="title-employer-article"><?php echo $content ?></h3>
					<div class="link-container">
						<?php if ($btn): ?>
							<a target="<?php echo $btn['target'] ?>" href="<?php echo $btn['url'] ?>">
								<div class="bbh-btn primary-btn" onclick="location.href='<?php echo $btn['url']; ?>'">
									<p class="btn-white"><?php echo $btn['title'] ?></p>
								</div>
							</a>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<?php if ($employees): ?>
				<div class="right">
					<div class="employee-list">
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
