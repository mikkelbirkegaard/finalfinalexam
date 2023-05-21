<?php
add_filter( 'woocommerce_customer_meta_fields', 'filter_add_customer_meta_fields', 10, 1 );
function filter_add_customer_meta_fields( $args ) {
    $args['shipping']['fields']['shipping_tel'] = array(
        'label'       => __( 'Mobil', 'woocommerce' ),
        'description' => '',
    );
    return $args;
} ?>
<?php
/*===============================================
=          New Usertype: Tjener           =
===============================================*/
add_role( 'waiter', 'Tjener', 'read' );

 ?>
