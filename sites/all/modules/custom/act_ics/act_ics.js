/*function actics_popup() {
   // jQuery("#actics_popup").toggle();
    var elem = jQuery(".actics_popup")[0];
	if(elem.style.display == 'none')
	     jQuery(".actics_popup").show();
	else
	{
	     jQuery(".actics_popup").hide();   			
	}
}

function pretty_monthshow() {
     //jQuery("#month_show").toggle();
     var elem = jQuery("#month_show")[0];
	if(elem.style.display == 'none')
	     jQuery("#month_show").show();
	else
	{
	     jQuery("#month_show").hide();   			
	}
}*/

function actics_popup() {
   jQuery("#actics_popup").toggle();
    
}

function pretty_monthshow() {
     jQuery("#month_show").toggle();
}

(function ($) {
   Drupal.behaviors.act_ics = {
       
        attach: function(context, settings) {
        },
        adjustPrettyCalendar: function() {
                    
                    if($('calendar-daynames.processed').length) return;
                   
                    last = $('.calendar-daynames  .pretty-calendar-last').html();
                    last = '<div class="pretty-calendar-weekend pretty-calendar-last">'+last+'</div>';

                    $('.calendar-daynames .pretty-calendar-last').remove();
                    $('.calendar-daynames').prepend(last);

                    var elem,elem2,elemblank;
                    elem=elem2=elemblank='';

                    elemblank = '<div class="pretty-calendar-day blank">'+$(".pretty-calendar-day.blank").html()+'</div>';

                    // Weekdays
                    $(".pretty-calendar-week").each(function(){
                    obj = $(this);

                    if(elem !== '')     elem2 = elem;

                    elem = obj.find('.pretty-calendar-last').html();
                    elem = '<div class="pretty-calendar-weekend pretty-calendar-last">'+elem+'</div>';
                    obj.find('.pretty-calendar-last').remove();

                        if(elem2 == '') {
                        obj.prepend(elemblank);  
                        }  
                        else {
                          obj.prepend(elem2);
                        }

                    });
                    
                   $('.calendar-daynames').addClass('processed'); 

	}
    }
    
    $(document).ready(function(){
         Drupal.behaviors.act_ics.adjustPrettyCalendar();
    });
   
    
})(jQuery);

        
        

