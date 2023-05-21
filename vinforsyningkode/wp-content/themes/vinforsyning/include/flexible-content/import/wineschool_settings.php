
<?php
$user_id = get_current_user_id();
 $user_info = get_userdata($user_id);
 $redirect = 'https://vinforsyning.dk/vinskolen';
 $urls = wp_logout_url($redirect);
 ?>
<section class="flexible-inner-section bbh-inner-section wineschool-header">
    <div class="grid-container">
        <div class="row">
            <div class="col-sm-12">
                <div class="wineschool-top-header">
                    <h1>Indstillinger</h1>
                    <div class="backLink-img">
                        <a class="backLink-wineschool" href="/vinskolen"><span class="arrow-left"></span> Tilbage</a>

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
                <div class="settings-btns">
                    <a class="bbh-btn" href="/profil">Rediger Profil</a>
                    <a class="bbh-btn" href="/om-vinskole">Om Vinskole</a>
                    <a class="bbh-btn" href="/vilkaar-og-betingelser">Vilkår & Betingelser</a>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="flexible-inner-section bbh-inner-section wineschool-edit-profile">
    <div class="grid-container">
        <div class="row">
            <div class="col-sm-12">
                <div class="edit-info">

                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="single-link">
                    <a class="logout-btn" href="<?php echo $urls ?>">Log ud</a>
                </div>
            </div>
        </div>
    </div>
</section>
