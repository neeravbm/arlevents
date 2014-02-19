<?php
/**
 * @file
 * This file is empty by default because the base theme chain (Alpha & Omega) provides
 * all the basic functionality. However, in case you wish to customize the output that Drupal
 * generates through Alpha & Omega this file is a good place to do so.
 *
 * Alpha comes with a neat solution for keeping this file as clean as possible while the code
 * for your subtheme grows. Please read the README.txt in the /preprocess and /process subfolders
 * for more information on this topic.
 */

function act_alpha_preprocess_user_profile(&$variables) {
  $uid = (arg(0) == 'user' && is_numeric(arg(1))) ? arg(1) : '';
  $account = user_load($uid);

  // Helpful $user_profile variable for templates.
  foreach (element_children($variables['elements']) as $key) {
    $variables['user_profile'][$key] = $variables['elements'][$key];
  }

  $variables['user_profile']['user_organization_info'] = views_embed_view('user_organization_info', 'block', $uid);

  $variables['user_profile']['user_organization_info'] .= '<div id="roster_info">Organization Roster';
  $variables['user_profile']['user_organization_info'] .= views_embed_view('user_organization_info', 'block_1', $uid);
  $variables['user_profile']['user_organization_info'] .= '</div>';

  $privacy_form = drupal_get_form('act_userprofile_get_privacy_form');
  $variables['user_profile']['userprivacyform'] = drupal_render($privacy_form);

  $password_change_form = drupal_get_form('act_userprofile_get_avectra_change_password_form');
  $variables['user_profile']['avectra_pass_change'] = drupal_render($password_change_form);

  $variables['user_profile']['user_membership'] = act_userprofile_get_my_transaction();

  module_load_include('inc', 'act_invoice', 'includes/act_invoice.class');

  if (function_exists('act_invoice_get_membership_renewal')) {
    $mambership_renewal = act_invoice_get_membership_renewal();
  }

  $variables['user_profile']['user_membership_renewal'] = drupal_render($mambership_renewal);

  // Preprocess fields.
  field_attach_preprocess('user', $account, $variables['elements'], $variables);
}

/**
 * Implements hook_css_alter().
 */
function act_css_alter(&$css) {
  foreach ($css as $key => $value) {
    if (preg_match('/^ie::(\S*)/', $key)) {
      unset($css[$key]);
    }
    else {
      $css[$key]['browsers']['IE'] = TRUE;
    }
  }
}

