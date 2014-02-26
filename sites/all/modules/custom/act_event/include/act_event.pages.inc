<?php


/*
 * Menu callback
 * Return Keynote Speaker form
 */
function act_event_speaker($node) {
  drupal_add_js(path_to_theme() . '/js/mytabs.js');
  return theme('event_get_speaker', array('node' => $node));
}


/*
 * Menu callback
 * Return Agenda form
 */
function act_event_agenda($node) {

  drupal_add_js(path_to_theme() . '/js/mytabs.js');

  $data = array();
  $tracks_nodes = array();

  if (!empty($node->field_event_session)) {

    // Extracting session nid referenced from event node
    $sessions = $node->field_event_session[LANGUAGE_NONE];

    foreach ($sessions as $session) {
      $sess_ref = entity_load('field_collection_item', array($session['value']));

      if (!empty($sess_ref[$session['value']]->field_session_reference)) {
        $session_nodes[] = $sess_ref[$session['value']]->field_session_reference[LANGUAGE_NONE][0]['target_id'];
      }


      if (!empty($sess_ref[$session['value']]->field_track_reference) && !empty($sess_ref[$session['value']]->field_session_reference)) {
        $tracks_nodes[$sess_ref[$session['value']]->field_session_reference[LANGUAGE_NONE][0]['target_id']] = $sess_ref[$session['value']]->field_track_reference[LANGUAGE_NONE][0]['target_id'];
      }
    }

    // Sorting Session nodes as per requirement
    if (!empty($session_nodes)) {
      $sess_data = node_load_multiple($session_nodes);
      foreach ($sess_data as $nid => $node) {

        $sess_start_date = $node->field_session_time[LANGUAGE_NONE][0]['value'];
        $sess_end_date = $node->field_session_time[LANGUAGE_NONE][0]['value2'];

        if (isset($data[date('Y-m-d', $sess_start_date)][$sess_start_date])) {

          if (is_object($data[date('Y-m-d', $sess_start_date)][$sess_start_date]['node'])) {
            $temp = $data[date('Y-m-d', $sess_start_date)][$sess_start_date]['node'];
            $data[date('Y-m-d', $sess_start_date)][$sess_start_date]['node'] = '';
            $data[date('Y-m-d', $sess_start_date)][$sess_start_date]['node'][] = array(
              'session' => $temp,
              'track' => $tracks_nodes[$temp->nid]
            );

          }

          $data[date('Y-m-d', $sess_start_date)][$sess_start_date]['node'][] = array(
            'session' => $node,
            'track' => $tracks_nodes[$node->nid]
          );
        }
        else {
          $data[date('Y-m-d', $sess_start_date)][$sess_start_date] = array(
            'sess_end_date' => $sess_end_date,
            'node' => $node
          );
        }

      }
    }
    // sorting date
    ksort($data);
  }
  //dsm($data);
  if ($data) {
    $data = _act_event_get_formatted_agenda_content($data);
  }

  return theme('event_get_agenda', array(
    'event_address' => act_event_get_event_address(),
    'data' => $data,
    'node' => $node,
  ));
}

/*
 * Function callback to act_event_agenda()
 */
