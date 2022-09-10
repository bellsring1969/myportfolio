<?php
/* ------------------------------------------------
全ての「?ver=~」を削除する（フロント）
-------------------------------------------------*/
function vc_remove_wp_ver_css_js( $src ) {
  if ( strpos( $src, 'ver=' ) )
    $src = remove_query_arg( 'ver', $src );
    return $src;
}
add_filter( 'style_loader_src', 'vc_remove_wp_ver_css_js', 9999 );
add_filter( 'script_loader_src', 'vc_remove_wp_ver_css_js', 9999 );
add_filter( 'show_admin_bar', '__return_false' );


/* ------------------------------------------------
wp-embed.min.jsを削除（フロント／Ver4.4以降）
-------------------------------------------------*/
remove_action('wp_head','rest_output_link_wp_head');
remove_action('wp_head','wp_oembed_add_discovery_links');
remove_action('wp_head','wp_oembed_add_host_js');


/* ------------------------------------------------
インラインスタイル削除 （フロント）
-------------------------------------------------*/
function remove_recent_comments_style() {
  global $wp_widget_factory;
  remove_action( 'wp_head', array( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style' ) );
}
add_action( 'widgets_init', 'remove_recent_comments_style' );


/* ------------------------------------------------
HEADタグをクリーン（フロント）
-------------------------------------------------*/
remove_action('wp_head', 'feed_links', 2);
remove_action('wp_head', 'feed_links_extra', 3);
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head');
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'rel_canonical');
remove_action('wp_head', 'index_rel_link');
remove_action('wp_head', 'parent_post_rel_link', 10, 0);
remove_action('wp_head', 'start_post_rel_link', 10, 0);
remove_action('wp_head', 'wp_shortlink_wp_head');


/* ------------------------------------------------
絵文字対応のスクリプトを無効化（フロント）
-------------------------------------------------*/
function disable_emoji() {
  remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
  remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
  remove_action( 'wp_print_styles', 'print_emoji_styles' );
  remove_action( 'admin_print_styles', 'print_emoji_styles' );
  remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
  remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
  remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
}
add_action( 'init', 'disable_emoji' );


/* ------------------------------------------------
WeriteRule（フロント）
-------------------------------------------------*/
//WORKS
add_rewrite_rule('works/?$', 'index.php?post_type=works', 'top');
add_rewrite_rule('works/page/([0-9]{1,})/?$', 'index.php?post_type=works&paged=$matches[1]', 'top');
add_rewrite_rule('lifestyle/([^/]+)/?$', 'index.php?lifestyle_category=$matches[1]', 'top');
add_rewrite_rule('works/(.+?)/page/?([0-9]{1,})/?$', 'index.php?works_category=$matches[1]&paged=$matches[2]', 'top');
add_rewrite_rule('(works)/(.+?)/([^/]+)(?:/([0-9]+))?/?$', 'index.php?post_type=works&slug=$matches[1]&works_category=$matches[2]&works=$matches[3]&page=$matches[4]', 'top');


/* ------------------------------------------------
タグ一覧にカスタム投稿タイプを表示（フロント）
-------------------------------------------------*/
function add_post_tag_archive( $wp_query ) {
  if ($wp_query->is_main_query() && $wp_query->is_tag()) {
    $wp_query->set( 'post_type', array('post','works'));
  }
}
add_action( 'pre_get_posts', 'add_post_tag_archive' , 10 , 1);


/* ------------------------------------------------
サイト内検索のカスタマイズ（フロント）
-------------------------------------------------*/
function custom_search($search, $wp_query) {
  global $wpdb;

  //検索ページ以外だったら終了
  if (!$wp_query->is_search)
  return $search;

  if (!isset($wp_query->query_vars))
  return $search;

  //タグ名・カテゴリ名・カスタムフィールドも検索対象にする
  $search_words = explode(' ', isset($wp_query->query_vars['s']) ? $wp_query->query_vars['s'] : '');
  if ( count($search_words) > 0 ) {
    $search = '';
    foreach ( $search_words as $word ) {
      if ( !empty($word) ) {
        $search_word = $wpdb->escape("%{$word}%");
        $search .= " AND (
          {$wpdb->posts}.post_title LIKE '{$search_word}'
          OR {$wpdb->posts}.post_content LIKE '{$search_word}'
          OR {$wpdb->posts}.ID IN (
            SELECT distinct r.object_id
            FROM {$wpdb->term_relationships} AS r
            INNER JOIN {$wpdb->term_taxonomy} AS tt ON r.term_taxonomy_id = tt.term_taxonomy_id
            INNER JOIN {$wpdb->terms} AS t ON tt.term_id = t.term_id
            WHERE t.name LIKE '{$search_word}'
          OR t.slug LIKE '{$search_word}'
          OR tt.description LIKE '{$search_word}'
          )
          OR {$wpdb->posts}.ID IN (
            SELECT distinct p.post_id
            FROM {$wpdb->postmeta} AS p
            WHERE p.meta_value LIKE '{$search_word}'
          )
        ) ";
      }
    }
  }
  return $search;
}
add_filter('posts_search','custom_search', 10, 2);


