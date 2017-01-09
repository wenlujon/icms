(function($){
  'use strict'

  // Start resizising library iframe
  var
    $images_frame = jQuery('iframe#fancy-gallery-library-window:first'),
    images_frame = $images_frame.get(0);

  var resize_library_iframe = function(){
    if (images_frame.contentWindow.document.documentElement){
      if ( images_frame.contentWindow.document.documentElement.scrollHeight == $images_frame.attr('height') ){
        // Size the frame down until the scrollHeight is higher than the frame height
        while ( images_frame.contentWindow.document.documentElement.scrollHeight == $images_frame.attr('height') && $images_frame.attr('height') > 250 ){
          $images_frame.attr('height', parseInt(images_frame.contentWindow.document.documentElement.scrollHeight * 0.9) );
        }
      }
      else if (images_frame.contentWindow.document.documentElement.scrollHeight > $images_frame.attr('height')){
        // Size the frame up!!!
        $images_frame.attr('height', images_frame.contentWindow.document.documentElement.scrollHeight - 1 );
      }
    }

    // Next run in 0.400sec.
    window.setTimeout(resize_library_iframe, 400);
  };
  resize_library_iframe();

  $('div#meta-box-show-code input.gallery-code').focus(function(){
    this.select();
  });

  $('div#meta-box-excerpt h3 input').click(function(){
    $(this).parent().next('.togglebox').slideDown().siblings('.togglebox').slideUp();
  })
  .filter(':checked').trigger('click');

  /* Submit the images iframe when the gallery will be saved */
  $('form#post').submit(function(event){
    var
      $this = $(this),
      $gallery_form = $images_frame.contents().find('form#gallery-form'),
      form_data = $gallery_form.serialize(),
      post_url = $gallery_form.attr('action');

    if ($gallery_form.length > 0){
      event.preventDefault();
      $.post(post_url, form_data, function(data, textStatus, jqXHR){
        $gallery_form.remove();
        $this.find('input#publish').trigger('click');
      });
    }

  });

})(jQuery);