function _act_event_get_formatted_agenda_content($data) {

  //  Generating/Formatting contents
  $tabData = '';
  $ind = 0;
  $tabContentData = '';
  $tabHeader = '';

  foreach ($data as $date => $session_items) {

    $ind += 1;
    $selected = $ind == 1 ? 'selected' : '';
    $date = '<div class="weekdayname">' . date('l', strtotime($date)) . '</div><div class="weekdate">' . date('M d', strtotime($date)) . '</div>';

    $tabData .= '<li class="  ' . $selected . ' tab-' . $ind . '" >
                <a href="javascript:void(0)">' . $date . '</a>
            </li>';

    $tcontent = '';
    $link = '';
    $speaker_content = '';
    foreach ($session_items as $session_time => $snode) {


      if (is_object($snode['node'])) {
        // Link
        if (!empty($snode['node']->field_session_subtitle)) {
          $link = ' - ' . l($snode['node']->field_session_subtitle[LANGUAGE_NONE][0]['title'], $snode['node']->field_session_subtitle[LANGUAGE_NONE][0]['url']);
        }

        $speakers = _act_event_get_agenda_speakers_formatted($snode['node'], 'speaker');
        $moderators = _act_event_get_agenda_speakers_formatted($snode['node'], 'moderator');

        $diff = _act_event_get_hours_diff(date('g:ia', $session_time), date('g:ia', $snode['sess_end_date']));
        if (trim($diff) != '') {
          $diff = ' ( ' . $diff . ' ) ';
        }

        $tcontent .= '<div class="session_item">
                          <div  class="session_item_date">' . date('g:ia', $session_time) . ' - ' . date('g:ia', $snode['sess_end_date']) . '<span>' . $diff . '</span></div>
                          <div  class="session_item_title">' . $snode['node']->title . '<span class="sess_link">' . $link . '</span></div>';

        if (!empty($speakers)) {
          $tcontent .= '<div  class="session_speakers"><div>Speakers:</div>' . $speakers . '</div>';
        }

        if (!empty($moderators)) {
          $tcontent .= '<div  class="session_moderators"><div>Moderators:</div>' . $moderators . '</div>';
        }

        $tcontent .= '</div>';

      }
      else {
        if (is_array($snode['node'])) {

          $diff = _act_event_get_hours_diff(date('g:ia', $session_time), date('g:ia', $snode['sess_end_date']));
          if (trim($diff) != '') {
            $diff = ' ( ' . $diff . ' ) ';
          }

          // we have multiple sessions here. Need to format them as Breakout Session
          $tcontent .= '<div class="session_item">
                          <div  class="session_item_date">' . date('g:ia', $session_time) . ' - ' . date('g:ia', $snode['sess_end_date']) . '<span>' . $diff . '</span></div>
                          <div  class="session_item_title">' . t('Breakout Sessions') . '</div>';

          $tcontent .= _act_event_get_formatted_breakout_session($snode['node']);
          $tcontent .= '</div>';
        }
      }
    }

    $tabHeader = '<div class="tabContentHeader">' . date('l, M d, Y', $session_time) . '</div>';

    $tabContentData .= '<div class="gHeading"><a href="#">' . $date . '</a></div>
                        <div class="' . $selected . ' gInner' . $ind . ' gInner" >
                          <div class="tabMain">
                            ' . $tabHeader . $tcontent . '
                          </div>
                       </div>';

  }

  return array('tabData' => $tabData, 'tabContentData' => $tabContentData);
}

/*
 * Function callback to _act_event_get_formatted_agenda_content()
 * Return formatted speaker/moderators content
 */
function _act_event_get_agenda_speakers_formatted($node, $type) {

  $speaker_content = '';

  $ccField = $type == 'speaker' ? 'field_session_speakers' : 'field_session_moderator';
  $ccRefField = $type == 'speaker' ? 'field_sess_speaker_prof' : 'field_moderator_ref';

  if (!empty($node->{$ccField})) {

    $speakers = $node->{$ccField}[LANGUAGE_NONE];

    foreach ($speakers as $key => $value) {
      $item = entity_load('field_collection_item', array($value['value']));

      $speakerprofnode = node_load($item[$value['value']]->{$ccRefField}[LANGUAGE_NONE][0]['target_id']);

      if (!empty($speakerprofnode->field_profile_title)) {
        $speaker_content .= '<div class="session_speaker">' .
          '<span class="sess_sp_title">' . $speakerprofnode->title . '</span>' .
          ', ' . $speakerprofnode->field_profile_title[LANGUAGE_NONE][0]['value'] .
          '</div>';
      }
    }
  }

  return $speaker_content;
}


/*
 * Function callback to _act_event_get_formatted_agenda_content()
 * Return formatted Breakout Session content
 */