/* ------------------------------------------------
ページ表示件数の設定（フロント）
-------------------------------------------------*/
function change_posts_per_page($query) {
  if ( is_admin() || ! $query->is_main_query() )
    return;
  if( $query-> is_post_type_archive('works') || is_tax() || is_tag() || is_search() ) {
    $query-> set( 'posts_per_page', '16' );
    return;
  }
}
add_action( 'pre_get_posts', 'change_posts_per_page' );


/* ------------------------------------------------
サムネイルサイズ指定（フロント）
-------------------------------------------------*/
add_image_size( 'crop_thumbnail', 470, 320, array( 'center', 'center') );
add_image_size( 'crop_writer', 400, 400, array( 'center', 'center') );
add_image_size( 'crop_works_mv', 1000, 750, array( 'center', 'center') );


/* ------------------------------------------------
ページネーション設定（フロント）
-------------------------------------------------*/
function the_pagination() {
  global $wp_query;
  $bignum = 999999999;
  if ( $wp_query->max_num_pages <= 1 )
    return;
  $page_links = paginate_links( array(
    //'base'         => str_replace( $bignum, '%#%', esc_url( get_pagenum_link($bignum) ) ),
    //'format'       => '',
    //'current'      => max( 1, get_query_var('paged') ),
    'total'        => $wp_query->max_num_pages,
    'prev_text'    => '',
    'next_text'    => '',
    'mid_size'     => 1,
    'end_size'     => 1,
    'type'        => 'array',
  ) );
	echo '<ul class="m-list-02"><li class="m-list-02__item">';
	echo join( '</li><li class="m-list-02__item">', $page_links );
	echo '</li></ul>';
}


/* ------------------------------------------------
検索結果ページのURL変更（フロント）
-------------------------------------------------*/
function my_custom_search_url() {
  if ( is_search() && ! empty( $_GET['s'] ) ) {
    wp_safe_redirect( home_url( '/search/' ) . urlencode( get_query_var( 's' ) ) );
  exit();
  }
}
add_action( 'template_redirect', 'my_custom_search_url' );


