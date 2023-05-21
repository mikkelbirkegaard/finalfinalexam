<!-- /*===============================================
=          made by Mikkel Christiansen           =
===============================================*/ -->
<?php

// No direct access, please
if (!defined('ABSPATH')) exit;

$qoute = get_sub_field('qoute');
 ?>

<section class="flexible-inner-section bbh-inner-section article-quote" data-animation="fadeIn">
    <div class="grid-container">
        <div class="row">
            <div class="col-sm-12">
                <p class="qoute">
                    <?php echo $qoute ?>
                </p>
            </div>
        </div>
    </div>
</section>
