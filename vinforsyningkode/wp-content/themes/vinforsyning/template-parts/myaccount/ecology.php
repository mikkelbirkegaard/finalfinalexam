<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

echo sprintf(
    '<h1><u>%s</u></h1>',
    __('Økologi', 'bbh')
);


$ecology_text = get_field( 'ecology_text', 'theme_shop_settings' );
echo $ecology_text;

function danish_amount($number){
    $danish_number = number_format($number,2,',','.');
    return $danish_number;
}
?>

<div class="ecology-flex-container">
    <div id="copy-btn" class="bbh-btn"  onclick="SelectContent('ecology-table');">Kopier hele tabel til udklipsholder</div>
    <div id="response">
        <div class="invoices">
            <table class="table table-bordered table-dark">
                <thead class="thead-red">
                    <tr>
                      <th class="th-invoice-date" scope="col">Dato<span></span></th>
                      <th scope="col">Faktura-nummer<span></span></th>
                      <th scope="col">Samlet beløb på faktura<span></span></th>
                      <th scope="col">Varer der ikke indgår i beregningen af økologiprocenten<span></span></th>
                      <th scope="col">Samlet beløb af økologiske råvarer<span></span></th>
                    </tr>
                </thead>

    <?php
    $user_id = get_current_user_id();
    $customer_id = get_field('customer_number', 'user_'.$user_id);
    $args = array(
        'post_type' => 'invoice',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => 'invoice_user_id',
                'value' => $customer_id,
                'compare' => '='
            ),
        ),
    );
    $the_query = new WP_Query( $args );
    if ($the_query->have_posts()): ?>
            <tbody id="ecology-table">
        <?php
        $total = 0;
        $non_food_total = 0;
        $eco_total = 0;
        // $first = reset($the_query->posts);
        // $last = end($the_query->posts);
        // $first_date = date('Y-m-d', strtotime(get_field('invoice_date', $first->ID)));
        // $last_date = date('Y-m-d', strtotime(get_field('invoice_date', $last->ID)));
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
                if ((float)$total_value) {
                    $total = $total+(float)$total_value;
                }
                if ((float)$non_food_value) {
                    $non_food_total = $non_food_total+(float)$non_food_value;
                }
                if ((float)$eco_value) {
                    $eco_total = $eco_total+(float)$eco_value;
                }
                ?>
                <tr>
                  <td><span class="hide"><?php echo $dateYMD ?></span><?php echo $invoice_date; ?></td>
                  <td><?php echo $invoice_appendix_number.", Østjysk Vinforsyning"; ?></td>
                  <td><?php echo danish_amount($total_value) ?></td>
                  <td><?php echo danish_amount($non_food_value) ?></td>
                  <td><?php echo danish_amount($eco_value) ?></td>
                </tr>
            <?php } ?>
        <?php endwhile;
        wp_reset_postdata(); ?>
            </tbody>

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
    </div>
    <?php
    $total_minus_non = $total-$non_food_total;
    $ecology_percent = $eco_total/$total_minus_non*100;
    if ($ecology_percent > 100 || $ecology_percent < 0) { $ecology_percent = 0; } ?>

    <form action="<?php echo site_url() ?>/wp-admin/admin-ajax.php" method="POST" id="filter">
        <div class="input-container">
            <label for="start-date">Fra:</label>
            <input type="date" id="start" name="start-date" value="" min="2015-01-01" max="<?php echo date('Y-m-d') ?>">
            <label for="start-end">Til:</label>
            <input type="date" id="end" name="end-date" value="" min="2015-01-01" max="<?php echo date('Y-m-d') ?>">
            <label for="unit">Enhed:</label>
            <select name="unit" id="unit">
              <option value="beløb">Beløb</option>
              <option value="vægt">Vægt</option>
            </select>

            <div class="ecology-percent" style="display:inline-block">
                <u>Økologiprocent: <span id="ecology-value"><?php echo number_format((float)$ecology_percent, 2, ',', '') ?></span>%</u>
            </div>
            <input class="bbh-btn" value="Nulstil" id="ecology-reset" type="reset">
        </div>
        <input type="hidden" name="security" id="ecology-ajax-nonce" value="<?php echo wp_create_nonce( 'ecology-ajax-nonce' ) ?>"/>
        <input type="hidden" name="action" value="ecology_ajax">
        <input type="hidden" id="ecology-percentage" name="ecology_percentage" value="<?php echo number_format((float)$ecology_percent, 2, ',', '') ?>">
    </form>
</div>
