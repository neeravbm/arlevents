function calendar_go(dir, action, uid) {
(function ($) {
	var uid = Drupal.settings.act_userprofile.uid;
	$("#calendar-body").html("<div class='calendar_loader'><div></div></div>" + $("#calendar-body").html());
    $.get(Drupal.settings.basePath + "calendar_get/" + $(".calendar-" + dir).parent().attr("rel")+'/'+action+'/'+uid, function(data){
      $("#calendar-body").html(" ");
      $("#calendar-body").append(data);
      $(".tooltip").tipTip({defaultPosition: "right", maxWidth: "400px", keepAlive:true});
      $("#calendar-body .calendar-daynames .calendar-value").each(function(){
        $(this).html($(this).html().substr(0,2) + '<span class="third-letter">' + $(this).html().substr(2,1) + '</span>');
      });
      Drupal.behaviors.act_ics.adjustPrettyCalendar();
    });
    
  })(jQuery);  
}


function calendar_data(cdate, action, uid) {
  (function ($) {
	var uid = Drupal.settings.act_userprofile.uid;
	$("#block-act-userprofile-dashboardevent").html("<div class='calendar_loader'><div></div></div>" + $("#block-act-userprofile-dashboardevent").html());
    $.get(Drupal.settings.basePath + "calendar_get_data/" +cdate+'/'+action+'/'+uid, function(data){
      $("#block-act-userprofile-dashboardevent").html(" ");
      $("#block-act-userprofile-dashboardevent").append(data);
       //Drupal.behaviors.act_ics.adjustPrettyCalendar();
    });
    
    $('.pretty-calendar-day .calendar-value a').click(function(){
	   $('.pretty-calendar-day .calendar-value').removeClass('active');
	   $(this).parent().addClass('active');	
	});
   
  })(jQuery);  
}



function calendar_active(active_date, action, uid) {
  (function ($) {
	var uid = Drupal.settings.act_userprofile.uid;
	$.get(Drupal.settings.basePath + "calendar_get_active_date/" +active_date+'/'+action+'/'+uid, function(data){
      $("#block-act-userprofile-dashboardevent").html(" ");
      $("#block-act-userprofile-dashboardevent").append(data);
    });
  })(jQuery);  
}



 /*
 * TipTip
 * Copyright 2010 Drew Wilson
 * www.drewwilson.com
 * code.drewwilson.com/entry/tiptip-jquery-plugin
 * -----
 * Modified version for AJAX preloading.
 */
