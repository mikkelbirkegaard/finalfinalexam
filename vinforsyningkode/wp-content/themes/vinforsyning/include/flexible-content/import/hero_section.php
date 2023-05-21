<!-- /*===============================================
=          made by Mikkel Christiansen           =
===============================================*/ -->
<?php
// No direct access, please
if (!defined('ABSPATH')) exit;

// Get the selected value of the ACF radio button field
$value = get_sub_field('section_size');
$imageorvideo = get_sub_field('imageorvideo');
$bg_video = get_sub_field('videohero');
$bg_image = get_sub_field('image');
$title = get_sub_field('title_hero');
$body_text = get_sub_field('body_text');
$overlay = get_sub_field('overlay');


// Construct the CSS class name based on the selected value
$class_name = 'section-size-' . esc_attr($value);
?>

<!-- Hero section size is determined here -->
<div class="hero-section <?php echo $class_name; ?>" >

    <!-- Background video or image -->
    <?php if ($imageorvideo && !empty($bg_video)): ?>
        <?php if ($bg_video): ?>
            <video id="video-controls" class="bg-video lazyload" src="<?php echo $bg_video['url'] ?>" autoplay muted playsinline loop poster=""></video>
        <?php endif; ?>
    <?php elseif (!($imageorvideo) && !empty($bg_image)): ?>
        <?php if ($bg_image): ?>
            <img class="bg-img lazyload" src="<?php echo $bg_image['url'] ?>" alt="<?php echo $bg_image['alt'] ?>">
        <?php endif; ?>
    <?php endif; ?>
    <!-- Overlay on image or videos -->
    <?php if ( ( $overlay ) == twenty ) : ?>
     <div class="overlay overlay-twenty"></div>
    <?php endif; ?>
        <?php if ( ( $overlay ) == forty ) : ?>
     <div class="overlay overlay-forty"></div>
    <?php endif; ?>
        <?php if ( ( $overlay ) == sixty ) : ?>
     <div class="overlay overlay-sixty"></div>
    <?php endif; ?>
        <?php if ( ( $overlay ) == eighty ) : ?>
     <div class="overlay overlay-eighty"></div>
    <?php endif; ?>

    <section class="flexible-inner-section bbh-inner-section bbh-hero-section">
        <div class="grid-container flex-container-hero">
            <div class="hero-width">
                <div class="col-sm-12">
                    <!-- Content on hero section -->
                    <div class="hero-content">
                        <div class="text-content">
                            <?php if ($title): ?>
                                <h1><?php echo $title ?></h1>
                            <?php endif; ?>
                            <?php if ($body_text): ?>
                                <?php echo $body_text ?>
                            <?php endif; ?>
                        </div>
                        <div class="hero-button">
                            <?php if (have_rows('buttons')) :
                                while (have_rows('buttons')) : the_row(); ?>
                                    <?php if (get_sub_field('primary_secondary_btn') == 'primary') {
                                        $link = get_sub_field('link');
                                        $icomoon_icon = get_sub_field('icomoon_icon');
                                        $choose_icon = get_sub_field('choose_icon');
                                        ?>
                                        <?php if ($link): ?>
                                            <div class="link-container">
                                                <a target="<?php echo $link['target'] ?>" href="<?php echo $link['url'] ?>">
                                                    <div class="bbh-btn primary-btn" onclick="location.href='<?php echo $link['url']; ?>'">
                                                        <p class="btn-white"><?php echo $link['title'] ?></p>
                                                        <?php if ($choose_icon && !empty($icomoon_icon)): ?>
                                                            <?php if ( $icomoon_icon ) : ?>
                                                                <span class="icon icomoon <?php echo $icomoon_icon ?>"></span>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                    </div>
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    <?php } else if (get_sub_field('primary_secondary_btn') == 'secondary') {
                                        $link_secondary = get_sub_field('link_secondary');
                                        $icomoon_secondary = get_sub_field('icomoon_secondary');
                                        $choose_icon_secondary = get_sub_field('choose_icon_secondary');
                                        ?>
                                        <?php if ($link_secondary): ?>
                                            <div class="link-container">
                                                <a target="<?php echo $link_secondary['target'] ?>" href="<?php echo $link_secondary['url'] ?>">
                                                    <div class="bbh-btn secondary-btn" onclick="location.href='<?php echo $link_secondary['url']; ?>'">
                                                        <p class="btn-white"><?php echo $link_secondary['title'] ?></p>
                                                        <?php if ($choose_icon_secondary && !empty($icomoon_secondary)): ?>
                                                            <?php if ( $icomoon_secondary ) : ?>
                                                                <span class="icon icomoon <?php echo $icomoon_secondary ?>"></span>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                    </div>
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    <?php } ?>
                                <?php endwhile;
                            endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div><!-- This closing div should be at the bottom -->