/* ------------------------------------------------
カスタム投稿タイプ／WORKS（フロント・管理画面）
-------------------------------------------------*/
function cptui_register_my_cpts_works() {
  $labels = [
    "name" => __( "WORKS", "custom-post-type-ui" ), //メニューに表示される名前
    "singular_name" => __( "WORKS", "custom-post-type-ui" ), //メニューに表示される名前
  ];
  $args = [
    "label" => __( "WORKS", "custom-post-type-ui" ), //管理画面のメニュー、カスタム投稿一覧ページのタイトルに表示されるカスタム投稿タイプの名前
    "labels" => $labels, //管理画面に表示されるラベルの文字列を指定
    "description" => "", //コンテンツタイプの説明は、「すべての投稿」ページに表示
    "public" => true, //管理画面に表示しサイト上にも表示する
    "publicly_queryable" => true, //「?」パラメーターを経由してクエリできるか
    "show_ui" => true, //Rest APIを経由してクエリできるかどうか。
    "show_in_rest" => false, //新エディタのGutenbergを有効化
    "rest_base" => "", //Rest APIのクエリエンドポイント
    "rest_controller_class" => "WP_REST_Posts_Controller",
    "has_archive" => true, //アーカイブページを作成
    "show_in_menu" => true, //管理画面に表示
    "show_in_nav_menus" => true, //ナビゲーションメニューでこの投稿タイプが選択可能かどうか。
    "delete_with_user" => false, //ユーザーを削除した後、コンテンツも削除されるかどうか
    "exclude_from_search" => false, //検索から除外するかどうか
    "capability_type" => "post", //権限。postは​​、投稿者以上がアクセス可能
    "map_meta_cap" => true, //WordPress が持つデフォルトのメタ権限処理を使用
    "hierarchical" => false, //親子関係をもたせる
    "rewrite" => [ "slug" => "works", "with_front" => true ], //リライトルール
    "query_var" => true, //この投稿に使用する query_var キーの名前または真偽値
    "supports" => [ "title", "editor", "revisions", "author" ], //編集／新規作成のページに表示する項目（機能）を指定
    "taxonomies" => [ "works_category" ], //使用するタクソノミー
    "show_in_graphql" => false, //graphQL を有効化するかどうか
  ];
  register_post_type( "works", $args );
  register_taxonomy_for_object_type('post_tag', 'works');
}
add_action( 'init', 'cptui_register_my_cpts_works' );


/* ------------------------------------------------
カスタムタクソノミー／WORKS（フロント・管理画面）
-------------------------------------------------*/
function cptui_register_my_taxes_works_category() {
  $labels = [
    "name" => __( "カテゴリー", "custom-post-type-ui" ), //メニューに表示される名前
    "singular_name" => __( "カテゴリー", "custom-post-type-ui" ), //メニューに表示される名前
  ];
  $args = [
    "label" => __( "カテゴリー", "custom-post-type-ui" ),
    "labels" => $labels, //管理画面に表示されるラベルの文字列を指定
    "public" => true, //管理画面に表示しサイト上にも表示する
    "publicly_queryable" => true, //「?」パラメーターを経由してクエリできるか
    "hierarchical" => true, //親子関係をもたせる
    "show_ui" => true, //Rest APIを経由してクエリできるかどうか。
    "show_in_menu" => true, //管理画面に表示 //新エディタGut_nav_menus" => true, //ナビゲーションメニューでこの投稿タイプが選択可能かどうか。
    "query_var" => true, //この投稿に使用する query_var キーの名前または真偽値
    "rewrite" => [ 'slug' => 'works', 'with_front' => true, ],
    "show_admin_column" => true, //管理画面の投稿一覧でタクソノミーを表示
    "show_in_rest" => true,
    "show_tagcloud" => false,
    "rest_base" => "works",
    "rest_controller_class" => "WP_REST_Terms_Controller",
    "show_in_quick_edit" => true, //クイック編集の表示・非表示
    "show_in_graphql" => false, //graphQL を有効化するかどうか
  ];
  register_taxonomy( "works_category", [ "works" ], $args );
}
add_action( 'init', 'cptui_register_my_taxes_works_category' );


/* ------------------------------------------------
スラッグの日本語禁止（管理画面）
-------------------------------------------------*/
function auto_post_slug( $slug, $post_ID, $post_status, $post_type ) {
if ( preg_match( '/(%[0-9a-f]{2})+/', $slug ) ) {
  $slug = utf8_uri_encode( $post_type ) . '-' . $post_ID;
}
return $slug;
}
add_filter( 'wp_unique_post_slug', 'auto_post_slug', 10, 4 );


/* ------------------------------------------------
ログイン画面のスタイル適用（管理画面）
-------------------------------------------------*/
function admin_login_css() {
  echo '<link rel="stylesheet" type="text/css" href="' . get_bloginfo("template_directory") . '/admin/admin_login.css">';
}
add_action('login_head', 'admin_login_css');


