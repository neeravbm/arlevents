<?php

/*
 * Implementing hook_preprocess_page()
 */
function act_alpha_preprocess_page(&$vars) {
  global $user;

  $uid = (is_numeric(arg(1)) && arg(0) == 'user') ? arg(1) : 0;

  if ($user->uid !== $uid) {
    unset($vars['page']['content']['content']['sidebar_first']['views_interests-block']);
    unset($vars['page']['content']['content']['sidebar_first']['views_my_interests-block']);
  }


  if (isset($vars['node']->field_logo) && !empty($vars['node']->field_logo['und'][0]['filename'])) {
    $vars['titleimage'] =
      '<img src="' . file_create_url($vars['node']->field_logo['und'][0]['uri']) . '" width=35  height=35 />';
  }

  if (isset($vars['node']->field_group_logo) && !empty($vars['node']->field_group_logo['und'][0]['filename'])) {
    $vars['titleimage'] =
      '<img src="' . file_create_url($vars['node']->field_group_logo['und'][0]['uri']) . '" width=35  height=35 />';
  }

  if (isset($vars['titleimage'])) {
    $vars['title'] = $vars['titleimage'] . ' ' . $vars['node']->title;
  }

  if (arg(0) == 'user' && is_numeric(arg(1))) {

    $title = '';
    $uid = arg(1);
    $loaduser = user_load($uid);
    $name = array();
    if (is_object($loaduser)) {

      $name_first = field_get_items('user', $loaduser, 'field_name_first');
      $name[] = $name_first[0]['value'];

      $name_last = field_get_items('user', $loaduser, 'field_name_last');
      $name[] = $name_last[0]['value'];

      if (is_object($loaduser->picture)) {
        $uri = $loaduser->picture->uri;
      }
      else {
        $uri = variable_get('user_picture_default');
      }
      $vars['user_avatar'] = theme('image_style',
        array(
          'style_name' => '50x50_avatar',
          'path' => $uri,
          'attributes' => array(
            'class' => 'avatar'
          ),
          'width' => NULL,
          'height' => NULL,
        )
      );
      if (sizeof($name) > 0) {
        $title = implode(' ', $name);
      }
      if (arg(2) == 'public-profile') {
        $vars['title'] = $title;
      }
      else {
        $vars['title'] = $vars['user_avatar'] . ' ' . $title;
      }

    }
  }

  if (isset($vars['node']->type) && $vars['node']->type == 'page') {
    require_once 'preprocess-node.inc';
    $vars['title'] = FALSE;
    $vars['breadcrumb'] = FALSE;

    $vars['data'] = _act_about_us_tabs_content($vars['node']);

    $vars['memberlist'] = views_embed_view('event_member_list', 'page_2');

  }

  if (isset($vars['node']->type) && $vars['node']->type == 'events') {

    $vars['breadcrumb'] = FALSE;
    $vars['title'] = FALSE;
  }

  if (arg(0) == 'non-archives' || arg(0) == 'archives') {
    $vars['breadcrumb'] = FALSE;
  }

}

