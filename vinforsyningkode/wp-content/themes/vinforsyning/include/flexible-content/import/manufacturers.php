<?php
$your_ajax_cpt = 'wine';
$cpt = 'manufacturer';
 ?>
 <section class="flexible-inner-section bbh-inner-section wineschool-header">
     <div class="grid-container">
         <div class="row">
             <div class="col-sm-12">
                 <div class="wineschool-top-header">
                     <h1>Producenter</h1>
                     <div class="backLink-img">
                         <a class="backLink-wineschool" href="/vinskolen"><span class="arrow-left"></span> Tilbage</a>

                     </div>
                 </div>
             </div>
         </div>
     </div>
 </section>
 <section class="flexible-inner-section bbh-inner-section manufacturers vinskolen-sub-all">
     <div class="grid-container">

         <div class="row">
             <div class="col-sm-12">
                 <form action="<?php echo site_url() ?>/wp-admin/admin-ajax.php" method="POST" class="bbh-filter-down-form <?php echo $your_ajax_cpt ?>" id="content-filter">
            <div class="search-wrap">
                <input type="text" name="search-input" id="search-input" class="search-input" placeholder="Indtast søgeord">
            </div>

            <?php // nonce security; ?>
            <input type="hidden" name="security" id="<?php echo $your_ajax_cpt ?>-ajax-nonce" value="<?php echo wp_create_nonce( $your_ajax_cpt.'-ajax-nonce' ) ?>"/>
            <input type="hidden" name="ajax_type" value="prodOrder">
            <input type="hidden" name="cpt_name" value="<?php echo $cpt ?>">
            <input type="hidden" name="action" value="<?php echo $your_ajax_cpt ?>_ajax">
            <input type="hidden" name="bbh-ajax-active" value="true" id="bbh-ajax-active">
        </form>
             </div>

         </div>
         <div class="row">
             <div class="col-sm-12">
                 <?php
                  $country_terms = get_terms( 'pa_country_att', 'orderby=name&order=ASC');
                  foreach ($country_terms as $terms) {
                      $term_id = $terms->term_id;
                      $term_name = $terms->name;
                      $prod_froms = get_field('prod_from','pa_country_att_'. $term_id);
                      if ($prod_froms) {
                      foreach ($prod_froms as $prod) {
                          $manu_id = $prod->term_id;
                          $termSlug = $prod->slug;
                          $manu = $prod->name;
                          $prod_meta = get_term_meta($manu_id);
                          $link = get_term_link($prod);

                          if (!$prod_meta['prod_from']) {
                              add_metadata( 'term', $manu_id, 'prod_from', $term_name );
                          }
                          if ($prod_meta['prod_from'] != $term_name) {
                              delete_term_meta( $manu_id, 'prod_from', $term_name);
                              add_metadata( 'term', $manu_id, 'prod_from', $term_name );
                          }
                      }
                      }
                  }
                   ?>
                 <div id="content-response">
                     <div class="vinskolen-sub-container">
                                         <?php
                                         $taxs = get_taxonomies('manufacturer');
                                         $terms = get_terms([
                                             'taxonomy' => 'manufacturer',
                                             'orderby' => 'meta_value',
                                             'order' => 'ASC',
                                             'hide_empty' => false,
                                             'hierarchical' => false,
                                             'parent' => 0,
                                             'meta_query' => [[
                                                 'key' => 'prod_from',
                                                 'type' => 'CHAR',
                                                 ]],

                                         ]);
                                         $startCountry = false;
                                         foreach ($terms as $term){
                                             $term_meta = get_term_meta($term->term_id);

                                             $country = $term_meta['prod_from'][0];
                                             $termSlug = $term->slug;

                                             $description = $term->description;
                                             $term_link = get_term_link($term->term_id);
                                             if ($startCountry != $country) {
                                                 echo '<div class="vinskolen-sub-letter"><p>'. $country .'</p></div>' ;
                                                 $startCountry = $country;
                                             }
                                             ?>
                                             <div class="vinskolen-sub-single">
                                                 <p><a href="<?php echo $term_link.'?vinskolen=true' ?>"><?php echo $term->name; ?><span class="arrow-right"></span></a></p>
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
