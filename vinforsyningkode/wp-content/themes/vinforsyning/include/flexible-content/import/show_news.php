<!-- /*===============================================
=          made by Mikkel Christiansen           =
===============================================*/ -->
<?php
$title = get_sub_field('title');
$link = get_sub_field('link');
$highlighted_news = get_sub_field('highlighted_news');
$choose_news_first = get_sub_field('choose_news_first');
$choose_news_second = get_sub_field('choose_news_second');

 ?>
 <section class="flexible-inner-section bbh-inner-section" data-animation="fadeIn">
   <div class="grid-container">
     <div class="row">
       <div class="col-sm-12 news-section">
         <div class="title-link-wrapper">
           <?php if ($title): ?>
             <h2><?php echo $title ?></h2>
           <?php endif; ?>
           <?php if ($link): ?>
             <a class="read-more" target="<?php echo $link['target'] ?>" href="<?php echo $link['url'] ?>">
                 <p><?php echo $link['title'] ?></p>
                 <span class="icon-arrow"></span>
             </a>
           <?php endif; ?>
         </div>
         <div class="news-wrapper">
            <div class="highlighted-news">
               <?php if ($highlighted_news):
                 $post = $highlighted_news;
                 setup_postdata($post);
                 ?>

                   <?php
                   if (has_post_thumbnail()): ?>

                     <a class="image-wrapper" href="<?php the_permalink(); ?>">
                         <?php the_post_thumbnail('full'); // display the featured image ?>
                     </a>
                   <?php endif; ?>
                   <div class="title-icon">
                       <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                       <span class="icon-arrow newsi"></span>
                   </div>
                   <a href="<?php the_permalink(); ?>">
                       <?php echo get_article_excerpt('article_sections', get_the_ID(), 15); ?>
                   </a>
                 <?php wp_reset_postdata(); ?>
               <?php endif; ?>
           </div>
           <div class="normal-news">
               <div class="first-news">
                   <?php if ($choose_news_first):
                     $post = $choose_news_first;
                     setup_postdata($post);
                     ?>

                       <?php
                       if (has_post_thumbnail()): ?>
                       <a class="image-wrapper" href="<?php the_permalink(); ?>">
                           <?php the_post_thumbnail('full'); // display the featured image ?>
                       </a>
                       <?php endif; ?>
                       <a class="title" href="<?php the_permalink(); ?>"><?php the_title(); ?>
                           <span class="icon-arrow"></span>
                       </a>
                       <a href="<?php the_permalink(); ?>">
                           <?php echo get_article_excerpt('article_sections', get_the_ID(), 15); ?>
                       </a>
                     <?php wp_reset_postdata(); ?>
                   <?php endif; ?>
               </div>
               <div class="second-news">
                   <?php if ($choose_news_second):
                     $post = $choose_news_second;
                     setup_postdata($post);
                     ?>

                       <?php
                       if (has_post_thumbnail()): ?>
                       <a class="image-wrapper" href="<?php the_permalink(); ?>">
                           <?php the_post_thumbnail('full'); // display the featured image ?>
                       </a>
                       <?php endif; ?>
                       <a class="title" href="<?php the_permalink(); ?>"><?php the_title(); ?>
                           <span class="icon-arrow"></span>
                       </a>
                       <a href="<?php the_permalink(); ?>">
                           <?php echo get_article_excerpt('article_sections', get_the_ID(), 15); ?>
                       </a>

                     <?php wp_reset_postdata(); ?>
                   <?php endif; ?>
               </div>
           </div>
         </div>
       </div>
     </div>
   </div>
 </section>
