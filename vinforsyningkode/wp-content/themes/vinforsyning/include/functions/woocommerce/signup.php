<?php
/*=============================================
= ADD FIELDS =
===============================================*/

function bbh_user_register_fields() {?>
    <!-- fornavn -->
    <p class="form-row form-row-wide">
        <label for="reg_billing_first_name"><?php _e( 'First name', 'woocommerce' ); ?><span class="required">*</span></label>
        <input type="text" class="input-text" name="billing_first_name" id="reg_billing_first_name" value="<?php if ( ! empty( $_POST['billing_first_name'] ) ) esc_attr_e( $_POST['billing_first_name'] ); ?>" />
    </p>
    <!-- firma -->
    <p class="form-row form-row-wide">
        <label for="reg_billing_last_name"><?php _e( 'Firmanavn (valgfri)', 'woocommerce' ); ?></label>
        <input type="text" class="input-text" name="billing_company" id="reg_billing_company" value="<?php if ( ! empty( $_POST['billing_company'] ) ) esc_attr_e( $_POST['billing_company'] ); ?>" />
    </p>
    <!-- efternavn -->
    <!-- <p class="form-row form-row-wide">
        <label for="reg_billing_last_name"><?php _e( 'Last name', 'woocommerce' ); ?><span class="required">*</span></label>
        <input type="text" class="input-text" name="billing_last_name" id="reg_billing_last_name" value="<?php if ( ! empty( $_POST['billing_last_name'] ) ) esc_attr_e( $_POST['billing_last_name'] ); ?>" />
    </p> -->
    <!-- adresse -->
    <p class="form-row form-row-wide">
        <label for="reg_billing_address_1"><?php _e( 'Adresse', 'woocommerce' ); ?><span class="required">*</span></label>
        <input type="text" class="input-text" name="billing_address_1" id="reg_billing_address_1" value="<?php if ( ! empty( $_POST['billing_address_1'] ) ) esc_attr_e( $_POST['billing_address_1'] ); ?>" />
    </p>
    <!-- postnummer -->
    <p class="form-row form-row-wide">
        <label for="reg_billing_postcode"><?php _e( 'Postnr.', 'woocommerce' ); ?><span class="required">*</span></label>
        <input type="text" class="input-text" name="billing_postcode" id="reg_billing_postcode" value="<?php if ( ! empty( $_POST['billing_postcode'] ) ) esc_attr_e( $_POST['billing_postcode'] ); ?>" />
    </p>
    <!-- by -->
    <p class="form-row form-row-wide">
        <label for="reg_billing_city"><?php _e( 'By', 'woocommerce' ); ?><span class="required">*</span></label>
        <input type="text" class="input-text" name="billing_city" id="reg_billing_city" value="<?php if ( ! empty( $_POST['billing_city'] ) ) esc_attr_e( $_POST['billing_city'] ); ?>" />
    </p>
    <!-- telefon -->
    <p class="form-row form-row-wide">
        <label for="reg_billing_phone"><?php _e( 'Phone', 'woocommerce' ); ?><span class="required">*</span></label>
        <input type="text" class="input-text" name="billing_phone" id="reg_billing_phone" value="<?php esc_attr_e( $_POST['billing_phone'] ); ?>" />
    </p>
    <!-- cvr -->
    <p class="form-row form-row-wide">
        <label for="customer_cvr"><?php _e( 'CVR', 'woocommerce' ); ?><span class="required">*</span></label>
        <input type="text" class="input-text" name="customer_cvr" id="customer_cvr" value="<?php if ( ! empty( $_POST['customer_cvr'] ) ) esc_attr_e( $_POST['customer_cvr'] ); ?>" />
    </p>
    <div class="clear"></div>
    <?php
}
add_action( 'woocommerce_register_form_start', 'bbh_user_register_fields' );

