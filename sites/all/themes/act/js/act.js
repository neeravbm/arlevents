(function ($) {
   Drupal.behaviors.acttab = {
        attach: function(context, settings) {
	      $(".tabs ul.primary li:last-child").addClass("last");
		    if($('.tabs ul.primary li a').hasClass('active')){
			    $(".tabs ul.primary").css('border-bottom','4px solid #850606');
		  	}
			
			/******** My ACT-IAC Vertical Tabs *********/
			 
			var tabs = $('#tabs-titles div'); 
			var contents = $('#tabs-contents div'); 
			 
			tabs.bind('click',function(){
			  contents.hide(); 
			  tabs.removeClass('current'); 
                          $(contents[$(this).index()]).show(); 
			  $(this).addClass('current');
			  return false;
			});


            var hometabs = $('.hometabs div'); 
			var homecontents = $('#tabs-contents div.hometabs'); 
			 
			hometabs.bind('click',function(){
			  homecontents.hide(); 
			  hometabs.removeClass('current'); 
                          $(homecontents[$(this).index()]).show(); 
			  $(this).addClass('current');
			  return false;
			});
			

		   /******** My ACT-IAC Vertical Tabs end*********/
		   
			
		   /******** Add even/odd class to a table *********/

		   $("#myevent tr:odd").addClass("odd");
		   $("#myevent tr:not(.odd)").addClass("even");  
            
        },
        resize: function() {
		}
    }
    
    $(window).resize(function() {
		Drupal.behaviors.acttab.resize();
	});	
})(jQuery);


(function($) {
	$(window).resize(function() {Drupal.behaviors.acttab.resize('body')});
})(jQuery);
 


 
