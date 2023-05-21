<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;
$cols = get_sub_field( 'columns' );
$colsClass = "cols-{$cols}";
$images = get_sub_field( 'images' );
$imageCount = $images ? count($images) : 0;
$rows = $imageCount / $cols > 1 ? ceil($imageCount / $cols) : 0 ;

wp_enqueue_script('slickjs');
wp_enqueue_style('slick');

?>

<section id="section-<?php echo get_row_index(); ?>" class="flexible-inner-section article-section bbh-inner-section article-gallery" >
	<?php if ($images): ?>
		<div class="gallery <?php echo $colsClass; ?>" data-cols="<?php echo $cols ?>" data-rows="<?php echo $rows ?>" >
			<?php foreach ($images as $key => $image): ?>
				<div class="gallery-col">
					<div class="gallery-image lazyload" <?php lazyBg($image['ID']) ?>>
						<img alt="<?php echo $image['alt'] ?>" class="lazyload mobile-img" <?php lazySrc($image['ID']); ?>>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</section>
