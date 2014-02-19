(function ($) {
	Drupal.behaviors.act = {
		attach: function(context, settings) {
                    
			$('#tabs div.tabcontent').hide();
                        
                        
                        if(Drupal.behaviors.act.findUrlParam('pass') != '') {
                           $('#tabs #tabcontent-changepwd').css('display','block')
                           $('#tabs #tab-changepwd').addClass('active');
                        } else {
                           $('#tabs .tabcontent:first').show();
                           $('#tabs .tabtitle:first').addClass('active');
                        }

			$('#tabs .tabtitle a').click(function(){
                            
                            $('#tabs .tabtitle').removeClass('active');
                            $(this).parent().addClass('active');
                            var currentTab = $(this).attr('href');
                            $('#tabs div.tabcontent').hide();
                            $(currentTab).show();
                            
                            return false;
			});
                        
		},
                findUrlParam: function(name) {
                    var results = new RegExp('[\\?&]' + name + '=([^&#]*)').exec(window.location.href);
                    return results != null ? results[1] || 0 : 0;
                },
		resize: function() {
                    
		    // Should be 980 but then stylign on full width breaks.
                    if(window.innerWidth <= 750) {
                        
                        // Applying Accordian 
                        $('.accr_sect, .gHeading').css('display','block');
                        $('.ui-tabs-nav,.gTabs').css('display','none');
                        $( "#accr, .gContent" ).accordion({ collapsible: true, autoHeight: false });
                        $( "#accr, .gContent" ).accordion( "option", "heightStyle", "content" );
                    } 
			else  {
                        
                        // Removing Accordian 
                        $('.ui-tabs-nav, .gTabs').css('display','block');
                        $('.accr_sect, .gHeading').css('display','none');
                        $( "#accr, .gContent" ).accordion( "destroy" );
                        $('.gInner.selected').removeAttr('style');
                        
                    } 
          
		}
	}

	$(window).resize(function() {
		Drupal.behaviors.act.resize();
	});
	
	$(document).ready(function() {
        var gcount = 1; 
        
        Drupal.behaviors.act.resize();
        
        $(".gTabs").children('[class!="media-tab"]').each(function() {
		
                addGatewayClickEvent($(this), gcount);
                $(this).addClass('tab'+gcount);
		gcount++;
   	 });
   	 
         var gcount = 1; 
          $(".gContent .gInner").each(function() {
              $(this).addClass('gInner-'+gcount);
              gcount++;
          });
         
	 function addGatewayClickEvent(listitem, counter){
		 $(listitem.children()).click(function() {
                     toggleGateway(this,counter); return false;
                 });
	 }
	function toggleGateway(obj,id){
                //$(obj).parent().attr('id')
                pobj  = $(obj).closest('.gatewayFeature');
                
		$(pobj).find('.gTabs li').removeClass("selected");
		$(pobj).find('.gInner').removeClass("selected");
                //var contentClass = ".gInner" + id;
		//var menuClass = ".gTabs .tab-" + id;
                var contentClass = ".gInner-" + id;
		var menuClass = ".gTabs .tab" + id;
		$(contentClass).addClass("selected");
		$(menuClass).addClass("selected");	
               
	}
         
        // Applying Accordian 
        $( "#accr_page_tab1,#accr_page_tab2,#accr_page_tab3,#accr_page_tab4" ).accordion({ collapsible: true, autoHeight: false });
        $( "#accr_page_tab1,#accr_page_tab2,#accr_page_tab3,#accr_page_tab4" ).accordion( "option", "heightStyle", "content" );
        
        // About Us page
        $.urlParam = function(name){
            var results = new RegExp('[\\?&]' + name + '=([^&#]*)').exec(window.location.href);
            return results != null ? results[1] || 0 : 0;
        }
        if( ($.urlParam('title') != '') || ($.urlParam('page') != '') ) {
            $('.doubleLineTabs .tab-4 a').trigger('click');
        }
        
        if ( document.location.href.indexOf('#glance') > -1 ) {
            $('.doubleLineTabs .tab-1 a').trigger('click');
        }
        if ( document.location.href.indexOf('#policy') > -1 ) {
            $('.doubleLineTabs .tab-2 a').trigger('click');
        }
        if ( document.location.href.indexOf('#history') > -1 ) {
            $('.doubleLineTabs .tab-3 a').trigger('click');
        }
        if ( document.location.href.indexOf('#members') > -1 ) {
            $('.doubleLineTabs .tab-4 a').trigger('click');
        }
        
        $('.gTabs li a').click(function(){
            path = $(this).attr('href');
            window.location.hash = path;
        });
        
});

})(jQuery);
