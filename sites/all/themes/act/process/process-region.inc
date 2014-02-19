<?php

/**
 * Implements themename_alpha_process_region().
 */

function act_alpha_process_region(&$vars) {
  if (in_array($vars['elements']['#region'], array('content', 'menu', 'branding'))) {
   $theme = alpha_get_theme();
   global $base_url;
   
    switch ($vars['elements']['#region']) {
      case 'branding':
        //$vars['linked_logo_img'] = $vars['logo'] ? l($vars['logo_img'], 'homepage' , array('attributes' => array('rel' => 'home', 'title' => check_plain($vars['site_name'])), 'html' => TRUE)) : '';
        $vars['linked_logo_img'] = $vars['logo'] ? l($vars['logo_img'], 'homepage' , array('attributes' => array('rel' => 'home', 'title' => check_plain($vars['site_name'])), 'html' => TRUE)) : '';
      break;
    }
  }
}

