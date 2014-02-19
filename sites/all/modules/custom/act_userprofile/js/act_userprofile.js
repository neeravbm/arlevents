(function ($) {
   Drupal.behaviors.act_userprofile = {
        attach: function(context, settings) {
			 
			 if ($.browser.webkit || $.browser.safari) { // Chrome has both 'Chrome' and 'Safari' inside userAgent string.Safari has only 'Safari'.
			     $('.tabs ul li.last').css('margin-top', '0');
		     }
		     
		     
			 $('.all').click(function(){
				 $('#calendar_block .hok span .use-ajax').each(function(){
					  var selected_date = '';
					  selected_date = $(this).text();
					  $(this).attr("href", "/act/mydate/"+selected_date+"/all/nojs/");
				  });
				});
			$('.rsvp').click(function(){
				$('#calendar_block .hok span .use-ajax').each(function(){
					  var selected_date = '';
					  selected_date = $(this).text();
					  $(this).attr("href", "/act/mydate/"+selected_date+"/rsvp/nojs/");
				    });
				});
		}
    }
})(jQuery);
