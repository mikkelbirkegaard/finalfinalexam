<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

echo sprintf(
    '<h1><u>%s</u></h1>',
    __('Mine fakturaer', 'bbh')
);

function danish_amount($number){
    $danish_number = number_format($number,2,',','.');
    return $danish_number;
}

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

if ($the_query->have_posts()):
    $total = 0; ?>
<div class="invoices">
    <div class="input-container">
        <input type="checkbox" id="paid-invoice" name="paid-invoice" value="" checked>
        <label for="paid-invoice">Vis kun ikke betalte fakturaer</label><br>
    </div>
    <table class="table table-bordered table-dark">
        <thead class="thead-red">
            <tr>
              <th class="th-invoice-date" scope="col">Posteringsdato<span></span></th>
              <th scope="col">Bilagsnummer<span></span></th>
              <th scope="col">Type<span></span></th>
              <th scope="col">Beskrivelse<span></span></th>
              <th scope="col">Forfaldsdato<span></span></th>
              <th scope="col">Oprindeligt beløb	<span></span></th>
              <th scope="col">Restbeløb<span></span></th>
              <th class="pdf" scope="col">Hent som PDF</th>
            </tr>
        </thead>
        <tbody>
<?php while($the_query->have_posts()): $the_query->the_post();
        $invoice_user_id = get_field('invoice_user_id');
        // Extra check
        if ($invoice_user_id == $customer_id ) {
            $invoice_date = get_field('invoice_date');
            $dateYMD = date('Ymd', strtotime($invoice_date));
            $invoice_appendix_number = get_field('invoice_appendix_number');
            $invoice_type = get_field('invoice_type');
            $invoice_description = get_field('invoice_description');
            $invoice_due_date = get_field('invoice_due_date');
            $due_dateYMD = date('Ymd', strtotime($invoice_due_date));
            $invoice_original_amount = get_field('invoice_original_amount');
            $invoice_balance = get_field('invoice_balance');
            $balance_int = (float)$invoice_balance;
            $invoice_url_pdf = get_field('invoice_url_pdf');
            if ($balance_int != 0) {
                $total = $total+($balance_int);
            }
            ?>
            <tr class="<?php if ($invoice_balance == 0) { echo "paid hide"; } ?>">
              <td><span class="hide"><?php echo $dateYMD ?></span><?php echo $invoice_date; ?></td>
              <td><?php echo $invoice_appendix_number; ?></td>
              <td><?php echo $invoice_type; ?></td>
              <td><?php echo $invoice_description; ?></td>
              <td><span class="hide"><?php echo $dateYMD ?></span><?php echo $invoice_due_date; ?></td>
              <td><?php echo danish_amount($invoice_original_amount)." DKK"; ?></td>
              <td><?php echo danish_amount($invoice_balance)." DKK"; ?></td>
              <td><a class="bbh-btn" target="_blank" href="<?php echo $invoice_url_pdf; ?>">Hent</a></td>
            </tr>
        <?php } ?>
    <?php endwhile;
    wp_reset_postdata(); ?>
        </tbody>
    </table>
    <div class="total-container">
        <div class="total">
            <span>Total</span><div class="price"><?php echo danish_amount($total) ?> DKK</div>
        </div>
    </div>
</div>
    <?php endif;
