<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

$thumbId = get_post_thumbnail_id();
$imageAlt = get_post_meta($thumbId, '_wp_attachment_image_alt', TRUE);
?>



<div id="archive-post-<?php the_id(); ?>" <?php post_class('archive-post'); ?>>
	<div class="inside-archive-post">
		<div class="post-image-wrap">
			<a href="<?php the_permalink();?>">
				<?php if ($thumbId): ?>
					<div role="img" aria-label="<?php echo $imageAlt ?>" <?php lazyBg($thumbId); ?> class="post-image lazyload"></div>
				<?php else: ?>
					<div class="image-placeholder lazyload" data-bgset="<?php echo get_stylesheet_directory_uri() . '/assets/images/employee-placeholder2.png'; ?>" ></div>
				<?php endif; ?>

			</a>
		</div>
		<div class="post-content-wrap">
			<h2 class="post-title">
				<a href="<?php the_permalink();?>"><?php the_title(); ?></a>
			</h2>
			<div class="post-excerpt">
				<?php echo get_article_excerpt('article_sections', get_the_ID(), 40); ?>
			</div>
			<div class="more">
				<a href="<?php the_permalink(); ?>" class="read-more"><?php _e('Læs mere', 'bbh'); ?></a>
			</div>
		</div>
	</div>
</div>
