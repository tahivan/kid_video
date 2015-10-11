/*
 * Slider Settings
 */
jQuery(document).ready(function(){
   jQuery('.widget_slider_area_rotate').bxSlider({
      mode: 'horizontal',
      speed: 1500,
      auto: true,
      pause: 5000,
      adaptiveHeight: true,
      nextText: '<div class="category-slide-next"><i class="fa fa-angle-right"></i></div>',
      prevText: '<div class="category-slide-prev"><i class="fa fa-angle-left"></i></div>',
      pager: false,
      tickerHover: true
   });
});

/*
 * News in picture setting
 */
jQuery(document).ready(function(){
   jQuery('.widget_block_picture_news .widget_highlighted_post_area').bxSlider({
      minSlides: 1,
      maxSlides: 2,
      slideWidth: 390,
      slideMargin: 20,
      speed: 1500,
      pause: 5000,
      adaptiveHeight: true,
      nextText: '<div class="slide-next"><i class="fa fa-angle-right"></i></div>',
      prevText: '<div class="slide-prev"><i class="fa fa-angle-left"></i></div>',
      pager: false,
      captions: false
   });
});

/*
 * Thumbnail/Images pager slider settings
 */
jQuery(document).ready(function(){
   jQuery('.thumbnail-big-sliders').bxSlider({
      pagerCustom: '.thumbnail-slider',
      captions: false,
      nextText: '',
      prevText: '',
      nextSelector: '',
      prevSelector: ''
   });
});

/*
 * Breaking News
 */
jQuery(document).ready(function(){
   var breaking_news_example = jQuery('#breaking-news-widget').newsTicker({
      row_height: 100,
      max_rows: 3,
      duration: 4000,
      prevButton: jQuery('#breaking-news-widget-prev'),
      nextButton: jQuery('#breaking-news-widget-next')
   });
});

/*
 * Ticker images news settings
 */
jQuery(document).ready(function(){
   jQuery('.image-ticker-news').bxSlider({
      minSlides: 5,
      maxSlides: 5,
      slideWidth: 150,
      slideMargin: 12,
      ticker: true,
      speed: 50000,
      tickerHover: true,
      useCSS: false
   });
});
/* Content via Ajax */
jQuery(document).ready(function(){
   jQuery('.colormag-ticker-news-popup-link').magnificPopup({
      type: 'ajax',
      callbacks: {
         parseAjax: function( mfpResponse ) {
            var setting = jQuery.magnificPopup.instance,
            content = jQuery( setting.currItem.el[0] ),
            fragment = ( content.data( 'fragment' ) );
            mfpResponse.data = jQuery( mfpResponse.data ).find( fragment );
         }
      }
   });
});

/*
 * Gallery Image PopUp Setting
 */
jQuery(document).ready(function(){
   jQuery('.gallery').magnificPopup({
      delegate: 'a',
      type: 'image',
      gallery: { enabled:true }
   });
});