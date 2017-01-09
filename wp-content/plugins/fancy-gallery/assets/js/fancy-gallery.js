(function($){
  'use strict';

  // Render the template stylesheets and javascripts
  if(FANCYGALLERY.stylesheets || FANCYGALLERY.javascripts){
    var $head = $('head:first');

    if(FANCYGALLERY.stylesheets){
      for(var index in FANCYGALLERY.stylesheets){
        $('<link rel="stylesheet" href="" media="screen">').attr('href', FANCYGALLERY.stylesheets[index]).appendTo($head);
      }
    }

    if(FANCYGALLERY.javascripts){
      for(var index in FANCYGALLERY.javascripts){
        $('<script type="text/javascript"></script>').attr('src', FANCYGALLERY.javascripts[index]).appendTo($head);
      }
    }
  }

  // Associate the links with the lightbox
  if (FANCYGALLERY.lightbox){
    // Default options for the lightbox
    var options = {
      'gallery_relation_attr':  'rel',
      'container':              'div.fancy-gallery-lightbox-container',
      'image_selector':         'a[href$=".jpg"], a[href$=".jpeg"], a[href$=".png"], a[href$=".gif"], a[href$=".bmp"], a[href$=".wbmp"], .image-lightbox',
      'titleElement':           '.title',
      'continuous':             FANCYGALLERY.continuous, // Allow continuous navigation, moving from last to first and from first to last slide
      'slideshowInterval':      parseFloat(FANCYGALLERY.slideshow_speed), // Delay in milliseconds between slides for the automatic slideshow
      'preloadRange':           parseFloat(FANCYGALLERY.preload_images), // The number of elements to load around the current index
      'transitionSpeed':        parseFloat(FANCYGALLERY.animation_speed), // The transition speed between slide changes in milliseconds
      'stretchImages':          FANCYGALLERY.stretch_images, // Defines if images should be stretched to fill the available space. Values: "cover", "contain", false
      'onopen':                 function(){ $('html').css('overflow', 'hidden') },
      'onclosed':               function(){ $('html').css('overflow', '') }
    };

    // Associate the links with the lightbox
    $(document).on('click', options.image_selector, function(event){
      event.preventDefault();

      var
        $link = $(this),
        gallery_relation = $link.attr(options.gallery_relation_attr),
        $gallery = [],
        $gallery_wrapper = $link.parents('.gallery.fancy-gallery:first'),
        $content_wrapper = $link.parents('.fancy-gallery-content-unit:first');

      // If the relation attribute is empty we need to remove it from all other link tags which have empty relation attributes
      if (!gallery_relation){
        $(options.image_selector)
          .filter(function(){ return !$(this).attr(options.gallery_relation_attr); })
          .removeAttr(options.gallery_relation_attr);
      }

      // Find other linked images which belongs to this gallery
      if (gallery_relation)
        $gallery = $('a[' + options.gallery_relation_attr + '="' + gallery_relation + '"]');
      else if ($gallery_wrapper.length)
        $gallery = $gallery_wrapper.find(options.image_selector);
      else if ($content_wrapper.length)
        $gallery = $content_wrapper.find(options.image_selector).not('a[' + options.gallery_relation_attr + '], .gallery.fancy-gallery a');
      else
        $gallery = $(options.image_selector).not('.fancy-gallery-content-unit a, a[' + options.gallery_relation_attr + '], .gallery.fancy-gallery a');

      // Set link index and event
      $.extend(options, {
        'index': $gallery.index( $link ),
        'event': event
      });

      // Enabled descriptions
      options.onslide = function(index, slide){
        $(this.container).find('.description').empty().html( this.list[index].description );
      };

      // Prepare the gallery set of image links
      $gallery.each(function(index, gallery_item){
        var
          $gallery_item = $(gallery_item),
          href = $gallery_item.attr('href'),
          thumbnail = $gallery_item.find('img:first').attr('src') || href,
          title = $gallery_item.attr('title') || $gallery_item.find('img:first').attr('title'),
          description = $gallery_item.data('description') /* || $gallery_item.data('caption') || $gallery_item.siblings('figcaption:first').text() */ ;

        // modify the gallery set
        $gallery[index] = {
          'title':        title,
          'description':  description,
          'href':         href,
          'thumbnail':    thumbnail
        };
      });

      // Start the lightbox
      new blueimp.Gallery($gallery, options);
    });
  }

}(jQuery));