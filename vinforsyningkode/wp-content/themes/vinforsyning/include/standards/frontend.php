<?php
/*========================================
=          remove copyright bar          =
========================================*/
add_action( 'after_setup_theme','bbh_remove_copyright_bar' );
function bbh_remove_copyright_bar() {
    remove_action( 'generate_footer','generate_construct_footer' );
}

/*========================================
=                seo stuff               =
========================================*/
add_action('wp_head','bbh_canonical');
function bbh_canonical(){
    ?>
    <link rel="canonical" href="<?php the_permalink() ?>"/>
    <?php
}
add_filter( 'generate_article_itemtype', 'bbh_custom_body_itemtype' );
function bbh_custom_body_itemtype( $type ) {
        return 'WebPage';
}
/*========================================
=                pagespeed               =
========================================*/
/*-------------- gp fonts -------------*/
add_filter( 'generate_google_font_display', function() {
    return 'swap';
} );

/*=============================================
      = Hide admin bar with simple query =
===============================================*/
// add ?hidetoolbar to url to use
function bbh_hide_toolbar($value)
{
    if (isset($_GET['hidetoolbar']) && current_user_can('administrator'))
    {
        $value = false;
    }
    return $value;
}
add_filter('show_admin_bar', 'bbh_hide_toolbar', 10, 1);
