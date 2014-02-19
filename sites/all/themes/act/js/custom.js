(function ($) {

  Drupal.behaviors.header_menu = {
    resize: function(context, settings) { 
	   
	  if(window.innerWidth <= 1000) {
         $("#block-block-12 a").html("<b>Login</b>");
	     $("#block-block-11 a").html("<b>Register</b>");
	  } else {
	     $("#block-block-12 a").html("Login as<br /><span>Member</span>");
	     $("#block-block-11 a").html("Become a<br /><span>Member</span>");
	  }
	  
	  //alert($('body.page-checkout').text());
	  
	  $('#edit-commerce-payment-payment-details-name').attr('size','25');
      
	  var tabwidth = $('.page-user-dashboard .tabs ul').width();
	  
	  if(tabwidth < 500) {
		 $('.page-user-dashboard .tabs ul.primary').css('border-left','none');
		 $('.page-user-dashboard .tabs ul.primary').css('border-top','none');
		 $(".page-user-dashboard .tabs ul.primary").css('background','none');
		 $(".page-user-dashboard .tabs ul.primary").css('border','none');
		 $(".page-user-dashboard .tabs ul.primary li a").css('float','left');
		 $(".page-user-dashboard .tabs ul.primary li a").css('border-width','0');
         $(".page-user-dashboard .tabs ul.primary li a").css('padding-top','10px');
         $(".page-user-dashboard .tabs ul.primary li a").css('padding-left','10px');
         $(".page-user-dashboard .tabs ul.primary li a").css('padding-right','10px');
         $(".page-user-dashboard .tabs ul.primary li a").css('padding-bottom','10px');
         $(".page-user-dashboard .tabs ul.primary li a").css('width','95%');
         /*$(".page-user-dashboard .tabs ul li.last").css('float','none');*/
         
        } else {
		 $(".page-user-dashboard .tabs ul.primary").css('background','#D3CECE');
	     $('.page-user-dashboard .tabs ul.primary').css('border-left','4px solid #D3CECE');
		 $('.page-user-dashboard .tabs ul.primary').css('border-top','4px solid #D3CECE');
		 $(".page-user-dashboard .tabs ul.primary").css('border-bottom','4px solid #850606');
		 $(".page-user-dashboard .tabs ul.primary li a").css('border-width','5px');
		 $(".page-user-dashboard .tabs ul.primary li a").css('float','none');
         $(".page-user-dashboard .tabs ul.primary li a").css('padding-bottom','4px');
         $(".page-user-dashboard .tabs ul.primary li a").css('padding-top','5px');
         $(".page-user-dashboard .tabs ul.primary li a").css('width','auto');
         /*$(".page-user-dashboard .tabs ul li.last").css('float','right');*/
         $(".page-user-dashboard .tabs ul li.last a").css('padding-bottom','2px');
       }
	  
	   /******* Manage layout in latest-event page *****/
	  if(window.innerWidth < 740) {
		$('.events-keynote-speaker-details .keynote_speaker_image').each(function(){
		   $(this).parent().parent().css('margin-top',0);	
		});  
		 
	  } else {
		$('.events-keynote-speaker-details .keynote_speaker_image').each(function(){
		  if($(this).html() == ''){
			   $(this).parent().parent().css('margin-top',0);	
			} else {
			   $(this).parent().parent().css('margin-top', '-18%');	
			}
		});
	  }
	  
	  if(window.innerWidth < 740) {
		$('.latest-events-keynote-speaker-details .keynote_speaker_image').each(function(){
		   $(this).parent().parent().css('margin-top',0);	
		});  
		 
	  } else {
		$('.latest-events-keynote-speaker-details .keynote_speaker_image').each(function(){
		  if($(this).html() == ''){
			   $(this).parent().parent().css('margin-top',0);	
			} else {
			   $(this).parent().parent().css('margin-top', '-18%');	
			}
		});
	  }
	  
    },
    
    heightResize : function (context, settings) {
        var flag;
        $('.innerpage-standard .col').each(function() {
           
           if(flag) {
                flag = false;

            }else {
                flag= true;
                
                firstHeight = $(this).height();    
                secondHeight = $(this).next().height();
                
                maxHeight = (firstHeight > secondHeight) ? firstHeight : secondHeight;
            }
            
            if(maxHeight > 0 ) {
                $(this).height((maxHeight + 212));  
            }
            
        });  
    },
    
    attach: function(context, settings) {
        $(".col:odd").addClass('oddcol');
        $(".col:even").addClass("evencol");
        $(".col:last").addClass("lastcol");
        $(".col:first").addClass("firstcol");
        $(".innerpage:odd").addClass("oddpage");
        $(".innerpage:even").addClass("evenpage");
        $(".innerpage:last").addClass("lastpage");
        $(".innerpage:first").addClass("firstpage");
        $('.not-logged-in .events-addtocart .form-submit').attr("disabled", "disabled");
        $('.not-logged-in .anonymous-addtocart .form-submit').attr("disabled", "disabled");
        $('.view-event-session-track .register-allow-1 #edit-submit').attr("disabled", "disabled");
        
        //var highestCol = Math.max($('.field-collection-container .field-items .field-item .field-collection-view:odd').height(),$('.field-collection-container .field-items .field-item .field-collection-view:even').height());
        //$('.field-collection-container .field-items .field-item').height(highestCol);
        
        /******* Hide tab if it show only view *****/
        if($('#eventcustom_tabs ul li').length == 1){
		  $('#eventcustom_tabs').hide();
	    }
        
        
        /******* Hide icons if there is no record at latest event page *****/
       $('.views-field-field-venue-address .field-content').each(function(){
	     if($(this).text() == ''){
		   $(this).parent().css('background','none');	
		 } 
	   });
	   
	   $('.events-keynote-speaker-details .field-content').each(function(){
		 if(($(this).text()).length == 17){
		   $(this).find(".event-summary").css('background','none');	
		 } 
	   });
	   
	   $('.latest-events-keynote-speaker-details .field-content').each(function(){
		  if(($(this).text()).length == 17){
			$(this).children().next().css('background','none');	
		  } 
	   });
	   
	   $('.views-field-field-event-date .events-date').each(function(){
		 if($(this).text() == '' || $(this).text() == null){
		   $(this).parent().css('background','none');
		 } 
	   });
	   
	   if($('#event_venue_container #event_venue .venue-info').text() == ''){
		  $('#event_venue_container #event_venue .venue-icon').html('');
	    }
        
       

      var searchString = "Search";
      $('#block-search-form .form-text').focus(function() {searchFocusNew(this, searchString);});
      $('#block-search-form .form-text').blur(function() {searchBlurNew(this, searchString);});
      
      function searchFocusNew(input, str_value){
	if(input.value == str_value){input.value ="";}
      }
      function searchBlurNew(input, str_value){
	if(input.value == ""){input.value = str_value;}
      }
      $("#region-branding .block-menu .menu").hide();
      $('#region-branding .block-menu .block-title').click(function(){
        $("#region-branding .block-menu .menu").toggle('slow');
        return false;
      });
				
      $('.search-link').click(function(){
        $('#block-search-form').toggle('slow');
        var searchtxt = $('#block-search-form .custom-search-box').val();
	    if(searchtxt != '' && searchtxt != null){
	       $('#search-block-form').submit();
        }
	    return false; 
      });
      
      Drupal.behaviors.header_menu.resize(context);
      
     }
  }
  
  $(document).ready(function(){
    Drupal.behaviors.header_menu.heightResize();
    /*$("#month_show option[value='rsvp']").attr("selected","selected");*/
    if($('.block-cck-blocks-field-silver-sponsor').length) {
        if($('.block-cck-blocks-field-silver-sponsor div.field-items').html() == '' ) 
            $('.block-cck-blocks-field-silver-sponsor').css('display', 'none');
    }
   
   if($('.sharethis-wrapper').length) {
        printHTML = "<span class='sh_print_large'><span class='shprint' onclick='window.print();'></span></span>";
        $('.sharethis-wrapper').append(printHTML);
        $("#block-system-main-menu .menu").hide();
        //$('#search-block-form').hide();
    }

    $('#block-commerce-cart-cart h2.block-title').click(function() {
      $(this).siblings('.content').toggle(10);
    });
    
  });
  
})(jQuery);


(function($) {
  $(window).resize(function() {Drupal.behaviors.header_menu.resize('body')});
})(jQuery);
