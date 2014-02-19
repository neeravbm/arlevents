(function ($) {

Drupal.behaviors.commerce_avectra = {
  attach: function(context, settings) {
    if (!$('#payment-details').html().length) {
      $('#edit-commerce-payment').hide();
    }
  }
}

})(jQuery);
  
