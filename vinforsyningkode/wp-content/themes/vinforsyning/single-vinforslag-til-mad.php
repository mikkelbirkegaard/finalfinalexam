<?php
$text = get_field('text');
$title = get_the_title();
 ?>
 <meta name="robots" content="noindex, nofollow" />
 <?php get_header(); ?>
 <section class="flexible-inner-section bbh-inner-section wineschool-header">
     <div class="grid-container">
         <div class="row">
             <div class="col-sm-12">
                 <div class="wineschool-top-header">
                     <h1>Forslag</h1>
                     <div class="backLink-img">
                         <a class="backLink-wineschool" href="/vinskolen/vinforslag-til-mad/"><span class="arrow-left"></span> Tilbage</a>

                     </div>
                 </div>
             </div>
         </div>
     </div>
 </section>
 <section class="flexible-inner-section bbh-inner-section wineschool-single-header">
     <div class="grid-container">
         <div class="row">
             <div class="col-sm-12">
                 <h2><?php echo $title ?></h2>
                 <?php echo $text ?>
             </div>
         </div>
     </div>
 </section>
