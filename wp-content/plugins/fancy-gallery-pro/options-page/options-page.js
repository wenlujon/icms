(function($){

  // Add the line highlighting
  $('div.capability-selection:odd').addClass('highlight1');
  $('div.capability-selection:odd:odd').addClass('highlight2');

	// Add a confirmation message to the delete link
  $('a.delete-link').click(function(){
    return confirm( $delete_confirm_message );
  });

})(jQuery);