function _act_event_get_formatted_breakout_session($nodes) {

  $output = '';

  foreach ($nodes as $node) {

    $tcontent = '';
    $track = '';

    $speakers = _act_event_get_agenda_speakers_formatted($node['session'], 'speaker');
    $moderators = _act_event_get_agenda_speakers_formatted($node['session'], 'moderator');

    $trackNode = node_load($node['track']);

    if (!empty($trackNode->field_tracks_subtitle)) {
      $trackSubtitle = ' - <span class="sess_track_subtitle">' . $trackNode->field_tracks_subtitle[LANGUAGE_NONE][0]['value'] . '</span>';
    }

    if (!empty($trackNode->field_tracks_url)) {
      $trackURL = ' - <span class="sess_track_url">' . l($trackNode->field_tracks_url[LANGUAGE_NONE][0]['title'], $trackNode->field_tracks_url[LANGUAGE_NONE][0]['url']) . '</span>';
    }

    $trackcontent = '<div  class="session_item_title">' . $trackNode->title . $trackSubtitle . $trackURL . '</div>';

    $tcontent .= '<div  class="session_track">' . $trackcontent . '</div>';

    if (!empty($speakers)) {
      $tcontent .= '<div  class="session_speakers"><div>Speakers:</div>' . $speakers . '</div>';
    }

    if (!empty($moderators)) {
      $tcontent .= '<div  class="session_moderators"><div>Moderators:</div>' . $moderators . '</div>';
    }

    $output .= '<div class="breakout_block">
                  <div class="breakout_room">' . $node['session']->field_session_room[LANGUAGE_NONE][0]['value'] . '</div>
                  <div class="breakout_track_info">' . $tcontent . '</div>
                </div>';

  }
  return $output;

}

function _act_event_get_hours_diff($first_date, $second_date) {

  $datetime1 = date_create($first_date);
  $datetime2 = date_create($second_date);

  $interval = date_diff($datetime2, $datetime1);

  $h = $interval->format('%h');
  $m = $interval->format('%i');

  $diff = '';

  if ($h > 0) {
    $diff .= $h . ' hr ';
  }
  if ($m > 0) {
    $diff .= $m . ' min';
  }

  return $diff;
}

/*
 * Menu callback
 * Return Press Release form
 */
function act_event_press_release($node) {
  return theme('event_get_press_release', array('node' => $node));
}

/*
 * Menu callback
 * Return Travel form
 */
function act_event_travel($node) {
  return theme('event_get_travel', array('node' => $node));
}

/*
 * Menu callback
 * Return Accommodation form
 */
function act_event_accommodation($node) {
  return theme('event_get_accommodation', array('node' => $node));
}

/*
 * Menu callback
 * Return Media Partners form
 */
function act_event_media_partners($node) {
  return theme('event_get_media_partners', array('node' => $node));
}

/*
 * Menu callback
 * Return Sponsorship Opportunities form
 */
function act_event_sponsorship_opp($node) {
  return theme('event_get_sponsorship_opp', array('node' => $node));
}

/*
 * Menu callback
 * Return Contact Us form
 */
function act_event_contact_us($node) {
  return theme('event_get_contact_us', array('node' => $node));
}

/*
 * Menu callback
 * Return Social Media form
 */
function act_event_social_media($node) {
  return theme('event_get_social_media', array('node' => $node));
}

/*
 * Menu callback
 * Return Floor Plans form
 */
function act_event_floor_plans($node) {
  return theme('event_get_floor_plans', array('node' => $node));
}

/*
 * Menu callback
 * Return Program Committee form
 */
function act_event_program_committee($node) {
  return theme('event_get_program_committee', array('node' => $node));
}

/*
 * Menu callback
 * Return Program Committee form
 */
function act_event_track_description($node) {
  return theme('event_get_track_description', array('node' => $node));
}

/*
 * Menu callback
 * Return Program Committee form
 */
function act_event_conference_sponsors($node) {
  return theme('event_get_conference_sponsors', array('node' => $node));
}

