<?php
$terms_wineschool = get_field('terms_wineschool', 'options');
 ?>
<section class="flexible-inner-section bbh-inner-section wineschool-header">
    <div class="grid-container">
        <div class="row">
            <div class="col-sm-12">
                <div class="wineschool-top-header">
                    <h1>Vilkår</h1>
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
            <?php echo $terms_wineschool ?>
            </div>
        </div>
    </div>
</section>
