<?php
/*===============================================
=               Vinforslag Ajax                =
===============================================*/

add_action('wp_ajax_wine_ajax', 'wine_ajax'); // Change names according to your form
add_action('wp_ajax_nopriv_wine_ajax', 'wine_ajax'); // Change names according to your form
/*-------------- Function to execute -------------*/
function wine_ajax(){
    // check for nonce security
    $nonce = $_POST['security'];
    if ( ! wp_verify_nonce( $nonce, 'wine-ajax-nonce' ) ){
        die;
    }
    $ajax_type = $_POST['ajax_type'];
    $search_value = $_POST['search-input'];
    $cpt_name = $_POST['cpt_name'];


    if ($ajax_type == 'alphaOrder') {

        $args = array(
            'orderby' => 'title',
            'order'  => 'ASC',
            'post_type' => $cpt_name,
            'posts_per_page' => -1,
            'post_status' => 'publish',
            's' => $search_value,

        );

        $the_query = new WP_Query( $args );
        $start_letter = false;

        ob_start();

        if( $the_query->have_posts() ) : ?>
            <div class="vinskolen-sub-container"><?php
                while( $the_query->have_posts() ): $the_query->the_post();
                $title = get_the_title();
                $first_letter = $title[0];
                $link = get_permalink();

                if ($start_letter != $first_letter) {
                    echo '<div class="vinskolen-sub-letter"><p>'. $first_letter .'</p></div>' ;
                    $start_letter = $first_letter;
                }
                        ?>
                        <div class="vinskolen-sub-single">
                        <p><a href="<?php echo $link ?>"><?php echo $title ?></a> <span class="arrow-right"></span></p>
                          </div>

                <?php endwhile; ?>
            </div>
            <?php
            wp_reset_postdata();
        else :
            echo '<div class="grid-container"><h4 class="no-matches">Din søgning gav desværre intet resultat</h4></div>';
        endif;
        $wine = ob_get_clean(); // Save case markup in output buffer
    }
    if ($ajax_type == 'catOrder') {
        $args = array(
            'orderby' => 'title',
            'order'  => 'ASC',
            'post_type' => $cpt_name,
            'posts_per_page' => 10,
            'post_status' => 'publish',
            's' => $search_value,

        );

        $the_query = new WP_Query( $args );
        ob_start();

        if( $the_query->have_posts() ) : ?>
            <div class="vinskolen-sub-container">
            <?php
             $tax = 'product_cat';
             $tax_terms = get_terms( $tax, 'orderby=name&order=ASC');
             $country_terms = get_terms( 'pa_country_att', 'orderby=name&order=ASC');

             if ($tax_terms) {
                 foreach ($tax_terms  as $tax_term) {
                     $args = array(
                         'post_type'         => $cpt_name,
                         "$tax"              => $tax_term->slug,
                         'post_status'       => 'publish',
                         'posts_per_page'    => -1,
                         's' => $search_value,
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
            <?php
            wp_reset_postdata();
        else :
            echo '<div class="no-matches-found"><h4 class="no-matches">Din søgning gav desværre intet resultat</h4></div>';
        endif;
        $wine = ob_get_clean(); // Save case markup in output buffer
    }

    if ($ajax_type == 'prodOrder') {

        ob_start();

        ?>
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
            'name__like' => $search_value,
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
            <?php
            wp_reset_postdata();

        $wine = ob_get_clean(); // Save case markup in output buffer
    }
    if ($ajax_type == 'grapeOrder') {

        ob_start();

        ?>
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
            'name__like' => $search_value,
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
            <?php
            wp_reset_postdata();

        $wine = ob_get_clean(); // Save case markup in output buffer
    }


    $send = array(
        'wine' => $wine,
    );
    wp_send_json($send); // Send json

    wp_die();
}
