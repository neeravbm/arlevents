<?php
/*
 * Implementing hook_preprocess_html()
 */
function act_alpha_preprocess_html(&$variables) {
   drupal_add_js(drupal_get_path('theme', 'act') . '/js/act.js', array( 
    'scope' => 'header', 
    'weight' => '15' 
  ));
  
  if (is_numeric(arg(1))) {
	$node = node_load(arg(1)); 
	if(is_object($node)){
	  if($node->type == 'events'){
        $variables['attributes_array']['class'][] = 'events-custom';
	  }
    } 
  }
  
}
