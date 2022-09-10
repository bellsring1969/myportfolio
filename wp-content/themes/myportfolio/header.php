<?php $webroot = $_SERVER['DOCUMENT_ROOT']; ?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex" />
  <?php if ( is_front_page()): //title ?>
  <title>My Portfolio | T.S.のポートフォリオ</title>
  <?php elseif ( is_post_type_archive('works') ): ?>
  <title>Works | My Portfolio | T.S.のポートフォリオ</title>
  <?php elseif ( is_tax('works_category') ): ?>
  <title><?php single_cat_title(); ?> | Works | My Portfolio | T.S.のポートフォリオ</title>
  <?php elseif ( is_404() ): ?>
  <title>404 File Not Found | My Portfolio | T.S.のポートフォリオ</title>
  <?php endif; ?>
  <?php if ( is_front_page()): //description ?>
  <meta name="description" content="T.S.のポートフォリオサイトです。">
  <?php elseif ( is_post_type_archive('works') ): ?>
  <meta name="description" content="T.S.のポートフォリオサイトの制作物一覧ページです。これまでに携わった主な案件をまとめました。">
  <?php elseif ( is_404() ): ?>
  <meta name="description" content="404 File Not Foundページです。">
  <?php endif; ?>
  <link rel="canonical" href="<?php echo get_pagenum_link();?>">
  <link rel="alternate" hreflang="ja" href="<?php echo get_pagenum_link();?>">
  <link rel="icon" href="https://bellsring.net/favicon.ico">
  <link rel="stylesheet" href="/assets/css/style.css">
  <?php if ( is_front_page() ): ?>
  <link rel="stylesheet" href="/assets/css/page/top.css">
  <?php elseif ( is_post_type_archive('works') || is_tax('works_category') ): ?>
  <link rel="stylesheet" href="/assets/css/page/works.css">
  <?php elseif ( is_404() ): ?>
  <link rel="stylesheet" href="/assets/css/page/404.css">
  <?php endif; ?>
  <?php wp_head(); ?>
</head>
<body>
  <?php require_once($webroot."/assets/inc/header.html"); ?>