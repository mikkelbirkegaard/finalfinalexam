<?php

if ( ! defined( 'ABSPATH' ) ) exit;

// Import variable
$file_path = STYLESHEETPATH . '/include/flexible-content/import/';


/*
--Template HTML--

<section class="flexible-inner-section bbh-inner-section">
    <div class="grid-container">
        <div class="row">
            <div class="col-sm-12">

            </div>
        </div>
    </div>
</section>

*/

// check if the flexible content field has rows of data
if( have_rows('flexible_sections') ): ?>

	<div class="flexible-field-wrapper">
        <?php // loop through the rows of data
        while ( have_rows('flexible_sections') ) : the_row();

			// save layout name as var
            $slug = get_row_layout();
			// check if layout exist in import folder
			if( file_exists( get_theme_file_path("/include/flexible-content/import/{$slug}.php") ) ) {
        		include( get_theme_file_path("/include/flexible-content/import/{$slug}.php") );
        	}

        endwhile; // END while have_rows() ?>
	</div> <?php // END div.flexible-field-wrapper ?>
<?php else :

    // no layouts found

endif;

?>