/* ------------------------------------------------
管理画面用CSSを読み込み（管理画面）
-------------------------------------------------*/
function wp_custom_admin() {
  echo "\n" . '<link href="' .get_bloginfo('template_directory'). '/admin/wp-admin-custom.css' . '" rel="stylesheet">';
  echo "\n" . '<script src="' . get_bloginfo('template_directory') . '/admin/wp-admin-custom.js"></script>';
}
add_action('admin_head', 'wp_custom_admin', 100);


/* ------------------------------------------------
本体の更新通知を非表示（管理画面）
-------------------------------------------------*/
add_filter("pre_site_transient_update_core", "__return_null");


/* ------------------------------------------------
カスタム投稿を公開日順にソート（管理画面）
-------------------------------------------------*/
function set_post_types_admin_order($wp_query){
  if(is_admin()){
    $post_type = $wp_query->query['post_type'];
    if($post_type == 'news' || $post_type == 'usecase' || $post_type == 'works'){
      $wp_query->set('orderby','date');
      $wp_query->set('order','DESC');
    }
  }
}
add_filter('pre_get_posts', 'set_post_types_admin_order');


/* ------------------------------------------------
 Adminバーに「ログアウト」追加（管理画面）
-------------------------------------------------*/
function add_new_item_in_admin_bar() {
  global $wp_admin_bar;
  $wp_admin_bar->add_menu(array(
  'id' => 'new_item_in_admin_bar',
  'title' => __('ログアウト'),
  'href' => wp_logout_url()
  ));
  }
add_action('wp_before_admin_bar_render', 'add_new_item_in_admin_bar');


/* ------------------------------------------------
 全角スペースを入れても勝手に消させない（管理画面）
-------------------------------------------------*/
function my_tiny_mce_before_init_filter( $init_array ) {
  $init_array['remove_linebreaks'] = false;
  return $init_array;
}
add_filter('tiny_mce_before_init', 'my_tiny_mce_before_init_filter',10,3);


/* ------------------------------------------------
WordPress本体のアップデート通知を非表示にする（管理画面）
-------------------------------------------------*/
add_filter('pre_site_transient_update_core', '__return_zero');
remove_action('wp_version_check', 'wp_version_check');
remove_action('admin_init', '_maybe_update_core');


/* ------------------------------------------------
フッターのテキストを変更（管理画面）
-------------------------------------------------*/
function custom_admin_footer() {
  $template = get_template(); //テーマ
  $theme_data = wp_get_theme($template); //テーマオブジェクト
  $name = $theme_data->get( 'Name' ); //テーマの名前
  $description= $theme_data->get( 'Description' ); //テーマの説明
  $version = $theme_data->get( 'Version' ); //テーマのバージョン
  $theme_uri = $theme_data->get( 'ThemeURI' ); //テーマURI
  $author = $theme_data->get( 'Author' ); //テーマのオーナー
  $author_uri = $theme_data->get( 'AuthorURI' ); //テーマのオーナーURI
  $template = $theme_data->get( 'Template' ); //テーマのテンプレート
  $tags = $theme_data->get( 'Tags' ); //テーマのタグ
  $tags = implode(',',$tags); //タグの連結
  echo $name .' ' . $version . '';
}
add_filter( 'admin_footer_text', 'custom_admin_footer' );


