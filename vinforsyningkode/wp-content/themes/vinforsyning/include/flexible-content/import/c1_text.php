<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

$bg = get_sub_field( 'theme_color' ) ? get_sub_field( 'theme_color' ).'-theme' : 'gray-theme';
$contentW = get_sub_field( 'content_width' ) ? get_sub_field( 'content_width' ).'-width' : 'normal-width';

$classes = [$bg, $contentW];

$content = get_sub_field( 'content' );


?>

<section id="section-<?php echo get_row_index(); ?>" class="flexible-inner-section bbh-inner-section c1-text has-padding <?php echo implode($classes, " "); ?>"  data-animation="fadeIn">
    <div class="grid-container">
        <div class="content">
        <?php if ($content): ?>
            <?php echo $content; ?>
        <?php endif; ?>
		</div>
    </div>
</section>
