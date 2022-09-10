var breakPoint = 767;
var winWidth = window.innerWidth;
var timer = false;

//----------------------------------------------------
//  Load
//----------------------------------------------------
$(function(){
  $(window).on('load', function() {
    $('body').addClass('is-shown');
  });
});

//----------------------------------------------------
//  画像置換
//----------------------------------------------------
$(function(){
  $(window).on('load resize', function() {

    if (timer !== false) {
      clearTimeout(timer);
    }
    timer = setTimeout(function() {

      if( winWidth <= breakPoint ){
        // 画像置換（PC→SP）
        $('.rwd').each(function(){
          $(this).attr("src",$(this).data("img").replace('_pc', '_sp'));
        });
      }else {
        // 画像置換（SP→PC）
        $('.rwd').each(function(){
          $(this).attr("src",$(this).data("img"));
        });
      }
    }, 10);
  });
  $(window).trigger('resize');
});

//----------------------------------------------------
//  アニメーション
//----------------------------------------------------
$(window).on('scroll load', function(){
  $('.js-fadeup-row').each( function(){
    $(this).children('.js-fadeup-row-child').each(function(i){
      $(this).css({
        'transition-delay' : (i * .8) + 's' // 時差
      });
    });
  });

  $('.js-fadeup, .js-fadeup-row').each(function(){
    var elemPos = $(this).offset().top;
    var scroll = $(window).scrollTop();
    var windowHeight = $(window).height();
    if (scroll > elemPos - windowHeight + 300){
      $(this).addClass('is-visible');
    }
  });

  $('.js-fadeup-top').each(function(){
    var elemPos = $(this).offset().top;
    var scroll = $(window).scrollTop();
    var windowHeight = $(window).height();
    if (scroll > elemPos - windowHeight + 300){
      $(this).addClass('is-visible');
    }
  });
});

//----------------------------------------------------
//  tel
//----------------------------------------------------
$(function(){
  var ua = navigator.userAgent;
  if (ua.indexOf('iPhone') > 0 || ua.indexOf('Android') > 0 && ua.indexOf('Mobile') > 0) {
    // smartphone
  } else if (ua.indexOf('iPad') > 0 || ua.indexOf('Android') > 0) {
    // tablet
  } else {
    // PC
    $('a[href^=tel]').css({
      'cursor' : 'default',
      'pointer-events' : 'none'
    });
    $('a[href^=tel]').click(function(){
      return false;
    });
  }
});

//----------------------------------------------------
//  スムーススクロール
//----------------------------------------------------
$(function () {
  $('a[href^="/#"]').click(function() {
    setTimeout(function(){
      var urlHash = location.hash;
      $('body,html').stop().scrollTop(0);
      var target = $(urlHash);
      var position = target.offset().top;
      $('body,html').stop().animate({scrollTop:position}, 500, 'swing');
    }, 100);
  });
  $('a[href^="#"]').click(function() {
    var href= $(this).attr("href");
    var target = $(href == "#" || href == "" ? 'html' : href);
    var position = target.offset().top;
    $("body, html").animate({scrollTop:position}, 500, "swing");
    return false;
  });
});

//----------------------------------------------------
//  header
//----------------------------------------------------
$(function(){
  $('#header-btn').on('click', function() {
    $(this).toggleClass('is-open');
    $('#header-nav').slideToggle(300);
    $('#header-nav-wrapper').toggleClass('is-open');
  });
});
$(function(){
  $(window).on('load resize', function() {

    if (timer !== false) {
      clearTimeout(timer);
    }
    timer = setTimeout(function() {

      if( winWidth <= breakPoint ){
        // PC→SP
        $('#header-nav a, #header-nav-wrapper').on('click', function() {
          $('#header-btn').removeClass('is-open');
          $('#header-nav').slideUp(300);
          $('#header-nav-wrapper').removeClass('is-open');
        });
      }
    }, 10);
  });
  $(window).trigger('resize');
});

//----------------------------------------------------
//  modal
//----------------------------------------------------
$(function(){
  $('.m-card-01').on('click', function(e) {
    e.preventDefault();
    var id = $(this).attr('id');
    var indexArr = id.split('modal-btn-show-');
    var index = indexArr[1];
    var scrollPosition = $(window).scrollTop();
    $('body').addClass('is-fixed').css({'top': -scrollPosition});
    var modalSelector = '#modal-content-' + index;
    $(modalSelector).toggleClass('is-shown');
    $('#modal-wrapper').toggleClass('is-shown');
    $('#modal-container').toggleClass('is-shown');
    $('#modal-wrapper, .m-modal-01__btn').on('click', function() {
      $('body').removeClass('is-fixed').css({'top': 0});
      window.scrollTo( 0 , scrollPosition );
      $(modalSelector).removeClass('is-shown');
      $('#modal-wrapper').removeClass('is-shown');
      $('#modal-container').removeClass('is-shown');
    })
  })
});