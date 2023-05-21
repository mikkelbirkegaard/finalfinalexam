<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

$name = get_field( 'name' ) ?: get_the_title();
$job_title = get_field('titel');
$thumb = get_post_thumbnail_id();
$phone = get_field( 'phone' );
$phonePre = get_field( 'phone_countrycode' );
$mobile = get_field( 'mobile' );
$mobilePre = get_field( 'mobile_countrycode' );
$email = get_field( 'email' );

?>

<div class="employee-card employee-<?php the_id(); ?>">
	<div class="employee-image lazyload <?php if (!get_the_post_thumbnail_url()) {
		echo 'placeholder-image';
	} ?>" data-bgset="<?php echo bbh_get_employee_image(get_the_id()); ?>">
	</div>
	<div class="employee-meta">
		<h3 class="name">
			<?php echo $name; ?>
		</h3>
		<div class="job-title">
			<?php echo $job_title; ?>
		</div>
		<?php if ($phone): ?>
			<div class="phone">
				<a href="tel:<?php echo $phone.$phonePre ?>">
					<?php echo sprintf(
						'%1$s %2$s%3$s',
						__('Tlf', 'bbh'),
						$phonePre ? $phonePre . ' ': '',
						implode(str_split($phone, 2), ' ')
					); ?>
				</a>
			</div>
		<?php endif; ?>
		<?php if ($mobile): ?>
			<div class="phone">
				<a href="tel:<?php echo $mobile.$mobilePre ?>">
					<?php echo sprintf(
						'%1$s %2$s%3$s',
						__('Mobil nr.', 'bbh'),
						$mobilePre ? $mobilePre . ' ': '',
						implode(str_split($mobile, 2), ' ')
					); ?>
				</a>
			</div>
		<?php endif; ?>
		<?php if ($email): ?>
			<div class="email">
				<a href="mailto:<?php echo $email ?>"><?php echo $email ?></a>
			</div>
		<?php endif; ?>
	</div>


</div>
