(function ($) {
  $(document).ready(function () {
    const limit = jQuery(window).width() < 770 ? 2 : 4;
    const imageToShow = jQuery(window).width() < 770 ? 3 : 5;
    if ($('.node--type-news .content-main .node__content .field--name-field-slike .field__item').length > limit) {
      $('.node--type-news .content-main .node__content .field--name-field-slike').append(function() {
        return $('<span class="more-images fas fa-angle-right"></span>').click(function () {
          $(".node--type-news .content-main .node__content .field--name-field-slike .field__item:nth-child(" + imageToShow + ") img").trigger("click");
        });
      })
    }
  })

})(jQuery);
