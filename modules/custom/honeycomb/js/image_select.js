/**
 * @file
 * Javascript for masonry image select page.
 */

(function ($) {

  $(document).ready(function() {

    $(document).ajaxSuccess(function( event, xhr, settings) {
      if (settings.url.indexOf("modal-image") != -1) {
        // colorbox just made an ajax call and we need to bind the new ajax link
        // that was just genereated.
        addajax();
      };
    });

    var $container = $('#photo-display-container');

    $container.css('opacity', 0);
    $container.imagesLoaded( function() {
      $container.masonry ({
        columnWith: 200,
        itemSelector: '.selection-photo'
      });
      $container.hide().css('opacity',1).fadeIn();
    });


    // Colour init.
    $('a.colorbox').colorbox({
      scalePhotos: true,
      srolling: false,
      width: '90%',
      height: '90%',
    });

  }); // end document

  function addajax() {
    // Bind Ajax behaviors 
    jQuery('.use-ajax:not(.ajax-processed)').addClass('ajax-processed').each(function ()    {
      var element_settings = {};
      // Clicked links look better with the throbber than the progress bar.
      element_settings.progress = { 'type': 'throbber' };
  
      // For anchor tags, these will go to the target of the anchor rather
      // than the usual location.
      if (jQuery(this).attr('href')) {
        element_settings.url = jQuery(this).attr('href');
        element_settings.event = 'click';
      }
      var base = jQuery(this).attr('id');
      Drupal.ajax[base] = new Drupal.ajax(base, this, element_settings);
    });
  }
  

})(jQuery);
