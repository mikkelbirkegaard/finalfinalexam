<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

$video = get_sub_field( 'video' );
$file = get_sub_field( 'video_file' );
$embedField = get_sub_field( 'video_embed' );
$embedSrc = get_embed_video_src($embedField);
$imageId = get_sub_field( 'image' ) ? get_sub_field( 'image' )['ID'] : false;



?>

<section id="section-<?php echo get_row_index(); ?>" class="flexible-inner-section article-section bbh-inner-section article-video" >
	<div class="flex-row jc-stretch ai-bottom">
		<div class="image-col">
			<div class="image">
				<div class="image-wrap">
					<?php if ($video == 'file'): ?>
						<?php echo get_video_player($file, $imageId); ?>
					<?php elseif($video == 'embed'): ?>
						<div class="image-el lazyload" <?php lazyBg($imageId); ?>>
						</div>
						<?php if ($embedSrc):
							?>
							<div class="video-trigger" data-iframe-src="<?php echo $embedSrc ?>"></div>
						<?php endif; ?>
					<?php elseif($imageId): ?>
						<div class="image-el lazyload" <?php lazyBg($imageId); ?>>
						</div>
					<?php endif; ?>

				</div>
			</div>
		</div>
    </div>
</section>
