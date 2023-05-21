<?php
$heading_division = get_sub_field('heading_division');
$employees = get_sub_field( 'employees' );
 ?>

<section id="section-<?php echo get_row_index(); ?>" class="flexible-inner-section bbh-inner-section contact-employees-collapsible">
    <div class="grid-container">
        <div class="row">
            <div class="col-sm-12">
                <div class="collapsible-container">
                    <?php if ($employees): ?>
                        <div class="info">
                            <div class="headline">
                                 <div class="headline-container">
                                     <h2><?php echo $heading_division ?></h2>
                                </div>
                            </div>
                            <div class="collapsible-content">
                                <div class="employee-container">
                                <?php foreach ($employees as $post):
                                    setup_postdata($post);
                                    if (file_exists(get_stylesheet_directory() . '/template-parts/employee-card.php')) {
                                        include( get_stylesheet_directory() . '/template-parts/employee-card.php');
                                    }
                                endforeach; ?>
                                <?php wp_reset_postdata(); ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
