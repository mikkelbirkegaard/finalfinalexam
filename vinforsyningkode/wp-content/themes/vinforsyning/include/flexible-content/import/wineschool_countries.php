<?php
$your_ajax_cpt = 'wine';
$cpt = 'pa_country_att';
 ?>
 <?php get_header(); ?>
 <section class="flexible-inner-section bbh-inner-section wineschool-header">
     <div class="grid-container">
         <div class="row">
             <div class="col-sm-12">
                 <div class="wineschool-top-header">
                     <h1>Lande</h1>
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
                 <form action="<?php echo site_url() ?>/wp-admin/admin-ajax.php" method="POST" class="bbh-filter-down-form <?php echo $your_ajax_cpt ?>" id="content-lex-filter">
            <div class="search-wrap">
                <input type="text" name="search-input" id="search-input" class="search-input" placeholder="Indtast søgeord">
            </div>

            <?php // nonce security; ?>
            <input type="hidden" name="security" id="<?php echo $your_ajax_cpt ?>-ajax-nonce" value="<?php echo wp_create_nonce( $your_ajax_cpt.'-ajax-nonce' ) ?>"/>
            <input type="hidden" name="ajax_type" value="alphaOrder">
            <input type="hidden" name="cpt_name" value="<?php echo $cpt ?>">
            <input type="hidden" name="action" value="<?php echo $your_ajax_cpt ?>_ajax">
            <input type="hidden" name="bbh-ajax-active" value="true" id="bbh-ajax-active">
        </form>
             </div>

         </div>
         <div class="row">
             <div class="col-sm-12">
                 <div id="content-response">
                  <?php // The Query
                  $args = array(
                  'post_type' => 'mini-lex',
                  'posts_per_page' => -1,
                  'order' => ASC,
                  );
                  $the_query = new WP_Query( $args );
                  $start_letter = false;
                  if ( $the_query->have_posts() ) {
                  // The Loop
                  ?>
                  <div class="vinskolen-sub-container">
                  <?php
                  while ( $the_query->have_posts() ) {
                      $the_query->the_post();
                      $post_title = get_the_title();
                      $permaLink = get_permalink();
                      $first_letter = $post_title[0];
                      if ($start_letter != $first_letter) {
                          echo '<div class="vinskolen-sub-letter"><p>'. $first_letter .'</p></div>' ;
                          $start_letter = $first_letter;
                      }
                              ?>
                              <div class="vinskolen-sub-single">
                              <p><a href="<?php echo $permaLink ?>"><?php echo $post_title ?> <span class="arrow-right"></span></a></p>
                                </div>
                              <?php
                      }
                 } else {
                  // no posts found
                 }
                 /* Restore original Post Data */
                  wp_reset_postdata(); ?>
                    </div>
                </div>
             </div>
         </div>
     </div>
 </section>
