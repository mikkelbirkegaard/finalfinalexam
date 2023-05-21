<?php
$about_wineschool = get_field('about_wineschool', 'options');
 ?>
<section class="flexible-inner-section bbh-inner-section wineschool-header">
    <div class="grid-container">
        <div class="row">
            <div class="col-sm-12">
                <div class="wineschool-top-header">
                    <h1>Om vinskolen</h1>
                    <div class="backLink-img">
                        <a class="backLink-wineschool" href="/vinskolen/indstillinger"><span class="arrow-left"></span> Tilbage</a>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="flexible-inner-section bbh-inner-section wineschool-single-header">
    <div class="grid-container">
        <div class="row">
            <div class="col-sm-12">
            <?php echo $about_wineschool ?>
            </div>
        </div>
    </div>
</section>
