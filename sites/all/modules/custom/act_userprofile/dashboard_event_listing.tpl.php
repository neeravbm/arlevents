<?php
/*
 * @file
 *   Dashboard event listing template
 */

?>

<?php
$output = '';
$fielddate = '';
$field_date_time = '';
$msg = '';
if ($action == 'all') {
  $msg = t('There are no scheduled meetings in your communities.');
}
else {
  $msg = t('You are not signed up for any community meetings at this time.');
}
$output .= (sizeof($items) == 0) ? '<div class="event-listing"><div class="datetitle"><b>' . $msg . '</b></div></div>' :
  '';
foreach ($items as $key => $nodes) {
  $output .= '<div class="event-listing"><div class="datetitle"><b>' .
    format_date(strtotime($key), 'custom', 'l, M d') . '</b></div>';
  foreach ($nodes as $node) {
    $title = $node->title;

    $field_date = field_get_items('node', $node, 'field_date');

    $event_timezone = new DateTimeZone(date_default_timezone());
    $event_from_date = new DateTime($field_date[0]['value'], $event_timezone);
    $event_from_offset = $event_timezone->getOffset($event_from_date);

    $field_date_time = date('l, M d', $event_from_date->format('U') + $event_from_offset);
    $fielddate = date('h:i a', $event_from_date->format('U') + $event_from_offset);

    /*$field_date_time = format_date(strtotime($field_date[0]['value']), 'custom', 'h A');
    $fielddate = format_date(strtotime($field_date[0]['value']), 'custom', 'l, M d');*/

    $og_group_ref = field_get_items('node', $node, 'og_group_ref');
    $og_group = node_load($og_group_ref[0]['target_id']);

    $output .= '<div class="event-details">';
    if (is_object($og_group)) {
      $output .= '<div class="event-group">' . l($og_group->title, 'node/' . $og_group->nid) . '</div>';
    }
    $output .= '<div class="event-title">' . l($title, 'node/' . $node->nid) . '</div>';
    $output .= '<div class="event_datetime">';
    $output .= '<span class="event_time">' . $field_date_time . '</span>';
    $output .= '<span class="event_date">' . $fielddate . '</span>';
    //$output .= views_embed_view('commons_events_event_attendee_list', 'default', $node->nid);
    $output .= views_embed_view('commons_events_event_attendee_list', 'commons_events_full_attendee_list', $node->nid);
    $output .= '</div>';
    $output .= '</div>';
  }
  $output .= '</div>';
}
print $output;
?>
