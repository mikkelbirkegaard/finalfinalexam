<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

$bg = get_sub_field( 'theme_color' ) ? get_sub_field( 'theme_color' ).'-theme' : 'gray-theme';
$typewriter = get_sub_field( 'typewriter_heading' );
$bgImage = get_sub_field( 'bg_image' );
$bgImageId = $bgImage['ID'] ?? 0;
$bgVideo = get_sub_field( 'bg_video' );
$videoClass = $bgVideo ? 'has-video' : '';
$content = get_sub_field( 'subheading' );
$linkBoxesClass = get_sub_field( 'link_boxes' ) ? 'has-link-boxes' : '';

$classes = [$bg, $videoClass, $linkBoxesClass];
?>

<section id="section-<?php echo get_row_index(); ?>" class="flexible-inner-section bbh-inner-section fw-banner no-padding lazyload <?php echo implode($classes, " "); ?>"  data-animation="fadeIn" <?php lazyBg($bgImageId); ?>>
	<?php if ($bgVideo): ?>
		<div class="video-wrap">
			<video class="lazyload" playsinline autoplay muted loop data-src="<?php echo $bgVideo['url'] ?>" data-type="<?php echo  $bgVideo['mime_type'] ?>"></video>
		</div>
	<?php endif; ?>
	<div class="grid-container">
		<?php if ($typewriter && isset($typewriter['variations']) && count($typewriter['variations'])):
			$variations = array_map(function($el){
				return $el['text'];
			}, $typewriter['variations']);
			$variationsJson = htmlspecialchars(json_encode($variations), ENT_QUOTES, 'UTF-8');
			$lengthClass = mb_strlen($typewriter['text_base']) >= 20  ? 'long-text' : '';
			?>
			<h1 class="typewriter-text <?php echo $lengthClass ?>" data-base="<?php echo $typewriter['text_base'] || ''; ?>" data-variations="<?php echo $variationsJson ?>">
				<?php echo $typewriter['text_base'] ?>
				<span class="txt-rotate" data-period="2000" data-rotate="<?php echo $variationsJson ?>">
					<?php echo $variations[0]; ?>
				</span>

			</h1>
		<?php endif; ?>
		<div class="content">

			<?php echo $content; ?>
			<?php if( have_rows('buttons') ): ?>
				<div class="links-wrapper">
				    <?php while ( have_rows('buttons') ) : the_row();
				        $link = get_sub_field('link');
				        ?>
						<div class="link-container">
				        	<a class="bbh-btn" target="<?php echo $link['target'] ?>" href="<?php echo $link['url'] ?>"><?php echo $link['title'] ?></a>
						</div>
				    <?php endwhile; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php if( have_rows('link_boxes') ):

			?>
			<div class="link-boxes">
				<div class="flex-row ai-stretch jc-center">
				    <?php while ( have_rows('link_boxes') ) : the_row();
						$heading = get_sub_field( 'heading' );
						$content = get_sub_field( 'content' );
				        $link = get_sub_field('link');
						$tag = $link ? 'a' : 'div';
						$href = $link ? 'href="'.$link['url'].'"  target="'.$link['target'].'"' : '';
						$image = get_sub_field( 'bg_image' );
						$imageId = $imageId = $image ? $image['ID'] : false;

				        ?>
						<?php echo sprintf('<%s %s class="link-box %s" %s>',
							$tag,
							$href,
							$image ? 'lazyload has-image' : '',
							lazyBg($imageId, false)
						); ?>
							<?php if ($heading): ?>
								<h4 class="heading"><?php echo $heading ?></h4>
							<?php endif; ?>
							<div class="box-content">
								<?php echo wpautop($content) ?>
							</div>
							<?php if ($link): ?>
								<span class="link"><?php echo $link['title'] ?></span>
							<?php endif; ?>
						<?php echo sprintf('</%s>', $tag); ?>
				    <?php endwhile; ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</section>
