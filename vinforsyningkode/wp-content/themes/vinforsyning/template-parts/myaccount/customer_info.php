<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

echo sprintf(
    '<h1><u>%s</u></h1>',
    __('Kundeinfo ', 'bbh')
);

$user_id = get_current_user_id();
$user_customer_manager_for = get_user_meta($user_id, 'customer_manager_for', true);
$user_customer_delivery_days = get_user_meta($user_id, 'customer_delivery_days', true);
$user_customer_warehouse_nr = get_user_meta($user_id, 'customer_warehouse_nr', true);
?>

<div class="customer-info-container">
    <div>
        <strong>Manager for:</strong>
        <span><?php echo $user_customer_manager_for;?></span>
    </div>
    <div>
        <strong>Leveringsdage:</strong>
        <span><?php echo $user_customer_delivery_days; ?></span>
    </div>
    <div>
        <strong>Lagernummer:</strong>
        <span><?php echo $user_customer_warehouse_nr;?></span>
    </div>
</div>