(function($){
		  
  $.fn.tipTip = function(options) {
    var defaults = { activation: "hover", keepAlive: false, maxWidth: "200px", edgeOffset: 3, defaultPosition: "bottom", delay: 400, fadeIn: 200, fadeOut: 200, attribute: "title", content: false, enter: function(){}, exit: function(){} };
     var opts = $.extend(defaults, options);
     if($("#tiptip_holder").length <= 0){
      var request = null;
       var tiptip_holder = $('<div id="tiptip_holder" style="max-width:' + opts.maxWidth + ';"></div>');
      var tiptip_content = $('<div id="tiptip_content"></div>');
      var tiptip_arrow = $('<div id="tiptip_arrow"></div>');
      $("body").append(tiptip_holder.html(tiptip_content).prepend(tiptip_arrow.html('<div id="tiptip_arrow_inner"></div>')));
    } else { var tiptip_holder = $("#tiptip_holder"); var tiptip_content = $("#tiptip_content"); var tiptip_arrow = $("#tiptip_arrow"); }
    return this.each(function(){
      var org_elem = $(this);
      var par_elem = $(this);
      if(opts.content){ var org_title = opts.content; } else { var org_title = org_elem.attr(opts.attribute); }
      if(org_title != ""){
        if(!opts.content){ org_elem.removeAttr(opts.attribute); }
        var timeout = false;
        org_elem = $(this).children().children();
        if (par_elem.attr('rel') == "" || par_elem.attr('rel') == undefined) { opts.keepAlive = false; }
        if(opts.activation == "hover"){
          org_elem.hover(function(){ active_tiptip(); }, function(){ if(!opts.keepAlive){ deactive_tiptip(); } });
          if(opts.keepAlive){ tiptip_holder.hover(function(){}, function(){ deactive_tiptip(); }); }
        } else if(opts.activation == "focus"){
          org_elem.focus(function(){ active_tiptip(); }).blur(function(){ deactive_tiptip(); });
        } else if(opts.activation == "click"){
          org_elem.click(function(){ active_tiptip(); return false; }).hover(function(){},function(){ if(!opts.keepAlive){ deactive_tiptip(); } });
          if(opts.keepAlive){ tiptip_holder.hover(function(){}, function(){ deactive_tiptip(); }); }
        }
        function place_tiptip(){
          tiptip_arrow.removeAttr("style");
          var top = parseInt(org_elem.offset()['top']); var left = parseInt(org_elem.offset()['left']); var org_width = parseInt(org_elem.outerWidth()); var org_height = parseInt(org_elem.outerHeight()); var tip_w = tiptip_holder.outerWidth(); var tip_h = tiptip_holder.outerHeight(); var w_compare = Math.round((org_width - tip_w) / 2); var h_compare = Math.round((org_height - tip_h) / 2); var marg_left = Math.round(left + w_compare); var marg_top = Math.round(top + org_height + opts.edgeOffset); var t_class = ""; var arrow_top = ""; var arrow_left = Math.round(tip_w - 12) / 2;
          if(opts.defaultPosition == "bottom"){ t_class = "_bottom";} else if(opts.defaultPosition == "top"){ t_class = "_top";  } else if(opts.defaultPosition == "left"){ t_class = "_left"; } else if(opts.defaultPosition == "right"){ t_class = "_right"; }
          var right_compare = (w_compare + left) < parseInt($(window).scrollLeft());
          var left_compare = (tip_w + left) > parseInt($(window).width());
          if((right_compare && w_compare < 0) || (t_class == "_right" && !left_compare) || (t_class == "_left" && left < (tip_w + opts.edgeOffset + 5))){
            t_class = "_right";
            arrow_top = Math.round(tip_h - 13) / 2;
            arrow_left = -12;
            marg_left = Math.round(left + org_width + opts.edgeOffset);
            marg_top = Math.round(top + h_compare);
          } else if((left_compare && w_compare < 0) || (t_class == "_left" && !right_compare)){
            t_class = "_left";
            arrow_top = Math.round(tip_h - 13) / 2;
            arrow_left = Math.round(tip_w);
            marg_left = Math.round(left - (tip_w + opts.edgeOffset + 5));
            marg_top = Math.round(top + h_compare);
          }
          var top_compare = (top + org_height + opts.edgeOffset + tip_h + 8) > parseInt($(window).height() + $(window).scrollTop());
          var bottom_compare = ((top + org_height) - (opts.edgeOffset + tip_h + 8)) < 0;
          if(top_compare || (t_class == "_bottom" && top_compare) || (t_class == "_top" && !bottom_compare)){
            if(t_class == "_top" || t_class == "_bottom"){ t_class = "_top"; } else { t_class = t_class + "_top"; }
            arrow_top = tip_h;
            marg_top = Math.round(top - (tip_h + 5 + opts.edgeOffset));
          } else if(bottom_compare | (t_class == "_top" && bottom_compare) || (t_class == "_bottom" && !top_compare)){
            if(t_class == "_top" || t_class == "_bottom"){ t_class = "_bottom"; } else { t_class = t_class + "_bottom"; }
            arrow_top = -12;
            marg_top = Math.round(top + org_height + opts.edgeOffset);
          }
          if(t_class == "_right_top" || t_class == "_left_top"){ marg_top = marg_top + 5; } else if(t_class == "_right_bottom" || t_class == "_left_bottom"){ marg_top = marg_top - 5; }
          if(t_class == "_left_top" || t_class == "_left_bottom"){ marg_left = marg_left + 5; }
          tiptip_arrow.css({"margin-left": arrow_left + "px", "margin-top": arrow_top + "px"});
          tiptip_holder.css({"margin-left": marg_left + "px", "margin-top": marg_top + "px"}).attr("class","tip" + t_class);
        }
        function active_tiptip(){
          if (request != null) { request.abort(); }
          opts.enter.call(this);
          if (par_elem.attr('rel') != "" && par_elem.attr('rel') != undefined) {
            tiptip_content.html("<div class='tooltip_loader'><div></div></div>");
            request = $.get(Drupal.settings.basePath + "calendar_links/" + par_elem.attr('rel'), function(data){ tiptip_content.html(" "); tiptip_content.append(data); place_tiptip(); });
          } else { tiptip_content.html(org_title); }
          tiptip_holder.hide().removeAttr("class").css("margin","0");
          place_tiptip();
          if (timeout){ clearTimeout(timeout); }
          timeout = setTimeout(function(){ tiptip_holder.stop(true,true).fadeIn(opts.fadeIn); }, opts.delay);
        }
        function deactive_tiptip(){ opts.exit.call(this); if (timeout){ clearTimeout(timeout); } tiptip_holder.fadeOut(opts.fadeOut); }
      }
    });
  }
  Drupal.behaviors.initRussianBeautyCalendar = {
    attach : function() {
      $(".tooltip").tipTip({defaultPosition: "right", maxWidth: "400px", keepAlive:true});
      $("#calendar-body .calendar-daynames .calendar-value").each(function(){
        //$(this).html($(this).html().substr(0,2) + '<span class="third-letter">' + $(this).html().substr(2,1) + '</span>');
      });
    }
  }
})(jQuery);
