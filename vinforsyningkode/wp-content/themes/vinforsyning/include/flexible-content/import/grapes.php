<?php
$your_ajax_cpt = 'wine';
$cpt = 'pa_grape_type';
 ?>
 <section class="flexible-inner-section bbh-inner-section wineschool-header">
     <div class="grid-container">
         <div class="row">
             <div class="col-sm-12">
                 <div class="wineschool-top-header">
                     <h1>Druer</h1>
                     <div class="backLink-img">
                         <a class="backLink-wineschool" href="/vinskolen"><span class="arrow-left"></span> Tilbage</a>

                     </div>
                 </div>
             </div>
         </div>
     </div>
 </section>
 <section class="flexible-inner-section bbh-inner-section vinskolen-sub-all">
     <div class="grid-container">
         <div class="row">
             <div class="col-sm-12">
                 <form action="<?php echo site_url() ?>/wp-admin/admin-ajax.php" method="POST" class="bbh-filter-down-form <?php echo $your_ajax_cpt ?>" id="content-filter">
            <div class="search-wrap">
                <input type="text" name="search-input" id="search-input" class="search-input" placeholder="Indtast søgeord">
            </div>

            <?php // nonce security; ?>
            <input type="hidden" name="security" id="<?php echo $your_ajax_cpt ?>-ajax-nonce" value="<?php echo wp_create_nonce( $your_ajax_cpt.'-ajax-nonce' ) ?>"/>
            <input type="hidden" name="ajax_type" value="grapeOrder">
            <input type="hidden" name="cpt_name" value="<?php echo $cpt ?>">
            <input type="hidden" name="action" value="<?php echo $your_ajax_cpt ?>_ajax">
            <input type="hidden" name="bbh-ajax-active" value="true" id="bbh-ajax-active">
        </form>
             </div>

         </div>
         <div class="row">
             <div class="col-sm-12">
                 <?php
                 $green_grapes = get_field('green_grapes','option');
                 foreach ($green_grapes as $green_grape) {
                     $greem_id = $green_grape->term_id;
                     $green_meta = get_term_meta($green_grape->term_id);
                     if (!$green_meta['grape_color']) {
                         add_metadata( 'term', $greem_id, 'grape_color', 'Grønne druer' );
                     }
                     if ($green_meta['grape_color'] != 'Grønne druer') {
                         delete_term_meta( $greem_id, 'grape_color', 'Grønne druer');
                         add_metadata( 'term', $greem_id, 'grape_color', 'Grønne druer' );
                     }
                 }
                 $red_grapes = get_field('red_grapes','option');
                 foreach ($red_grapes as $red_grape) {
                     $greem_id = $red_grape->term_id;
                     $red_meta = get_term_meta($red_grape->term_id);
                     if (!$red_meta['grape_color']) {
                         add_metadata( 'term', $greem_id, 'grape_color', 'Røde Druer' );
                     }
                     if ($red_meta['grape_color'] != 'Røde Druer') {
                         delete_term_meta( $greem_id, 'grape_color', 'Røde Druer');
                         add_metadata( 'term', $greem_id, 'grape_color', 'Røde Druer' );
                     }
                 }
                  ?>
                 <div id="content-response">
                 <div class="vinskolen-sub-container">
                 <?php


                 $taxs = get_taxonomies('pa_grape_type');
                 $terms = get_terms([
                     'taxonomy' => 'pa_grape_type',
                     'orderby' => 'meta_value',
                     'order' => 'ASC',
                     'hide_empty' => false,
                     'hierarchical' => false,
                     'parent' => 0,
                     'meta_query' => [[
                         'key' => 'grape_color',
                         'type' => 'CHAR',
                         ]],

                 ]);
                 $startColor = false;
                 foreach ($terms as $term){
                     $term_meta = get_term_meta($term->term_id);
                     $term_link = get_term_link($term->term_id);

                     $grapeColor = $term_meta['grape_color'][0];
                     $termSlug = $term->slug;

                     if ($startColor != $grapeColor) {
                         echo '<div class="vinskolen-sub-letter"><p>'. $grapeColor .'</p></div>' ;
                         $startColor = $grapeColor;
                     }
                     ?>
                     <div class="vinskolen-sub-single">
                         <p><a href="<?php echo '/druesort/?sort='.$termSlug ?>"><?php echo $term->name; ?><span class="arrow-right"></span></a></p>
                         <!-- <p><?php echo $description ?></p> -->
                     </div>

                     <?php
                 }
                  ?>
             </div>
              </div>
            </div>
         </div>
     </div>
 </section>
