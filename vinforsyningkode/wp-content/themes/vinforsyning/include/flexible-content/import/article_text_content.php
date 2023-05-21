<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;
$content = get_sub_field( 'content' );
$bg = get_sub_field( 'theme_color' ) ? get_sub_field( 'theme_color' ).'-theme' : 'white-theme';


?>

<section id="section-<?php echo get_row_index(); ?>" class="flexible-inner-section article-section bbh-inner-section article-text" >
	<div class="grid-container">
	    <div class="content">
			<?php if ($content): ?>
				<?php echo $content; ?>
			<?php endif; ?>
		</div>
	</div>
</section>
