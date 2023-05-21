<?php
$title = get_sub_field('title');
$body_text = get_sub_field('body_text');
$buttons = get_sub_field('buttons');
 ?>



<section class="flexible-inner-section bbh-inner-section article-become-costomer">
    <div class="grid-container">
        <div class="row">
            <div class="col-sm-12 become-costomer">
                <?php if ($title): ?>
                    <h3><?php echo $title ?></h3>
                <?php endif; ?>
                <?php if ($body_text): ?>
                    <?php echo $body_text?>
                <?php endif; ?>
                <div class="section-button">
                    <?php if (have_rows('buttons')) :
                        while (have_rows('buttons')) : the_row(); ?>
                            <?php if (get_sub_field('primary_secondary_btn') == 'primary') {
                                $link = get_sub_field('link');
                                $icomoon_icon = get_sub_field('icomoon_icon');
                                $choose_icon = get_sub_field('choose_icon');
                                ?>
                                <?php if ($link): ?>
                                    <div class="link-container">
                                        <div class="bbh-btn primary-btn">
                                        <a class="btn-white" target="<?php echo $link['target'] ?>" href="<?php echo $link['url'] ?>"><?php echo $link['title'] ?></a>
                                        <?php if ($choose_icon && !empty($icomoon_icon)): ?>
                                            <?php if ( $icomoon_icon ) : ?>
                                                <span class="icon icomoon <?php echo $icomoon_icon ?>"></span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php } else if (get_sub_field('primary_secondary_btn') == 'secondary') {
                                $link_secondary = get_sub_field('link_secondary');
                                $icomoon_secondary = get_sub_field('icomoon_secondary');
                                $choose_icon_secondary = get_sub_field('choose_icon_secondary');
                                ?>
                                <?php if ($link_secondary): ?>
                                    <div class="link-container">
                                        <div class="bbh-btn secondary-btn red-btn">
                                            <a class="btn-red" target="<?php echo $link_secondary['target'] ?>" href="<?php echo $link_secondary['url'] ?>"><?php echo $link_secondary['title'] ?></a>
                                            <?php if ($choose_icon_secondary && !empty($icomoon_secondary)): ?>
                                                <?php if ( $icomoon_secondary ) : ?>
                                                    <span class="icon icomoon <?php echo $icomoon_secondary ?>"></span>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php } ?>
                        <?php endwhile;
                    endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