/* ------------------------------------------------
カスタムウィジェット（管理画面）
-------------------------------------------------*/
function works_widget() {echo '
<ul>
  <li><a href="/manage/wp-admin/post-new.php?post_type=works">新規追加</a></li>
  <li><a href="/manage/wp-admin/edit.php?post_type=works">記事一覧（編集）</a></li>
</ul>
';}

function media_widget() {echo '
<ul>
  <li><a href="/manage/wp-admin/media-new.php">新規追加</a></li>
  <li><a href="/manage/wp-admin/upload.php">ライブラリ</a></li>
</ul>
';}

function add_original_widget() {
  wp_add_dashboard_widget( 'works_widget', 'WORKS', 'works_widget' );
  wp_add_dashboard_widget( 'media_widget', 'メディア管理', 'media_widget' );
}
add_action( 'wp_dashboard_setup', 'add_original_widget' );


/* ------------------------------------------------
 カテゴリーのチェックボックスをツリー構造化（管理画面）
-------------------------------------------------*/
function lig_wp_category_terms_checklist_no_top( $args, $post_id = null ) {
  $args['checked_ontop'] = false;
  return $args;
}
add_action( 'wp_terms_checklist_args', 'lig_wp_category_terms_checklist_no_top' );


/* ------------------------------------------------
ダッシュボードにカスタムエリアを追加（管理画面）
-------------------------------------------------*/
//ユーザー権限に対して管理画面サイドナビ表示設定
function remove_menus () {
  global $menu;

  //権限mac以外のメニュー設定
  if ( !(current_user_can('metasmaster'))  ) {
    $restricted1 = array(
    __('投稿'),
    __('固定ページ'),
    //__('メディア'),
    __('コメント'),
    // __('プラグイン'),
    // __('外観'),
    //__('ユーザー'),
    __('ツール'),
    // __('プロフィール'),
    // __('設定'),
    // __('カスタムフィールド'),
    //__('BackWPup'),
    __('WP ULike'),
    //__('新着情報一覧'),
    __('データベース'),
    //__('お問い合わせ'),
  );

  remove_menu_page('WpFastestCacheOptions'); // WpFastestCache
  // remove_menu_page('siteguard'); // siteguard
  remove_menu_page('toplevel_page_gadash_settings'); // gadash_settings
  remove_menu_page('gadash_settings'); // WpFastestCache
  // remove_menu_page('edit.php?post_type=acf'); // Advanced Custom Field
  remove_menu_page('page=copy-delete-posts'); // WP Fastest Cache

  end ($menu);
    while (prev($menu)){
    $value = explode(' ',$menu[key($menu)][0]);
      if(in_array($value[0] != NULL?$value[0]:"" , $restricted1)){
        unset($menu[key($menu)]);
      }
    }
  }
}
add_action('admin_menu', 'remove_menus', 999);



/* -----------------------------------------------------------------
左サイドメニューの順番入れ替え（管理画面）
/* -----------------------------------------------------------------*/
function custom_menu_order($menu_ord) {
  if (!$menu_ord) return true;
  return array(
    'index.php', // ダッシュボード
    'separator1', // 隙間その1
    'edit.php?post_type=works', // 固定ページ
    'options-general.php', // 設定
    'tools.php', // ツール
    'separator2', // 隙間その2
    'edit.php', // 最新情報
    'upload.php', // メディア
    'link-manager.php', // リンク
    'edit-comments.php', // コメント
    'themes.php', // 外観
    'users.php', // ユーザー
    'plugins.php', // プラグイン
    'separator-last', // 隙間その3
		'admin.php?page=wp-ulike-settings#tab=configuration', //
		'edit.php?post_type=acf-field-group',
  );
}
add_filter('custom_menu_order', 'custom_menu_order');
add_filter('menu_order', 'custom_menu_order');


/* -----------------------------------------------------------------
投稿一覧と編集画面の表示ボタンをクリックを別窓にする（管理画面）
------------------------------------------------------------------*/
function wp_custom_admin_target() {
  echo "<script>jQuery( function($) {
    $('#wp-admin-bar-site-name a, .row-actions .view a, #view-post-btn a, #sample-permalink a').click(function(){
      this.target = '_blank';
    });
  });
  </script>";
}
add_action('admin_head', 'wp_custom_admin_target', 100);


/* ------------------------------------------------
投稿のタグをチェックボックスで選択できるようにする（管理画面）
-------------------------------------------------*/
function change_post_tag_to_checkbox() {
  $args = get_taxonomy('post_tag');
  $args -> hierarchical = true;//Gutenberg用
  $args -> meta_box_cb = 'post_categories_meta_box';//Classicエディタ用
  register_taxonomy( 'post_tag', 'post', $args);
}
add_action( 'init', 'change_post_tag_to_checkbox', 1 );


