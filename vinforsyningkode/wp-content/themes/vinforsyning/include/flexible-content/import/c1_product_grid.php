<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

$bg = 'white-theme';

$classes = [$bg];

$content = get_sub_field( 'heading' );
$subHeading = get_sub_field( 'subheading' );
$btn = get_sub_field( 'cta_btn' );
$displayType = get_sub_field( 'display_type' );
if($displayType == 'new'){
	$shortCode = '[products columns="5" orderby="date" limit="10"]';
} else{
	$products = get_sub_field( 'products' );
	$shortCode = '[products ids="'.implode(',',$products).'" columns="5" orderby="post__in"]';
}
?>

<section id="section-<?php echo get_row_index(); ?>" class="flexible-inner-section bbh-inner-section c1-product-grid has-padding <?php echo implode($classes, " "); ?>"  data-animation="fadeIn">
    <div class="grid-container">
		<div class="flex-row wrap ai-top jc-between">

			<div class="content">
				<?php if ($subHeading): ?>
					<h4 class="sub-heading"><?php echo $subHeading; ?></h4>
				<?php endif; ?>
				<?php echo $content; ?>
			</div>
			<?php if ($btn): ?>
				<a href="<?php echo $btn['url'] ?>" target="<?php echo $btn['target'] ?>" class="bbh-btn"><?php echo $btn['title'] ?></a>
			<?php endif; ?>
		</div>
		<?php if ($shortCode): ?>
			<?php echo do_shortcode( $shortCode ); ?>
		<?php endif; ?>
    </div>
</section>