/*=============================================
= VALIDATE FIELDS =
===============================================*/
function wooc_validate_extra_register_fields( $username, $email, $validation_errors ) {
    //fornavn
    if ( isset( $_POST['billing_first_name'] ) && empty( $_POST['billing_first_name'] ) ) {
        $validation_errors->add( 'billing_first_name_error', __( '<strong>Error</strong>: Fornavn mangler', 'woocommerce' ) );
    }
    //efternavn
    if ( isset( $_POST['billing_last_name'] ) && empty( $_POST['billing_last_name'] ) ) {
        $validation_errors->add( 'billing_last_name_error', __( '<strong>Error</strong>: Efternavn mangler.', 'woocommerce' ) );
    }
    //adresse
    if ( isset( $_POST['billing_address_1'] ) && empty( $_POST['billing_address_1'] ) ) {
        $validation_errors->add( 'billing_address_1_error', __( '<strong>Error</strong>: Adresse mangler.', 'woocommerce' ) );
    }
    //by
    if ( isset( $_POST['billing_city'] ) && empty( $_POST['billing_city'] ) ) {
        $validation_errors->add( 'billing_city_error', __( '<strong>Error</strong>: By mangler.', 'woocommerce' ) );
    }
    //postnummer
    if ( isset( $_POST['billing_postcode'] ) && empty( $_POST['billing_postcode'] ) ) {
        $validation_errors->add( 'billing_postcode_error', __( '<strong>Error</strong>: Postnr. mangler.', 'woocommerce' ) );
    }
    //telfon
    if ( isset( $_POST['billing_phone'] ) && empty( $_POST['billing_phone'] ) ) {
        $validation_errors->add( 'billing_phone_error', __( '<strong>Error</strong>: Telefon mangler.', 'woocommerce' ) );
    }
    //cvr
    if ( isset( $_POST['customer_cvr'] ) && empty( $_POST['customer_cvr'] ) ) {
        $validation_errors->add( 'customer_cvr_error', __( '<strong>Error</strong>: CVR mangler.', 'woocommerce' ) );
    }
    return $validation_errors;
}
add_action( 'woocommerce_register_post', 'wooc_validate_extra_register_fields', 10, 3 );
/*=============================================
= SAVE FIELDS =
===============================================*/
function wooc_save_extra_register_fields( $customer_id ) {
    //telefon
    if ( isset( $_POST['billing_phone'] ) ) {
        update_user_meta( $customer_id, 'billing_phone', sanitize_text_field( $_POST['billing_phone'] ) );
    }
    //fornavn
    if ( isset( $_POST['billing_first_name'] ) ) {
        update_user_meta( $customer_id, 'first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
        update_user_meta( $customer_id, 'billing_first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
    }
    //firmanavn
    if ( isset( $_POST['billing_company'] ) ) {
        update_user_meta( $customer_id, 'billing_company', sanitize_text_field( $_POST['billing_company'] ) );
    }
    //efternavn
    if ( isset( $_POST['billing_last_name'] ) ) {
        update_user_meta( $customer_id, 'last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
        update_user_meta( $customer_id, 'billing_last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
    }
    //Adresse
    if ( isset( $_POST['billing_address_1'] ) ) {
        update_user_meta( $customer_id, 'billing_address_1', sanitize_text_field( $_POST['billing_address_1'] ) );
    }
    //By
    if ( isset( $_POST['billing_city'] ) ) {
        update_user_meta( $customer_id, 'billing_city', sanitize_text_field( $_POST['billing_city'] ) );
    }
    //Postnu
    if ( isset( $_POST['billing_postcode'] ) ) {
        update_user_meta( $customer_id, 'billing_postcode', sanitize_text_field( $_POST['billing_postcode'] ) );
    }
    //CVR
    if ( isset( $_POST['customer_cvr'] ) ) {
        update_user_meta( $customer_id, 'customer_cvr', sanitize_text_field( $_POST['customer_cvr'] ) );
    }
}
add_action( 'woocommerce_created_customer', 'wooc_save_extra_register_fields' );
/*=============================================
= REMOVE PASSWORD STRENGHT =
===============================================*/

function bbh_remove_password_strength() {


    wp_dequeue_script( 'wc-password-strength-meter' );

}


add_action( 'wp_print_scripts', 'bbh_remove_password_strength', 10 );

/*=============================================
            = Rediect to home after signup =
===============================================*/
add_filter( 'woocommerce_registration_redirect', 'custom_redirection_after_registration', 10, 1 );
function custom_redirection_after_registration( $redirection_url ){
    // Change the redirection Url
    $redirection_url = get_home_url()."/shop/"; // Home page

    return $redirection_url; // Always return something
}
