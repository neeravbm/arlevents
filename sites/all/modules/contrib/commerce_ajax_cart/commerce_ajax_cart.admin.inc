<?php

function commerce_ajax_cart_settings_form() {
  $defaults = variable_get('commerce_ajax_cart_position',commerce_ajax_cart_get_defaults()) ;

  $form = array();
  $form['position'] = array(
    '#type' => 'fieldset',
    '#tree' => true ,
    '#title' => t('Positioning of cart preview'),
    '#description' => t('Read documentation for jquery_ui position <a target="_blank" href="http://api.jqueryui.com/position/">here</a>')
  );
  $form['position']['my'] = array(
  	'#type' => 'textfield',
    '#title' => 'my',
    '#default_value' => $defaults['my']
  );
  $form['position']['at'] = array(
    '#type' => 'textfield',
    '#title' => 'at',
    '#default_value' => $defaults['at']
  );
  $form['position']['collision'] = array(
    '#type' => 'textfield',
    '#title' => 'collision',
    '#default_value' => $defaults['collision']
  );

  $form['empty'] = array(
  	'#type' => 'textfield',
    '#title' => t('Empty cart text'),
    '#default_value' => variable_get('commerce_ajax_cart_text', 'There is no product in your cart' )
  );

  $form['actions'] = array();
  $form['actions']['submit'] = array(
  	'#type' => 'submit',
    '#value' => t('Save settings'),
  );

  return $form ;
}

function commerce_ajax_cart_settings_form_submit($form,$form_state) {
  variable_set('commerce_ajax_cart_position',$form_state['values']['position']) ;
  variable_set('commerce_ajax_cart_text',$form_state['values']['empty']) ;
}