function act_event_create_badges($node) {
  // list for all registered users for the event
  $data = array();
  $data = act_event_registred_users($node);
  if (empty($data)) {
		drupal_set_message(t("No registration information found for this event."), 'error');
    return;
	}
  
  $tmp_path = realpath(file_directory_temp()) . '/'; 
  $filename = $clean = preg_replace('/[^\d\w.]+/', '_', $node->title . '.pdf');
  $res = arl_badge_createBadges($data, $tmp_path . $filename, FALSE, NULL);
  if ($res > 0) {

    if (file_exists($tmp_path . $filename)) {
      // Serve file download.
      drupal_add_http_header('Pragma', 'public');
      drupal_add_http_header('Expires', '0');
      drupal_add_http_header('Cache-Control', 'must-revalidate');
      drupal_add_http_header('Content-Type', 'application/pdf');
      drupal_add_http_header('Content-Disposition', 'attachment; filename=' . basename($tmp_path . $filename));
      drupal_add_http_header('Content-Length', filesize($tmp_path . $filename));
      readfile($tmp_path . $filename);
      unlink($tmp_path . $filename);
      drupal_exit();
    }
  }
  else {
    drupal_set_message(t('No badges were created.'), 'error');
    drupal_goto(drupal_lookup_path('alias', 'node/' . $node->nid));
  }

}

/*
 * Manage Badge menu callback
 * 
 */
function _act_event_manage_badge($form, &$form_state) {

  $event_id = check_plain(arg(1));
  $eventnode = node_load($event_id);

  $form['#tree'] = TRUE;

  //$totalUsers = _act_event_get_users_by_event($eventnode, '', TRUE);
  $totalUsers = act_event_registred_users($eventnode);
  $totalUnprintedUsers = _act_event_get_unprinted_badge_rec($eventnode, TRUE);


  $form['eventnid'] = array(
    '#type' => 'hidden',
    '#value' => $eventnode->nid,
  );
  
  $form['event'] = array(
    '#type' => 'hidden',
    '#value' => $eventnode,
  );

  $form['badge_event_info'] = array(
    '#markup' => t('Badges for ' . $eventnode->title),
    '#prefix' => '<div id="badge-eventinfo">',
    '#suffix' => '</div>',
  );


  $form['badge_container'] = array(
    '#prefix' => '<div id="badge-manage">',
    '#suffix' => '</div>',
  );

  $form['badge_container']['all_badge'] = array(
    '#type' => 'fieldset',
    '#tree' => TRUE,
    '#prefix' => '<div class="badge-section-group"><div class="badge-section">',
    '#suffix' => '</div>',
  );

  $form['badge_container']['all_badge']['title'] = array(
    '#markup' => t('All badges'),
    '#prefix' => '<div class="badge-title">',
    '#suffix' => '</div>',
  );

  $form['badge_container']['all_badge']['info'] = array(
    '#markup' => t('There are currently ' . sizeof($totalUsers) . ' registered participants attending this event.'),
    '#prefix' => '<div class="badge-body">',
  );

  $form['badge_container']['all_badge']['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Print All ' . sizeof($totalUsers) . ' Badges'),
    '#prefix' => '<div class="save">',
    '#suffix' => '</div></div>',
  );

  // Unprinted badge section
  $form['badge_container']['unprinted_badge'] = array(
    '#type' => 'fieldset',
    '#tree' => TRUE,
    '#prefix' => '<div class="badge-section">',
    '#suffix' => '</div></div>',
  );
  $form['badge_container']['unprinted_badge']['title'] = array(
    '#markup' => t('Unprinted Badges'),
    '#prefix' => '<div class="badge-title">',
    '#suffix' => '</div>',
  );

  $form['badge_container']['unprinted_badge']['info'] = array(
    '#markup' => t('There are currently ' . $totalUnprintedUsers . ' unprinted badges for registered participants attending this event.'),
    '#prefix' => '<div class="badge-body">',
  );
  $form['badge_container']['unprinted_badge']['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Print All ' . $totalUnprintedUsers . ' Badges'),
    '#prefix' => '<div class="save">',
    '#suffix' => '</div></div>',
  );


  // Group badge section
  $form['badge_container']['group_badge'] = array(
    '#type' => 'fieldset',
    '#tree' => TRUE,
    '#prefix' => '<div class="badge-section">',
    '#suffix' => '</div>',
  );

  $form['badge_container']['group_badge']['title'] = array(
    '#markup' => t('Group Badges'),
    '#prefix' => '<div class="badge-title">',
    '#suffix' => '</div>',
  );

  $form['badge_container']['group_badge']['info'] = array(
    '#markup' => t('Please select the names of the registered participants you want to print.'),
    '#prefix' => '<div class="badge-body">',
  );

  $options = array();
  //$users = _act_event_get_users_by_event($eventnode);
  $data = act_event_registred_users($eventnode);
  if (!empty($data)) {
    foreach ($data as $vuser) {
      $options[$vuser['uid']] = $vuser['first_name'] . ' ' . $vuser['last_name'];
    }
  }

  $form['badge_container']['group_badge']['group_attendee_select'] = array(
    '#type' => 'select',
    '#title' => '',
    '#multiple' => TRUE,
    '#options' => $options,
    '#default_value' => array(),
  );

  $form['badge_container']['group_badge']['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Print Badges'),
    '#prefix' => '<div class="save">',
    '#suffix' => '</div></div>',
  );


  // Single badge
  $form['badge_container']['single_badge'] = array(
    '#type' => 'fieldset',
    '#tree' => TRUE,
    '#prefix' => '<div class="badge-section">',
    '#suffix' => '</div>',
  );

  $form['badge_container']['single_badge']['title'] = array(
    '#markup' => t('Single Badges'),
    '#prefix' => '<div class="badge-title">',
    '#suffix' => '</div>',
  );

  $form['badge_container']['single_badge']['info'] = array(
    '#markup' => t('Please enter the name of the registered participants you want to print.'),
    '#prefix' => '<div class="badge-body">',
  );

  $form['badge_container']['single_badge']['single_badge_select'] = array(
    '#type' => 'textfield',
    '#autocomplete_path' => 'badge/user/' . $event_id,
    '#size' => 30,
  );

  $form['badge_container']['single_badge']['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Print Badge'),
    '#prefix' => '<div class="save">',
    '#suffix' => '</div></div>',
  );
  return $form;

}

