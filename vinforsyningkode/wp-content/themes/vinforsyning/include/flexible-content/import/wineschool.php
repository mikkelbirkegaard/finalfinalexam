<?php
$headline_wineschool = get_field('headline_wineschool', 'option');


 ?>






<?php if (is_user_logged_in()): ?>
    <section class="flexible-inner-section bbh-inner-section wineschool-header">
        <div class="grid-container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="wineschool-top-header">
                        <h1><?php echo $headline_wineschool ?></h1>
                        <div class="backLink-img">
                            <a class="edit-profile" href="/vinskolen/indstillinger">
                            <img src="/wp-content/uploads/2022/05/Image-1.png" alt="Indstilligner">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="flexible-inner-section bbh-inner-section wineschool-body">
        <div class="grid-container">

            <div class="row">
                <div class="col-sm-12">
                    <div class="cat-grid">
                    <?php
                    $wineschool_categories = get_field('wineschool_categories', 'option');

                   foreach ($wineschool_categories as $category ) {
                       $img = $category['img'];
                       $headline = $category['headline'];
                       $link = $category['link'];
                       ?>
                       <div class="cat-box">
                           <a href="<?php echo $link['url'] ?>" target="<?php echo $link['target'] ?>" class="btn">
                           <div class="image">
                               <img class="lazyload" data-srcset="<?php echo $img['sizes']['small'] ?>" alt="<?php echo $img['alt'] ?>">
                           </div>
                           <div class="text">
                               <h4><?php echo $headline ?></h4>
                           </div>
                           </a>
                       </div>
                       <?php
                   }
                    ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
 <?php else: ?>

     <section class="flexible-inner-section bbh-inner-section wineschool-header">
         <div class="grid-container">
             <div class="row">
                 <div class="col-sm-12">
                     <div class="wineschool-top-header">
                         <h1>Vinskolen</h1>
                         <div class="backLink-img">

                         </div>
                     </div>
                 </div>
             </div>
         </div>
     </section>
     <section class="flexible-inner-section bbh-inner-section wineschool-login">
         <div class="grid-container">
             <div class="row">
                 <div class="col-sm-12">
                     <div class="login-box">
                         <h2>Log ind</h2>
                     <form id="loginform" name="loginform" action="<?php echo wp_login_url(home_url($wp->request)) ?>" method="POST">
                         <?php if ($error): ?>
                             <p class="error-message">
                                 <?php _e('Der var en fejl med din indtastning.', 'bbh'); ?>
                                 <br>
                                 <?php _e('Tjek dine loginoplysninger og prøv venligst igen.', 'bbh'); ?>
                             </p>
                         <?php endif; ?>
                         <p class="login-username">
                             <label for="user_login">
                                 <?php _e('E-mail', 'bbh'); ?><sup class="required">*</sup>
                             </label>
                             <input required type="text" id="user_login" class="input" name="log" value="" placeholder="<?php esc_attr_e('Indtast emailadresse', 'bbh'); ?>" input-mode="email">
                         </p>

                         <p class="login-password">
                             <label for="user_pass">
                                 <?php _e('Adgangskode', 'bbh'); ?><sup class="required">*</sup>
                             </label>
                             <input required type="password" name="pwd" id="user_pass" class="input" value="" placeholder="<?php esc_attr_e('Indtast adgangskode', 'bbh'); ?>"  autocomplete="off">
                         </p>
                         <p><input id="rememberme" type="checkbox" value="forever" name="rememberme"> Husk mig</p>
                         <?php do_action( 'login_form' ) ?>
                         <p class="login-submit">
                             <input type="submit" value="<?php _e('Log ind', 'bbh'); ?> ">
                         </p>
                         <input type="hidden" name="popup_form" value="1">
                     </form>
                     <div class="signup">
                         <div class="flex-row ai-center">
                            <a class="bbh-btn" href="/vinskolenn/registrer-ny-bruger/">Ny bruger?</a>
                         </div>
                     </div>
                     <div class="forgot-password">
                         <?php echo sprintf(
                             '<a href="%s">%s</a>',
                             wp_lostpassword_url(),
                             __('Glemt dit password?', 'bbh')
                         ); ?>
                     </div>
                    </div>
                 </div>
             </div>
         </div>
     </section>

<?php endif; ?>
