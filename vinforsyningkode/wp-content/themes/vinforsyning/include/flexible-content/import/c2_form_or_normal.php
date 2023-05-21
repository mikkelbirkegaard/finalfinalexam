<?php
// No direct access, please
if (!defined('ABSPATH')) exit;

// Get the selected value of the ACF radio button field
$choose_section = get_sub_field('choose_section');
// image and become costumer section
$imageorvideo = get_sub_field('imageorvideo');
$image = get_sub_field('image');
$video = get_sub_field('video');
$body_text = get_sub_field('body_text');
$title = get_sub_field('title');
$gravity_form = get_sub_field('gf_become_customer');
// testimonial section
$imageorvideo_centered = get_sub_field('imageorvideo_centered');
$image_centered_left = get_sub_field('image_centered_left');
$video_centered_left = get_sub_field('video_centered_left');
$text_title = get_sub_field('text_title');
$body_text_section = get_sub_field('body_text_second');
$buttons = get_sub_field('buttons');

?>

<div class="testimonial-costumer-sektion section-margin">
    <section class="flexible-inner-section bbh-inner-section c2-becomecostumer-testimonials" data-animation="fadeIn">
        <!-- /*===============================================
        =         image and become costumer section   =
        ===============================================*/ -->
            <?php if ( get_sub_field( 'choose_section' ) == image_become_costumer ) : ?>
                <div class="flexcontainer-become-costumer flex-row">
                    <div class="left-image">
                        <!-- choose betweeen image or video to the left -->
                        <div class="image-or-video-wrapper left">
                            <!-- video is diterment here -->
                            <?php if ($imageorvideo && !empty($video)): ?>
                                <?php if ($video): ?>
                                    <video id="video-controls" class="video lazyload" src="<?php echo $video['url'] ?>" autoplay muted playsinline loop poster=""></video>
                                <?php endif; ?>
                            <!-- image is diterment here -->
                            <?php elseif (!($imageorvideo) && !empty($image)): ?>
                                <?php if ($image): ?>
                                    <img class="img lazyload" src="<?php echo $image['url'] ?>" alt="<?php echo $image['alt'] ?>">
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="right-text">
                        <div class="content">
                            <?php if ($title): ?>
                                <h2><?php echo $title ?></h2>
                            <?php endif; ?>
                            <?php if ($body_text): ?>
                                <?php echo $body_text ?>
                            <?php endif; ?>
                            <?php if ($gravity_form): ?>
                                <?php echo do_shortcode('[gravityform id="' . $gravity_form . '" title="false" description="false" ajax="true"]'); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?> <!-- this is the end of become customer section  -->
        <!-- /*===============================================
        =         testimonial section    =
        ===============================================*/ -->
        <?php if ( get_sub_field( 'choose_section' ) == image_text_section ) : ?>
            <div class="flexcontainer-normal-section flex-row">
                <div class="left-image">
                    <!-- choose betweeen image or video to the left -->
                    <div class="image-or-video-wrapper left">
                        <!-- video is diterment here -->
                        <?php if ($imageorvideo_centered && !empty($video_centered_left)): ?>
                            <?php if ($video_centered_left): ?>
                                <video id="video-controls" class="video lazyload" src="<?php echo $video_centered_left['url'] ?>" autoplay muted playsinline loop poster=""></video>
                            <?php endif; ?>
                        <!-- image is diterment here -->
                        <?php elseif (!($imageorvideo_centered) && !empty($image_centered_left)): ?>
                            <?php if ($image_centered_left): ?>
                                <img class="img lazyload" src="<?php echo $image_centered_left['url'] ?>" alt="<?php echo $image_centered_left['alt'] ?>">
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="right-text">
                    <div class="content">
                        <?php if ($text_title): ?>
                            <h2><?php echo $text_title ?></h2>
                        <?php endif; ?>
                        <?php if ($body_text_section): ?>
                            <?php echo $body_text_section ?>
                        <?php endif; ?>
                        <div class="section-button">
                            <?php if (have_rows('buttons')) :
                                while (have_rows('buttons')) : the_row(); ?>
                                    <?php if (get_sub_field('primary_secondary_btn') == 'primary') {
                                        $link = get_sub_field('link');
                                        $icomoon_icon = get_sub_field('icomoon_icon');
                                        $choose_icon = get_sub_field('choose_icon');
                                        ?>
                                        <?php if ($link): ?>
                                            <div class="link-container">
                                                <div class="bbh-btn primary-btn">
                                                <a class="btn-white" target="<?php echo $link['target'] ?>" href="<?php echo $link['url'] ?>"><?php echo $link['title'] ?></a>
                                                <?php if ($choose_icon && !empty($icomoon_icon)): ?>
                                                    <?php if ( $icomoon_icon ) : ?>
                                                        <span class="icon icomoon <?php echo $icomoon_icon ?>"></span>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php } else if (get_sub_field('primary_secondary_btn') == 'secondary') {
                                        $link_secondary = get_sub_field('link_secondary');
                                        $icomoon_secondary = get_sub_field('icomoon_secondary');
                                        $choose_icon_secondary = get_sub_field('choose_icon_secondary');
                                        ?>
                                        <?php if ($link_secondary): ?>
                                            <div class="link-container">
                                                <div class="bbh-btn secondary-btn red-btn">
                                                    <a class="btn-red" target="<?php echo $link_secondary['target'] ?>" href="<?php echo $link_secondary['url'] ?>"><?php echo $link_secondary['title'] ?></a>
                                                    <?php if ($choose_icon_secondary && !empty($icomoon_secondary)): ?>
                                                        <?php if ( $icomoon_secondary ) : ?>
                                                            <span class="icon icomoon <?php echo $icomoon_secondary ?>"></span>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php } ?>
                                <?php endwhile;
                            endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?> <!-- this is the end of become customer section  -->
    </section>
</div><!-- This closing div should be at the bottom -->