function _act_event_manage_badge_submit($form, $form_state) {
  
  $op = $form_state['clicked_button']['#id'];
  $eventnid = $form_state['values']['eventnid'];
  $eventnode = $form_state['values']['event'];

  $users = array();

  switch ($op) {

    case 'edit-badge-container-all-badge-submit':
      _act_event_print_user_badges($eventnode, array(), TRUE);
      break;

    case 'edit-badge-container-unprinted-badge-submit':

      $data = _act_event_get_unprinted_badge_rec($eventnode);
      foreach ($data as $key => $value) {
        $users[] = $value->uid;
      }
      if (empty($users)) {
        drupal_set_message(t('No badges found to print'));
        return;
      }
      _act_event_print_user_badges($eventnode, $users);
      break;

    case 'edit-badge-container-group-badge-submit':
      $selectedUsers = $form_state['values']['badge_container']['group_badge']['group_attendee_select'];
      foreach ($selectedUsers as $key => $value) {
        $users[] = $value;
      }

      if (!empty($users) && $eventnid != '') {
        _act_event_print_user_badges($eventnode, $users);
      }
      break;

    case 'edit-badge-container-single-badge-submit':

      $selectedUsers = explode(' ', $form_state['values']['badge_container']['single_badge']['single_badge_select']);

      $acc = user_load(array_pop($selectedUsers));
      $users[] = $acc->uid;
      if (!empty($users) && $eventnid != '') {
        _act_event_print_user_badges($eventnode, $users);
      }
      break;

    default:
      break;
  }

}

/*
 * 
 */
function _act_event_badge_autocomplete_user($eventnode) {
  $matches = array();
  $username = check_plain(arg(3));
  //$users = _act_event_get_users_by_event($eventnode, $username);
  $users = act_event_registred_users($eventnode);
  foreach ($users as $vuser) {
    $matches[$vuser['first_name'] . ' ' . $vuser['last_name'] . ' ' . $vuser['uid']] = $vuser['first_name'] . ' ' . $vuser['last_name'];
  }
  // return for JS
  drupal_json_output($matches);
}


