<!-- /*===============================================
=          made by Mikkel Christiansen           =
===============================================*/ -->
<?php
// No direct access, please
if (!defined('ABSPATH')) exit;

// Get the selected value of the ACF radio button field
$imageorvideo = get_sub_field('imageorvideo_article');
$video = get_sub_field('video');
$image = get_sub_field('image');
$title = get_the_title();
?>

<!-- Hero section size is determined here -->
<div class="article-hero-section" data-animation="fadeIn">
    <section class="flexible-inner-section bbh-inner-section bbh-hero-section">
        <div class="grid-container flex-container-hero">
            <div class="hero-width">
                <div >
                    <!-- Content on hero section -->
                    <div class="article-hero-content">
                        <!-- Background video or image -->
                        <div class="image-or-video">
                            <?php if ($imageorvideo && !empty($video)): ?>
                                <?php if ($video): ?>
                                    <video id="video-controls" class="video lazyload" src="<?php echo $video['url'] ?>" autoplay muted playsinline loop poster=""></video>
                                <?php endif; ?>
                            <?php elseif (!($imageorvideo) && !empty($image)): ?>
                                <?php if ($image): ?>
                                    <img class="image lazyload" src="<?php echo $image['url'] ?>" alt="<?php echo $image['alt'] ?>">
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        <div class="title-and-btn">
                            <?php if ($title): ?>
                                <h2><?php echo $title?></h2>
                            <?php endif; ?>
                            <?php if ($imageorvideo && !empty($video)): ?>
                                <?php if ($video): ?>
                                    <div id="video-btn-article" class="btn">
                                        <span class="icon-play1"></span>
                                        <p>Se video</p>
                                </div>
                                <?php endif; ?>
                            <?php elseif (!($imageorvideo) && !empty($image)): ?>
                                <?php if ($image): ?>

                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div id="video-hide-article" class="video-overlay-article">
        <?php if ($imageorvideo && !empty($video)): ?>
            <?php if ($video): ?>
                <div class="videocontainer">
                    <video id="popup-video" controls class="video-popup lazyload" src="<?php echo $video['url'] ?>" loop poster=""></video>
                    <div class="close">
                        <button class="btn-close" type="button" id="close-btn" data-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div><!-- This closing div should be at the bottom -->
