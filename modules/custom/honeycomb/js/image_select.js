/**
 * @file
 * Javascript for masonry image select page.
 */

(function ($) {

  $(document).ready(function() {

    var $container = $('#photo-display-container');

    $container.imagesLoaded( function() {
      $container.masonry ({
        columnWith: 200,
        itemSelector: '.selection-photo'
      });
    });

  });

})(jQuery);
