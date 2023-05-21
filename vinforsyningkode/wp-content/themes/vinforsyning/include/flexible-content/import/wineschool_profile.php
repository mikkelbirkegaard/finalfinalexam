

<?php
$user_id = get_current_user_id();
 $user_info = get_userdata($user_id);
 $redirect = 'https://vinforsyning.dk/vinskolen';
 $urls = wp_logout_url($redirect);
 ?>
