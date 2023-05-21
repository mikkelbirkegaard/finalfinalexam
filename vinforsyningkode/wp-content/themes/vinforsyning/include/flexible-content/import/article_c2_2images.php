<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;
$gapX = get_sub_field('images_space_between');
?>

<section id="section-<?php echo get_row_index(); ?>" class="flexible-inner-section article-section bbh-inner-section article-images" >
    <?php if( have_rows( 'img_repeater') ): ?>
        <div class="article-image-container" style="grid-gap:<?php echo $gapX ?>%;">
            <?php while( have_rows( 'img_repeater') ): the_row();
                $image = get_sub_field( 'article_image' ); ?>
                <?php if ($image): ?>
                    <img src="<?php echo $image['url'] ?>">
                <?php endif; ?>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</section>
