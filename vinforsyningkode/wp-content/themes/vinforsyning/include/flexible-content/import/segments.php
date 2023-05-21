<!-- /*===============================================
=          made by Mikkel Christiansen           =
===============================================*/ -->
<?php
$title = get_sub_field('title');
?>

<section class="flexible-inner-section bbh-inner-section" data-animation="fadeIn">
  <div class="grid-container">
    <div class="row">
      <div class="col-sm-12 segments-wrapper">
        <?php if ($title): ?>
          <div class="title">
            <h2><?php echo $title ?></h2>
          </div>
        <?php endif; ?>

        <div class="grid-wrapper">
          <?php if (have_rows('add_segments')) :
            while (have_rows('add_segments')) : the_row();
              $image = get_sub_field('image');
              $link = get_sub_field('link');
              $description = get_sub_field('description');
          ?>
              <?php if ($link): ?>
                <div class="segment" onclick="location.href='<?php echo $link['url']; ?>'">
                  <?php if ($image): ?>
                    <div class="image-container">
                      <img class="image lazyload" src="<?php echo $image['url'] ?>" alt="<?php echo $image['alt'] ?>">
                    </div>
                  <?php endif; ?>
                  <div class="segment-title-icon">
                    <a class="link-normal" target="<?php echo $link['target'] ?>" href="<?php echo $link['url'] ?>">
                      <?php echo $link['title'] ?>
                    </a>
                    <span class="icon-arrow"></span>
                  </div>
                  <?php if ($description): ?>
                    <div class="description">
                      <?php echo $description ?>
                    </div>
                  <?php endif; ?>
                </div>
              <?php endif; ?>
            <?php endwhile;
          endif; ?>
        </div>

      </div>
    </div>
  </div>
</section>
