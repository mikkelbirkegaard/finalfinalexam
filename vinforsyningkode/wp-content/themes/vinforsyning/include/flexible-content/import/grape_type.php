<?php
$grape_sort = $_GET['sort'];
$term = get_term_by('slug', $grape_sort, 'pa_grape_type');
$grape_name = $term->name;
$grape_desc = $term->description;
$text = get_field('text', $term);
$img_or_video = get_field('img_or_video', $term);
$img = get_field('img', $term);
$video = get_field('video', $term);


?>

<section class="flexible-inner-section bbh-inner-section wineschool-header">
    <div class="grid-container">
        <div class="row">
            <div class="col-sm-12">
                <div class="wineschool-top-header">
                    <h1>Drueinfo</h1>
                    <div class="backLink-img">
                        <a class="backLink-wineschool" href="/vinskolen/druer"><span class="arrow-left"></span> Tilbage</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="flexible-inner-section bbh-inner-section wineschool-single-header single-producent">
    <div class="grid-container">
        <div class="row row-wineschool-headline">
            <div class="col-sm-12">
            <div class="wineschool-product-header">
                <div class="p-text">
                    <h1 class="product-title">
                        <?php echo $grape_name; ?>
                    </h1>
                </div>
            </div>
            </div>
        </div>
        <div class="row">
           <div class="col-sm-12">
               <div class="img-container">
                   <?php if ($img_or_video == true): ?>
                       <?php if ($img): ?>
                           <img class="lazyload" data-srcset="<?php echo $img['sizes']['large'] ?>" alt="<?php echo $img['alt'] ?>">
                       <?php endif; ?>
                   <?php else: ?>
                       <?php if ($video): ?>
                           <video src="<?php echo $video['url'] ?>" autoplay controls poster="">
                           </video>
                       <?php endif; ?>
                   <?php endif; ?>
                </div>
           </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="text2">
                <?php echo $text ?>
                </div>
            </div>
        </div>

    </div>
</section>
