<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;


function hook_facebook_metatag_head() {
  ?>
    <meta name="facebook-domain-verification" content="rmkm1p1ctcoeyu50j3i0v76980l841" />
  <?php
}
add_action( 'wp_head', 'hook_facebook_metatag_head' );

/*-------------- Add extra class to WordPress links -------------*/
add_action( 'after_wp_tiny_mce', function(){
  ?>
  <script>
    var originalWpLink;
    // Ensure both TinyMCE, underscores and wpLink are initialized
    if ( typeof tinymce !== 'undefined' && typeof _ !== 'undefined' && typeof wpLink !== 'undefined' ) {
      // Ensure the #link-options div is present, because it's where we're appending our checkbox.
      if ( tinymce.$('#link-options').length ) {
        // Append our checkbox HTML to the #link-options div, which is already present in the DOM.
        tinymce.$('#link-options').append(<?php echo json_encode( '<div class="link-nofollow"><label><span></span><input type="checkbox" id="wp-link-class" />Show link as button (with border)</label></div>' ); ?>);
        // Clone the original wpLink object so we retain access to some functions.
        originalWpLink = _.clone( wpLink );
        wpLink.addClass = tinymce.$('#wp-link-class');
        // Override the original wpLink object to include our custom functions.
        wpLink = _.extend( wpLink, {
          /**
           * Fetch attributes for the generated link based on
           * the link editor form properties.
           *
           * In this case, we're calling the original getAttrs()
           * function, and then including our own behavior.
           */
          getAttrs: function() {
            var attrs = originalWpLink.getAttrs();
            attrs.class = wpLink.addClass.prop( 'checked' ) ? 'bbh-btn' : false;
            return attrs;
          },
          /**
           * Build the link's HTML based on attrs when inserting
           * into the text editor.
           *
           * In this case, we're completely overriding the existing
           * function.
           */
          buildHtml: function( attrs ) {
            var html = '<a href="' + attrs.href + '"';
            if ( attrs.target ) {
              html += ' target="' + attrs.target + '"';
            }
            if ( attrs.class ) {
              html += ' class="' + attrs.class + '"';
            }
            return html + '>';
          },
          /**
           * Set the value of our checkbox based on the presence
           * of the rel='nofollow' link attribute.
           *
           * In this case, we're calling the original mceRefresh()
           * function, then including our own behavior
           */
          mceRefresh: function( searchStr, text ) {
            originalWpLink.mceRefresh( searchStr, text );
            var editor = window.tinymce.get( window.wpActiveEditor )
            if ( typeof editor !== 'undefined' && ! editor.isHidden() ) {
              var linkNode = editor.dom.getParent( editor.selection.getNode(), 'a[href]' );
              if ( linkNode ) {
                wpLink.addClass.prop( 'checked', 'bbh-btn' === editor.dom.getAttrib( linkNode, 'class' ) );
              }
            }
          }
        });
      }
    }
  </script>
  <style>
  #wp-link #link-options .link-nofollow {
    padding: 3px 0 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  #wp-link #link-options .link-nofollow label span {
    width: 83px;
  }
  .has-text-field #wp-link .query-results {
    top: 223px !important;
  }
    input#wp-link-class {
        margin-right: 9px;
    }
    #wp-link .query-results {
        top: 206px;
    }

  </style>
  <?php
});



add_action( 'admin_init', 'bbh_custom_editor_style' );
function bbh_custom_editor_style() {
    add_editor_style( get_stylesheet_directory_uri() . '/assets/scss/wysiwyg.css' );
}




/*======================================
=            Wysiwyg colors            =
======================================*/
function bbh_wysiwyg_colors($init) {

  $custom_colours = '
  "000000", "Black",
  "fefefe", "White",
  "D9D8D7", "Gray",
  "595047", "Brun",
  "403732", "Mørk Brun",
  "BFB0A3", "Sand",
  "AF3E3E", "Rød",
  "5C8E7D", "Grøn",
  ';

  // build colour grid default+custom colors
  $init['textcolor_map'] = '['.$custom_colours.']';

  // change the number of rows in the grid if the number of colors changes
  // 8 swatches per row
  $init['textcolor_rows'] = 3;
  $init['textcolor_cols'] = 3;

  return $init;
}
add_filter('tiny_mce_before_init', 'bbh_wysiwyg_colors');


/*=============================================
          = Admin wyiwyg stylesheet =
===============================================*/
function bbh_custom_editor_styles() {
	add_editor_style('/assets/scss/wywisyg.css');
}

add_action('init', 'bbh_custom_editor_styles');



/* Remove Yoast SEO Social Profiles From All Users
 * Credit: Yoast Developers
 * Last Tested: May 18 2019 using Yoast SEO 11.2.1 on WordPress 5.2
 */

add_filter('user_contactmethods', 'yoast_seo_admin_user_remove_social');

function yoast_seo_admin_user_remove_social ( $contactmethods ) {
	unset( $contactmethods['facebook'] );
	unset( $contactmethods['instagram'] );
	unset( $contactmethods['linkedin'] );
	unset( $contactmethods['myspace'] );
	unset( $contactmethods['pinterest'] );
	unset( $contactmethods['soundcloud'] );
	unset( $contactmethods['tumblr'] );
	unset( $contactmethods['twitter'] );
	unset( $contactmethods['youtube'] );
	unset( $contactmethods['wikipedia'] );
	return $contactmethods;
}
