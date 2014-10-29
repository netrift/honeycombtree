/**
 * @file
 * Javascript for masonry image select page.
 */

(function ($) {

  $(document).ready(function() {

    var $container = $('#photo-display-container');

    $container.css('opacity', 0);
    $container.imagesLoaded( function() {
      $container.masonry ({
        columnWith: 200,
        itemSelector: '.selection-photo'
      });
      $container.hide().css('opacity',1).fadeIn();
    });

  });

})(jQuery);
