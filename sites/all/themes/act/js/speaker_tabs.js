(function ($) {
    $(document).ready(function() {
	var gcount = 1; 
	$("#customgTabs").children().each(function() {
		addGatewayClickEvent($(this), gcount);
		gcount++;
   	 });
   	 
	 function addGatewayClickEvent(listitem, counter){
		$(listitem.children()).click(function() {
		  toggleGatewayMain(listitem.attr('id')); return false;
        });
	 }
	
	
	function toggleGatewayMain(id){
	  $("#"+id).siblings().removeClass("selected");
	  $("#content"+id).siblings().removeClass("selected");	
	  $("#"+id).addClass("selected");
	  $("#content"+id).addClass("selected");	
    }
  });
})(jQuery);
