<?php
$value = get_sub_field('section_size');
$imageorvideo = get_sub_field('imageorvideo');
$video = get_sub_field('video');
$image = get_sub_field('image');
$customer_testimonial = get_sub_field('customer_testimonial');
$text_name = get_sub_field('text_name');
$firm_logo = get_sub_field('firm_logo');



 ?>
<section class="flexible-inner-section bbh-inner-section testimonial-wrapper flex-row" data-animation="fadeIn">
    <div class="left-image">
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
    <div class="testimonials-content">
        <div class="content">
            <?php if ($customer_testimonial): ?>
                <p class="costumer-say-text">kunden siger</p>
                <p class="testimonitals-text"><?php echo $customer_testimonial ?></p>
            <?php endif; ?>
            <?php if ($text_name): ?>
                <p><?php echo $text_name ?></p>
            <?php endif; ?>
            <?php if ($firm_logo): ?>
                <img class="img lazyload" src="<?php echo $firm_logo['url'] ?>" alt="<?php echo $firm_logo['alt'] ?>">
            <?php endif; ?>
        </div>
    </div>
</section>
