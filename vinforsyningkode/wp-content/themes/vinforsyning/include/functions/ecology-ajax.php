<?php
add_action('wp_ajax_ecology_ajax', 'ecology_ajax'); // Change names according to your form
add_action('wp_ajax_nopriv_ecology_ajax', 'ecology_ajax'); // Change names according to your form
/*-------------- Function to execute -------------*/
function ecology_ajax(){
    // check for nonce security
    $nonce = $_POST['security'];
    if ( ! wp_verify_nonce( $nonce, 'ecology-ajax-nonce' ) ){
        die;
    }

    function danish_amount($number){
        $danish_number = number_format($number,2,',','.');
        return $danish_number;
    }

    $user_id = get_current_user_id();
    $customer_id = get_field('customer_number', 'user_'.$user_id);
    $start_date = date('d-m-Y', strtotime($_POST['start-date'] ));
    $end_date = date('d-m-Y', strtotime($_POST['end-date'] ));
    if ($end_date == "01-01-1970") {
       $end_date = date("d-m-Y");
    }
    $period = new DatePeriod(
     new DateTime($start_date),
     new DateInterval('P1D'),
     new DateTime($end_date)
    );
    $date_array = array();
    foreach ($period as $key => $value) {
     $date_array[] = $value->format('d-m-Y');
    }
    $date_array[] = $end_date;

    $args = array(
        'post_type' => 'invoice',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => 'invoice_user_id',
                'value' => $customer_id,
                'compare' => '='
            ),
            array(
                'key' => 'invoice_date',
                'value' => $date_array,
                'compare' => 'IN',
            ),
        ),
    );
    ?>
    <div class="invoices">
            <table class="table table-bordered table-dark">
                <thead class="thead-red">
                    <tr>
                      <th class="th-invoice-date" scope="col">Dato<span></span></th>
                      <th scope="col">Faktura-nummer<span></span></th>
                      <th scope="col">Samlet <?php echo $_POST['unit'] ?> på faktura<span></span></th>
                      <th scope="col">Varer der ikke indgår i beregningen af økologiprocenten<span></span></th>
                      <th scope="col">Samlet <?php echo $_POST['unit'] ?> af økologiske råvarer<span></span></th>
                    </tr>
                </thead>
    <?php
    $the_query = new WP_Query( $args );
    if ($the_query->have_posts()): ?>
            <tbody id="ecology-table">
            <?php
            $unit = false;
            $total = 0;
            $non_food_total = 0;
            $eco_total = 0;
            if ($_POST['unit'] == "vægt"){ $unit = true; }else{ $unit = false; }

            ?>
    <?php while($the_query->have_posts()): $the_query->the_post();
            $invoice_user_id = get_field('invoice_user_id');
            // Extra check
            if ($invoice_user_id == $customer_id ) {
                $invoice_date = get_field('invoice_date');
                $dateYMD = date('Ymd', strtotime($invoice_date));
                $invoice_appendix_number = get_field('invoice_appendix_number');
                $total_weight = get_field('samlet_litre');
                $total_value = get_field('_samlet_vaerdi');
                $non_food_weight = get_field('ej_fodevare_vaegt');
                $non_food_value = get_field('ej_fodevare_vaerdi');
                $eco_weight = get_field('liter_okologi');
                $eco_value = get_field('vaerdi_af_okologiske_varer');
                if ($unit == true) {
                    if ((float)$total_weight) {
                        $total = $total+(float)$total_weight;
                    }
                    if ((float)$non_food_weight) {
                        $non_food_total = $non_food_total+(float)$non_food_weight;
                    }
                    if ((float)$eco_weight) {
                        $eco_total = $eco_total+(float)$eco_weight;
                    }
                }else{
                    if ((float)$total_value) {
                        $total = $total+(float)$total_value;
                    }
                    if ((float)$non_food_value) {
                        $non_food_total = $non_food_total+(float)$non_food_value;
                    }
                    if ((float)$eco_value) {
                        $eco_total = $eco_total+(float)$eco_value;
                    }
                }
                ?>
                <tr>
                  <td><span class="hide"><?php echo $dateYMD ?></span><?php echo $invoice_date; ?></td>
                  <td><?php echo $invoice_appendix_number.", Østjysk Vinforsyning"; ?></td>
                  <td><?php if ($unit == true){ echo danish_amount($total_weight); }else{ echo danish_amount($total_value); } ?></td>
                  <td><?php if ($unit == true){ echo danish_amount($non_food_weight); }else{ echo danish_amount($non_food_value); } ?></td>
                  <td><?php if ($unit == true){ echo danish_amount($eco_weight); }else{ echo danish_amount($eco_value); } ?></td>
                </tr>
            <?php } ?>
        <?php endwhile;
        wp_reset_postdata(); ?>
            </tbody>
            <?php
            $total_minus_non = $total-$non_food_total;
            $ecology_percent = $eco_total/$total_minus_non*100;
            if ($ecology_percent > 100 || $ecology_percent < 0) { $ecology_percent = 0; } ?>
            <input type="hidden" id="ajax-ecology-percentage" value="<?php echo number_format((float)$ecology_percent, 2, ',', '') ?>">
        <?php endif; ?>
        <tfoot class="tr-foot">
            <tr>
                <td>I alt:</td>
                <td></td>
                <td><?php echo danish_amount($total) ?></td>
                <td><?php echo danish_amount($non_food_total) ?></td>
                <td><?php echo danish_amount($eco_total) ?></td>
            </tr>
          </tfoot>
        </table>
    </div>
    <?php
    $ecology = ob_get_clean();

    $send = array(
        'ecology' => $ecology
    );
    wp_send_json($send); // Send json

    wp_die();
}
