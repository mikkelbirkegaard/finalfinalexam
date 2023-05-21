<?php
$user = wp_get_current_user();

add_action( 'profile_update', 'custom_profile_redirect', 12 );
function custom_profile_redirect() {
    if (current_user_can('waiter')):
        wp_redirect('https://vinforsyning.dk/vinskolen');
        exit;
    endif;
}
