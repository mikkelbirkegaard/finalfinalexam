<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

$bg = get_sub_field( 'theme_color' ) ? get_sub_field( 'theme_color' ).'-theme' : 'gray-theme';
$video = get_sub_field( 'video' );
$file = get_sub_field( 'video_file' );
$embedField = get_sub_field( 'video_embed' );
$embedSrc = get_embed_video_src($embedField);
$hasVideo = $video ? 'has-video' : '';
$classes = [$bg, $hasVideo];
$heading = get_sub_field( 'heading' );
$content = get_sub_field( 'content' );
$imageId = get_sub_field( 'image' ) ? get_sub_field( 'image' )['ID'] : false;



?>

<section id="section-<?php echo get_row_index(); ?>" class="flexible-inner-section bbh-inner-section c2-video-text has-padding <?php echo implode($classes, " "); ?>"  data-animation="fadeInUp">
    <div class="grid-container">
		<div class="flex-row jc-start ai-bottom">
			<div class="image-col">
				<?php if ($heading): ?>
					<div class="heading content">
						<?php echo $heading ?>
					</div>
				<?php endif; ?>
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
			<div class="content-col">
				<div class="content">
					<?php echo $content; ?>
				</div>
			</div>
		</div>
    </div>
</section>