/*
 * Function to print user badges 
 */
function _act_event_print_user_badges($eventnode, $users, $all = FALSE) {
  $data = array();
  $data = act_event_registred_users($eventnode);
  $tmp_path = realpath(file_directory_temp()) . '/';
  $filename = $clean = preg_replace('/[^\d\w.]+/', '_', 'GroupBadge' . '.pdf');
  $res = 0;
  if ($all) {
    $res = arl_badge_createBadges($data, $tmp_path . $filename, FALSE, NULL);
  }
  else {
    $res = arl_badge_createBadges($data, $tmp_path . $filename, FALSE, $users);
  }

  if ($res > 0) {

    if (file_exists($tmp_path . $filename)) {
      // Serve file download.
      drupal_add_http_header('Pragma', 'public');
      drupal_add_http_header('Expires', '0');
      drupal_add_http_header('Cache-Control', 'must-revalidate');
      drupal_add_http_header('Content-Type', 'application/pdf');
      drupal_add_http_header('Content-Disposition', 'attachment; filename=' . basename($tmp_path . $filename));
      drupal_add_http_header('Content-Length', filesize($tmp_path . $filename));
      readfile($tmp_path . $filename);
      unlink($tmp_path . $filename);
      drupal_exit();
    }
  }
  else {
    drupal_set_message(t('No badges were created.'), 'error');
    drupal_goto(drupal_lookup_path('alias', 'node/' . $eventnode->nid));
  }


}

/*
 * Function to return unprinted user key or count
 */
function _act_event_get_unprinted_badge_rec($eventnode, $count = FALSE) {
  $allusers = array();

  $printedusers = db_select('arl_badge_userlist', 'ul')
    ->fields('ul', array('user_id'))
    ->condition('ul.event_id', $eventnode->nid, '=')
    ->execute()->fetchCol();
 
  //$allusers = _act_event_get_users_by_event($eventnode);
  $allusers = act_event_registred_users($eventnode);
  if (!empty($allusers) && is_array($allusers)) {
   foreach ($allusers as $key => $value) {
	   if (in_array($value['user_id'], $printedusers)) {
        unset($allusers[$key]);
      }
    }
  }

  if (is_array($allusers)) {
    return $count ? count($allusers) : $allusers;
  }
  else {
    return 0;
  }

}

/*
 * Badge Event Scan attendance  
 */