/* ------------------------------------------------
WORKSの投稿一覧でカテゴリーでの絞り込みを追加（管理画面）
-------------------------------------------------*/
function my_add_filter_works(){
  global $post_type;
  if ('works' == $post_type) {
    ?>
    <select name="works_category">
      <option value="">カテゴリー指定なし</option>
      <?php
      $terms = get_terms('works_category');
      foreach ($terms as $term) { ?>
        <option value="<?php echo $term->slug; ?>" <?php if ($_GET['works_category'] == $term->slug) {
          print 'selected';
        } ?>><?php echo $term->name; ?></option>
        <?php
      }
      ?>
    </select>
    <?php
  }
}
add_action('restrict_manage_posts', 'my_add_filter_works');


/* ------------------------------------------------
投稿一覧の列の順番を変更（管理画面）
-------------------------------------------------*/
function sort_posts_column($columns){
  $columns = array(
    'cb' => '<input type="checkbox">',
    'title' => 'タイトル',
    'taxonomy-works_category' => 'カテゴリー',
    'tags' => 'タグ',
    'tags' => 'タグ',
    'date' => '日時',
  );
  return $columns;
}
add_filter( 'manage_posts_columns', 'sort_posts_column');


/* ------------------------------------------------
管理者以外は他の人がアップした画像を非表示（管理画面）
-------------------------------------------------*/
add_filter( 'ajax_query_attachments_args', 'wpb_show_current_user_attachments' );

function wpb_show_current_user_attachments( $query ) {
$user_id = get_current_user_id();
if ( $user_id && !current_user_can('activate_plugins') && !current_user_can('edit_others_posts
') ) {
$query['author'] = $user_id;
}
return $query;
}


/* ------------------------------------------------
管理者以外は他の人がアップした記事を非表示（管理画面）
-------------------------------------------------*/
function exclude_other_posts( $wp_query ) {
  if (!current_user_can('administrator')) {
    if ( isset( $_REQUEST['post_type'] ) && post_type_exists( $_REQUEST['post_type'] ) ) {
      $post_type = get_post_type_object( $_REQUEST['post_type'] );
      $cap_type = $post_type->cap->edit_other_posts;
    } else {
      $cap_type = 'edit_others_posts';
    }

    if ( is_admin() && $wp_query->is_main_query() && !$wp_query->get( 'author' ) && !current_user_can( $cap_type ) ) {
      $user = wp_get_current_user();
      $wp_query->set( 'author', $user->ID );
    }
  }
}
add_action( 'pre_get_posts', 'exclude_other_posts' );


/* ------------------------------------------------
タクソノミーのチェックを必須化（管理画面）
-------------------------------------------------*/
function post_edit_required() {
  ?>
  <script type="text/javascript">
  jQuery(function($) {
    if( 'works' == $('#post_type').val() ) {
      $('#post').submit(function(e) {
        if ( $('#works_categorydiv input:checked').length < 1) {
          alert('カテゴリーを選択してください');
          $('.spinner').css('visibility', 'hidden');
          $('#publish').removeClass('button-primary-disabled');
          $('#works_categorydiv a[href="#category-all"]').focus();
          return false;
        }
      });
    }
  });
  </script>
  <?php
  }
  add_action( 'admin_head-post-new.php', 'post_edit_required' );
  add_action( 'admin_head-post.php', 'post_edit_required' );

/* ------------------------------------------------
検索結果を投稿のみにする
-------------------------------------------------*/
function SearchFilter( $query ) {
  if ( $query -> is_search ) {
      $query->set( 'post_type', array('works') );
  }
  return $query;
}
add_filter( 'pre_get_posts', 'SearchFilter' );

/* ------------------------------------------------
管理画面にファビコンを設定する
-------------------------------------------------*/
function admin_favicon() {
  echo '<link  rel="icon" href="https://www.bellsring.net/favicon.ico">';
}
add_action('admin_head', 'admin_favicon');