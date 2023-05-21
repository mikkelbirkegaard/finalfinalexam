<?php
$user_id = get_current_user_id();
// $vinkort = bbh_find_vinskole_on_customer_id($user_id);
$customer_id = get_field('customer_number', 'user_'.$user_id);
$vinkort_id = get_field('vinkort_id', 'user_'.$user_id);
$vinkort = get_page_by_title($vinkort_id, OBJECT, 'vinkort');
$vinskolen_vinkort = get_field('vinskolen_vinkort', $vinkort);
// $vinskolen_vinkort = false;

 ?>
 <section class="flexible-inner-section bbh-inner-section wineschool-header">
     <div class="grid-container">
         <div class="row">
             <div class="col-sm-12">
                 <div class="wineschool-top-header">
                     <h1>Vinkort</h1>
                     <div class="backLink-img">
                         <a class="backLink-wineschool" href="/vinskolen"><span class="arrow-left"></span> Tilbage</a>
                     </div>
                 </div>
             </div>
         </div>
     </div>
 </section>
 <section class="flexible-inner-section bbh-inner-section winecard">
     <div class="grid-container">
<?php if ($vinskolen_vinkort): ?>
         <?php foreach ($vinskolen_vinkort as $vinSection): ?>
             <?php
             $headline = $vinSection['vinskolen_headline'];
             $vine = $vinSection['vinskolen_featured'];
              ?>
              <div class="vinskolen-grid-headline">
                  <p><strong><?php echo $headline ?></strong></p>
              </div>
             <div class="vinskolen-grid">
                 <?php if ($vine) {
                     echo do_shortcode('[products ids="'.implode(', ',$vine).'" columns="4"]');
                 } ?>
             </div>
         <?php endforeach; ?>
         <?php else: ?>
            <div class="winecard-unavailable">
                <div class="unavailable-headline-box">

                </div>
                <div class="unavailable-grid">
                    <div class="box">
                        <div class="box-text-1">

                        </div>
                        <div class="box-text-2">

                        </div>
                    </div>
                    <div class="box">
                        <div class="box-text-1">

                        </div>
                        <div class="box-text-2">

                        </div>
                    </div>
                    <div class="box">
                        <div class="box-text-1">

                        </div>
                        <div class="box-text-2">

                        </div>
                    </div>
                    <div class="box">
                        <div class="box-text-1">

                        </div>
                        <div class="box-text-2">

                        </div>
                    </div>
                </div>
            </div>
         <?php endif; ?>
     </div>
 </section>
