<?php get_header(); ?>
<main role="main" class="main">
  <section id="works" class="works">
    <div class="inner">
      <h1 class="m-heading-01">WORKS</h1>
      <nav class="works__nav">
        <ul class="m-list-01">
          <li class="m-list-01__item"><a href="/works/">ALL</a></li>
          <?php
            $terms = get_terms('works_category','hide_empty=0');
            foreach ( $terms as $term ) {
              echo '<li class=m-list-01__item>';
              echo '<a href="/works/'.$term->slug.'/"><span>';
              echo $term->name.'</span></a></li>';
            }
          ?>
        </ul>
      </nav>
      <?php if(have_posts()): ?>
      <div class="m-card-01__container">
        <?php 
          $count = 0;
          while(have_posts()): the_post();
          $index =  $count + 1;
          $thumbnail_url = get_field('thumbnail_url');
          $client = get_field('client');
          $project_start = get_field('project_start');
          $project_start_date = date_create(get_field('project_start'));
          $project_start_formatted = date_format($project_start_date,'Y.m.d');
        ?>
        <div id="modal-btn-show-<?php echo $index ?>" class="m-card-01">
          <div class="m-card-01__img"><img src="<?php echo $thumbnail_url ?>" alt=""></div>
          <ul class="m-list-01">
            <?php
              $terms = get_the_terms($post->ID, array('works_category'));
              foreach($terms as $term):
              echo '<li class="m-list-01__item"><span>'.$term->name.'</span></li>';
              endforeach;
            ?>
          </ul>
          <div class="m-card-01__ttl"><?php the_title(); ?></div>
          <div class="m-card-01__client"><?php echo $client; ?> 様</div>
          <div class="m-card-01__date"><time datetime="<?php echo $project_start ?>"><?php echo $project_start_formatted ?></time> 〜</div>
        </div>
        <?php 
          ++$count;
          endwhile; 
        ?>
      </div>
      <?php else: ?>
        <p class="works__txt-error">登録の記事がありません。</p>
      <?php endif; ?>
      <?php if( function_exists("the_pagination") ) the_pagination(); ?>
    </div>
  </section>
</main>
<?php if(have_posts()): ?>
<div id="modal-container" class="m-modal-01__container">
  <?php 
    $count = 0;
    while(have_posts()): the_post(); 
    $index =  $count + 1;
    $thumbnail_url = get_field('thumbnail_url');
    $client = get_field('client');
    $url = get_field('url');
    $organization = get_field('organization');
    $project_start = get_field('project_start');
    $project_start_date = date_create(get_field('project_start'));
    $project_start_formatted = date_format($project_start_date,'Y.m.d');
    if (get_field('project_end')) {
      $project_end = get_field('project_end');
      $project_end_date = date_create(get_field('project_end'));
      $project_end_formatted = date_format($project_end_date,'Y.m.d');
    } else {
      $project_end = '';
      $project_end_date = '';
      $project_end_formatted = ''; 
    }
    $phase = get_field('phase');
    $span = get_field('span');
    $tools = get_field('tools');
    $languages = get_field('languages');
    $comments = get_field('comments');
  ?>
  <div id="modal-content-<?php echo $index; ?>" class="m-modal-01 ">
    <div class="inner">
      <div class="m-modal-01__scroll">
        <div class="m-modal-01__btn"></div>
        <div class="m-modal-01__lyt">
          <div class="m-modal-01__img"><img src="<?php echo $thumbnail_url ?>" alt=""></div>
          <ul class="m-list-01">
            <li class="m-list-01__item"><span><?php echo $term->name; ?></span></li>
          </ul>
          <div class="m-modal-01__ttl"><?php the_title(); ?></div>
          <div class="m-modal-01__client"><?php echo $client ?> 様</div>
        </div>
        <div class="m-modal-01__list-container">
          <dl class="m-modal-01__list">
            <dt class="m-modal-01__list-ttl">【URL】</dt>
            <dd class="m-modal-01__list-desc"><a href="<?php echo $url ?>" target="_blank"><?php echo $url ?></a></dd>
          </dl>
          <dl class="m-modal-01__list">
            <dt class="m-modal-01__list-ttl">【体制】</dt>
            <dd class="m-modal-01__list-desc"><?php echo $organization ?></dd>
          </dl>
          <dl class="m-modal-01__list">
            <dt class="m-modal-01__list-ttl">【プロジェクト期間】</dt>
            <dd class="m-modal-01__list-desc"><time datetime="<?php echo $project_start ?>"><?php echo $project_start_formatted ?></time> 〜 <time datetime="<?php echo $project_end ?>"><?php echo $project_end_formatted ?></time></dd>
          </dl>
          <dl class="m-modal-01__list">
            <dt class="m-modal-01__list-ttl">【担当フェーズ】</dt>
            <dd class="m-modal-01__list-desc"><?php echo $phase ?></dd>
          </dl>
          <dl class="m-modal-01__list">
            <dt class="m-modal-01__list-ttl">【担当フェーズの所要期間】</dt>
            <dd class="m-modal-01__list-desc"><?php echo $span ?></dd>
          </dl>
          <dl class="m-modal-01__list">
            <dt class="m-modal-01__list-ttl">【使用ツール】</dt>
            <dd class="m-modal-01__list-desc"><?php echo $tools ?></dd>
          </dl>
          <dl class="m-modal-01__list">
            <dt class="m-modal-01__list-ttl">【使用言語】</dt>
            <dd class="m-modal-01__list-desc"><?php echo $languages ?></dd>
          </dl>
          <?php if (get_field('comments')): $comments = get_field('comments'); ?>
          <dl class="m-modal-01__list">
            <dt class="m-modal-01__list-ttl">【コメント】</dt>
            <dd class="m-modal-01__list-desc"><?php echo $comments ?></dd>
          </dl>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
  <?php 
    ++$count;
    endwhile;
  ?>
  </div>
<?php endif; ?>
<div id="modal-wrapper" class="m-modal-01__wrapper"></div>
<?php get_footer(); ?>