function _act_event_badge_eventscan($code = '') {

  $badge_no = '';
  $status = FALSE;

  if (trim($code) != '') {
    $badge_no = $code;
  }
  else {
    if (isset($_GET['code'])) {
      $badge_no = check_plain($_GET['code']);
    }
  }

  $query = db_select('arl_badge_userlist', 'ul')
    ->fields('ul', array('user_id', 'event_id'))
    ->condition('ul.user_id', $badge_no, '=')
    ->execute()->fetchAll();

  if (!empty($query)) {

    $rec = $query[0];

    $ukey = $rec->user_id;
    $ekey = $rec->event_id;

    $uid = getUidByKey($ukey);

    $query = db_select('node', 'node');
    $query->condition('node.nid', $ekey)
      ->condition('node.type', 'events')
      ->fields('node', array('nid'));
    $result = $query->execute()->fetchCol();

    $eventnode = count($result) > 0 ? node_load($result[0]) : array();
    $acc = user_load($uid);

    $org_key = @$acc->field_organization['und'][0]['target_id'];
    $org_node = node_load($org_key);

    $user_name = t($acc->field_name_first['und'][0]['value']) . ' ' . t($acc->field_name_last['und'][0]['value']);
    $event_name = t($eventnode->title);
    $org_name = t($org_node->title);

    if ($uid) {

      $query = db_select('arl_badge_scans', 'abs')
        ->fields('abs', array('user_id'))
        ->condition('abs.user_id', $badge_no, '=')
        ->execute()->fetchCol();

      if (empty($query)) {

        $record = array(
          "user_id" => $badge_no,
          "scan_location" => '',
          "scan_date" => REQUEST_TIME,
        );
        $status = drupal_write_record('arl_badge_scans', $record);
        $status = $status ? 'SUCCESS' : 'ERROR';
      }
      else {
        $status = 'ALREADY_EXIST';
      }
    }
  }
  else {
    $status = 'BADGE_NOT_FOUND';
  }

  switch ($status) {
    case 'ERROR':
      $msg = t('Could not mark @user as attending the event @event.', array(
        '@user' => $user_name,
        '@event' => $event_name
      ));
      $errortype = 'error';
      break;
    case 'SUCCESS':
      $msg = t('@user from @org is attending @event.', array(
        '@user' => $user_name,
        '@event' => $event_name,
        '@org' => $org_name
      ));
      $errortype = 'status';
      break;
    case 'ALREADY_EXIST':
      $msg = t('@user is already marked to be attending the event @event.', array(
        '@user' => $user_name,
        '@event' => $event_name
      ));
      $errortype = 'error';
      break;
    case 'BADGE_NOT_FOUND':
      $msg = t('Badge number @badgeno does not exist in our record.', array('@badgeno' => $badge_no));
      $errortype = 'error';
      break;
  }

  if (trim($code) != '') {
    drupal_set_message($msg, $errortype);
  }
  else {
    return $msg;
  }

}


/*
 *	Function to return user who have marked attendace for particular event 
*/
function act_get_arl_event_attendees($node) {

  $event_key = (string) $node->field_event_key['und'][0]['value'];

  if ($event_key != '') {
    return views_embed_view('arl_badge_users', 'block_1', $event_key);
  }
  return;
}

/*
 *	Function to mark badge attendance from UI
 */
function act_event_manage_badge_attendance($form, &$form_state) {

  drupal_set_title(t('Badge Attendance'));

  $form['badge']['info'] = array(
    '#markup' => t('Please enter badge number to mark attendance'),
    '#prefix' => '<div id="badge-info">',
    '#suffix' => '</div>',
  );

  $form['badge']['badgeno'] = array(
    '#type' => 'textfield',
  );

  $form['badge']['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Save'),
    '#prefix' => '<div class="save">',
    '#suffix' => '</div>',
  );

  return $form;

}

/*
 * Submit handler for Badge attendance form
 */
function act_event_manage_badge_attendance_submit($form, &$form_state) {

  $badge_no = check_plain($form_state['input']['badgeno']);
  _act_event_badge_eventscan($badge_no);

}

function act_event_get_resize_page() {

  $path = drupal_get_path('module', 'act_event');
  drupal_add_js($path . '/js/nfWidget.min.js');
  drupal_add_js($path . '/js/nfWidgetCustom.js');

  $form['section'] = array(
    '#prefix' => '<div id="nfWidget">',
    '#suffix' => '</div>',

  );
  return $form;
}

/**
 * Function to get all registered user for a specific events
 * @para node object
 *   node as obect
 * @return array $data
 *   array contains title, first_name, organization etc.
 */  
function act_event_registred_users($node){
	$data = array();
  // list for all registered users for the event
  $view = views_get_view('registered_users');
	$view->set_display("page");
	$view->set_arguments(array($node->nid));
	$view->render();
	$results = $view->result;
  $data = array();
  if (count($view->result) > 0){
    foreach ($results as $result) {
			$first_name = $result->field_field_name_first[0]['raw']['value'];
		  $last_name = $result->field_field_name_last[0]['raw']['value'];
		  $organization = $result->field_field_organization[0]['raw']['entity']->title;
		  
		  $data[] = array(
		    'title' => $node->title,
		    'first_name' => $first_name,
		    'last_name' => $last_name,
		    'organization' => $organization,
		    'user_id' => $result->users_commerce_order_uid,
		    'event_id' => $node->nid,
		  ); 
		}
  } 
	return $data;
}
