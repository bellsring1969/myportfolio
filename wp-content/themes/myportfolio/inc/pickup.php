<h2 class="m-heading-01 is-center is-pc u-mb30"><b>PICK UP</b></h2>
<div class="m-carousel-wrap">
<h2 class="m-heading-01 is-center is-sp u-mb5"><b class="is-pickup">PICK UP</b></h2>
<?php
  $args = [
    'post_type' => ['lifestylebook','metasfilm'],
    'numberposts' => -1,
    'orderby' => 'meta_value_num',
    'meta_key' => 'pickup_order',
    'order' => 'DESC',
    'meta_query' => [
      [
        'key' => 'pickup_check',
        'value' => true,
      ],
    ],
  ];
  $the_query = new WP_Query($args);
  ?>
  <?php if($the_query -> have_posts()): ?>
    <div class="m-carousel">
      <?php while($the_query -> have_posts()): $the_query -> the_post(); ?>
      <div class="m-carousel__item">
        <a class="m-card-01 u-mb0" href="<?php the_permalink(); ?>">
          <?php if( get_field('thumbnail') ) { ?>
            <?php echo wp_get_attachment_image(get_post_meta($post->ID, 'thumbnail', true),'crop_thumbnail'); ?>
          <?php }else{ ?>
            <img class="m-card-01__img" src="/assets/img/common/dummy_thumbnail.jpg" alt="">
          <?php } ?>
          <div class="m-blog-row flex-wrap">
            <?php
            if (get_post_type() === 'lifestylebook'):
              $terms = get_the_terms($post->ID, 'lifestylebook_category');
            elseif (get_post_type() === 'metasfilm'):
              $terms = get_the_terms($post->ID, 'metasfilm_category');
            endif;
            if( !empty( $terms ) ){
              foreach($terms as $term):
              echo '<span class="m-blog-category">'.$term->name.'</span>';
              endforeach;
            }
            ?>
            <?php
              $checked = get_field('pr'); 
              if($checked){
                echo '<span class="m-blog-pr">PR</span>';
              }
            ?>
          </div>
          <p class="m-card-01__txt"><?php echo mb_substr(strip_tags($post-> post_title),0,27); ?><?php if(mb_strlen($post->post_title)>27): ?>...<?php endif; ?></p>
          <time class="m-blog-date" datetime="<?php echo get_the_date('Y-m-d'); ?>"><?php echo get_the_date('Y.m.d'); ?></time>
        </a>
      </div>
      <?php endwhile; ?>
      <?php wp_reset_postdata(); ?>
    </div>
  <?php endif; ?>
</div>