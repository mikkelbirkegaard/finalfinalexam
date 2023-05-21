<?php
// No direct access, please

if ( ! defined( 'ABSPATH' ) ) exit;

echo sprintf(
    '<h1><u>%s</u></h1>',
    __('Vinskolen', 'bbh')
);

    $current_user_id = get_current_user_id();
    $vinkort_id = get_user_meta($current_user_id, 'vinkort_id', true);
    $vinskolen_array = bbh_find_vinskole_on_customer_id($current_user_id);

?>
<section class="vinskolen-page">

    <?php
        acf_form(array(
        'id' => 'vinskolen-form',
        'post_id'	   => 'user_'.$current_user_id,
        'post_title'	=> false,
        'form' => true,
        'submit_value' => __("Gå til vinkort", 'acf'),
        'field_groups' => array('group_62b077911a7b1'),
        'updated_message' => __("", 'acf'),
        'honeypot' => true,
    ));



    if ($vinkort_id) {
        // d($vinkort_id);
        $vinkort = get_page_by_title($vinkort_id, OBJECT, 'vinkort');
        $vinskolen_vinkort = get_field('vinskolen_vinkort', $vinkort);

        // d($vinkort);
        if ($vinkort) {
            acf_form(array(
        		'id' => 'vinskolen-form',
                'post_id'	   => $vinkort->ID,
                'post_title'	=> false,
        		'form' => true,
        		'submit_value' => __("Opdater vinkort", 'acf'),
                'field_groups' => array('group_6267d3adb43a6'),
        		'updated_message' => __("Vinkortet er nu opdateret!", 'acf'),
                'honeypot' => true,
            ));
            ?>
            <?php if ($vinskolen_vinkort): ?>
                <?php foreach ($vinskolen_vinkort as $vinskolen_single ): ?>
                    <?php
                    $vinskolen_vine = $vinskolen_single['vinskolen_featured'];
                    $vineskole_header = $vinskolen_single['vinskolen_headline'];
                     ?>
                    <div class="vinskolen-grid-headline">
                        <p><strong><?php echo $vineskole_header ?></strong></p>
                    </div>
                   <div class="vinskolen-grid">
                       <?php if ($vinskolen_array) {
                           echo do_shortcode('[products ids="'.implode(', ',$vinskolen_vine).'" columns="4"]');
                       } ?>
                   </div>
                <?php endforeach; ?>
            <?php endif; ?>
                <?php
        }else{
            acf_form(array(
        		'id' => 'vinskolen-form',
                'post_id'	   => 'new_post',
                'new_post'		=> array(
        			'post_type'		=> 'vinkort',
        			'post_status'	=> 'publish',
                    'post_title'	=> $vinkort_id,
        		),
                'post_title'	=> false,
        		'form' => true,
        		'submit_value' => __("Opdater vinskolen", 'acf'),
                'field_groups' => array('group_6267d3adb43a6'),
        		'updated_message' => __("Vinskolen er nu opdateret!", 'acf'),
                'honeypot' => true,
            ));
        }



    }
    ?>

</section>
