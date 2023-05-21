<?php
$your_ajax_cpt = 'wine';
$cpt = 'product';

 ?>
 <section class="flexible-inner-section bbh-inner-section wineschool-header">
     <div class="grid-container">
         <div class="row">
             <div class="col-sm-12">
                 <div class="wineschool-top-header">
                     <h1>Info bank</h1>
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
            <input type="hidden" name="ajax_type" value="catOrder">
            <input type="hidden" name="cpt_name" value="<?php echo $cpt ?>">
            <input type="hidden" name="action" value="<?php echo $your_ajax_cpt ?>_ajax">
            <input type="hidden" name="bbh-ajax-active" value="true" id="bbh-ajax-active">
        </form>
             </div>

         </div>
         <div class="row">
             <div class="col-sm-12">
                 <div id="content-response">
                 <div class="vinskolen-sub-container">
                 <?php
         // Get current Category
         $get_current_cat = get_term_by('name', single_cat_title('',false), 'category');


         // List posts by the terms for a custom taxonomy of any post type
         $post_type = 'product';
         $tax = 'product_cat';
         $tax_terms = get_terms( $tax, 'orderby=name&order=ASC');
         $country_terms = get_terms( 'pa_country_att', 'orderby=name&order=ASC');

         if ($tax_terms) {
             foreach ($tax_terms  as $tax_term) {
                 $args = array(
                     'post_type'         => $post_type,
                     "$tax"              => $tax_term->slug,
                     'post_status'       => 'publish',
                     'posts_per_page'    => -1,
                     'meta_query' => [[
                         'key' => '_stock_status',
                         'value' => 'instock',
                         'compare' => '=',
                         ]],
                 );

                 $my_query = null;
                 $my_query = new WP_Query($args);

                 if( $my_query->have_posts() ) : ?>
                 <?php

                 $term_id = $tax_term->term_id;
                  ?>
                     <div class="vinskolen-sub-letter"><p><?php echo $tax_term->name; // Group name (taxonomy) ?></p></div>

                     <?php while ( $my_query->have_posts() ) : $my_query->the_post(); ?>
                            <div class="vinskolen-sub-single">
                                <p><a href="<?php echo get_permalink().'?vinskolen=true' ?>"><?php the_title(); ?> <span class="arrow-right"></span></a></p>
                            </div>
                     <?php endwhile; // end of loop ?>

                 <?php endif; // if have_posts()
                 wp_reset_query();

             } // end foreach #tax_terms
         } // end if tax_terms
     ?>
             </div>
              </div>
            </div>
         </div>
     </div>
 </section>
