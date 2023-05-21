<!-- /*===============================================
=          made by Mikkel Christiansen           =
===============================================*/ -->


<?php
// No direct access, please
if (!defined('ABSPATH')) exit;

// Get the selected value of the ACF radio button field
$choose_quotes = get_sub_field('choose_quotes');
// offset text and image section starts here
$imageorvideo = get_sub_field('imageorvideo');
$image = get_sub_field('image');
$video = get_sub_field('video');
$quote = get_sub_field('text');
$title = get_sub_field('title');
$body_text = get_sub_field('body_text');
$buttons = get_sub_field('buttons');
$imageorvideo_right = get_sub_field('imageorvideo_right');
$image_right = get_sub_field('image_right');
$video_right = get_sub_field('video_right');
// Centered image and text section starts here
$imageorvideo_centered = get_sub_field('imageorvideo_centered');
$image_centered_left = get_sub_field('image_centered_left');
$video_centered_left = get_sub_field('video_centered_left');
$imageorvideo_centered_right = get_sub_field('imageorvideo_centered_right');
$image_centered_right = get_sub_field('image_centered_right');
$video_centered_right = get_sub_field('video_centered_right');
$text_center = get_sub_field('text_center');
$buttons_center = get_sub_field('buttons_center');

?>

<div class="quotes-sektion section-margin" >
    <section class="flexible-inner-section bbh-inner-section c2-quotes" data-animation="fadeIn">
        <div class="grid-container">
            <div class="row">
                <div class="col-sm-12">
                    <!-- /*===============================================
                    =         offset text and image section    =
                    ===============================================*/ -->
                        <?php if ( get_sub_field( 'choose_quotes' ) == two_images_offset ) : ?>
                            <div class="flexcontainer-offset flex-row">
                                <div class="left-image-guote">
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
                                    <div class="content">
                                        <?php if ($quote): ?>
                                            <p>"<?php echo $quote?>"</p>
                                        <?php endif; ?>

                                    </div>
                                </div>
                                <div class="right-image-text">
                                    <div class="content">
                                        <?php if ($title): ?>
                                            <h2><?php echo $title ?></h2>
                                        <?php endif; ?>
                                        <?php if ($body_text): ?>
                                            <?php echo $body_text ?>
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
                                    <div class="image-or-video-wrapper right">
                                        <!-- video is diterment here -->
                                        <?php if ($imageorvideo_right && !empty($video_right)): ?>
                                            <?php if ($video_right): ?>
                                                <video id="video-controls" class="video lazyload" src="<?php echo $video_right['url'] ?>" autoplay muted playsinline loop poster=""></video>
                                            <?php endif; ?>
                                        <!-- image is diterment here -->
                                    <?php elseif (!($imageorvideo) && !empty($image_right)): ?>
                                            <?php if ($image_right): ?>
                                                <img class="img lazyload" src="<?php echo $image_right['url'] ?>" alt="<?php echo $image_right['alt'] ?>">
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?> <!-- this is the end of offset text and image section  -->
                    <!-- /*===============================================
                    =         Centered image and text section    =
                    ===============================================*/ -->
                    <?php if ( get_sub_field( 'choose_quotes' ) == two_images_center ) : ?>
                        <div class="flexcontainer-centered">
                            <div class="image-wrapper flex-row">

                                <!-- the first image and video is determined here -->
                                <div class="first-image-video global-image-video">
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
                                <!-- the secound image and video is determined here -->
                                <div class="second-image-video global-image-video">
                                    <!-- video is diterment here -->
                                    <?php if ($imageorvideo_centered_right && !empty($video_centered_right)): ?>
                                        <?php if ($video_centered_right): ?>
                                            <video id="video-controls" class="video lazyload" src="<?php echo $video_centered_right['url'] ?>" autoplay muted playsinline loop poster=""></video>
                                        <?php endif; ?>
                                    <!-- image is diterment here -->
                                    <?php elseif (!($imageorvideo_centered_right) && !empty($image_centered_right)): ?>
                                        <?php if ($image_centered_right): ?>
                                            <img class="img lazyload" src="<?php echo $image_centered_right['url'] ?>" alt="<?php echo $image_centered_right['alt'] ?>">
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="content">
                                <p><?php echo $text_center ?></p>
                                <div class="section-button">
                                    <?php if (have_rows('buttons_center')) :
                                        while (have_rows('buttons_center')) : the_row(); ?>
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
                                                                    <span class="icon icon-red icomoon <?php echo $icomoon_secondary ?>"></span>
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
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</div><!-- This closing div should be at the bottom -->
