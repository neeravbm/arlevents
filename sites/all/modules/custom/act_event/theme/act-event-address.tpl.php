<?php
global $base_url;
$node = NULL;
$og = og_context();
if (empty($og)) {
  $node = node_load(check_plain(arg(1)));
}
else {
  $node = node_load($og['gid']);
}

if ($node) {
  $mail = '';
  $userload = user_load($node->uid);
  $mail = $userload->mail;

  $product = drupal_render(field_view_field('node', $node, 'field_product'));
  $slogan = drupal_render(field_view_field('node', $node, 'field_event_slogan'));
  //$startdate = field_get_items('node', $node, 'field_date');
  $field_date = field_get_items('node', $node, 'field_date');

  $event_timezone = new DateTimeZone(date_default_timezone());

  $event_from_date = new DateTime($field_date[0]['value'], $event_timezone);
  $event_from_offset = $event_timezone->getOffset($event_from_date);

  $start = date('l, M jS', $event_from_date->format('U') + $event_from_offset);
  //$event_start_time = date('h:i A', $event_from_date->format('U') + $event_from_offset);


  $event_from_date2 = new DateTime($field_date[0]['value2'], $event_timezone);
  $event_from_offset2 = $event_timezone->getOffset($event_from_date2);

  $end = date('l, M jS', $event_from_date2->format('U') + $event_from_offset2);
  $year = date('Y', $event_from_date2->format('U') + $event_from_offset2);
  //$event_end_time = date('h:i A', $event_from_date2->format('U') + $event_from_offset2);

  /*
  $start = $startdate[0]['value'];
  $enddate = field_get_items('node', $node, 'field_date');
  $end = $enddate[0]['value2'];
  $start    = date('l, M jS', strtotime($start));
  $end      = date('l, M jS, Y', strtotime($end));
  */

  $field_venue_address = field_get_items('node', $node, 'field_venue_address');
  $venue_address = $field_venue_address[0];
}
else {
  return;
}

?>
<div>

  <div id="event_venue_container">
    <div id="event_title"><h3><?php print render($node->title); ?></h3></div>

    <div id="event_slogan"><?php print render($node->field_event_slogan['und'][0]['value']); ?></div>

    <div id="event_date">
      <div
        class="venue-cal-icon"><?php echo '<img src="' . base_path() . path_to_theme() . '/images/calender.png' . '">'; ?></div>
      <div class="venue-cal-info"><?php print $start . (($start != $end) ? (' to ' . $end) : '') . ',
      ' . $year; ?></div>
    </div>

    <?php if (sizeof($venue_address) > 0) { ?>
      <div id="event_venue">
        <div
          class="venue-icon"><?php echo '<img src="' . base_path() . path_to_theme() . '/images/location_icon.png' . '">'; ?></div>
        <div class="venue-info">
          <div id="event_venue"><?php if (!empty($venue_address['name'])) {
              print $venue_address['name'];
            } ?></div>
          <div
            id="event_venue_address"><?php if (!empty($venue_address['street'])) {
              print $venue_address['street'] . ' ' . $venue_address['additional'] . ',';
            } ?></div>
          <div id="event_venue_address2"><?php print $venue_address['city'];
            if (!empty($venue_address['province'])) {
              print ', ' . $venue_address['province'] . ' ';
            }
            print $venue_address['postal_code']; ?></div>
          <div id="event_venue_country"><?php print strtoupper($venue_address['country']); ?></div>
        </div>
      </div>
    <?php } ?>

    <div id="event_media">
      <div class="venue-icon"><a
          href="javascript:window.print();"><?php echo '<img src="' . base_path() . path_to_theme() . '/images/print-icon.png' . '">'; ?></a>
      </div>
      <div class="venue-icon"><a
          href="mailto:<?php print $mail; ?>?subject=I wanted you to see this site&body= Visit event at <?php print $base_url . '/node/' . $node->nid ?>"><?php echo '<img src="' . base_path() . path_to_theme() . '/images/envelope-icon.png' . '">'; ?></a>
      </div>
      <div class="venue-icon ven-fb-icon">
        <a
          href="https://www.facebook.com/act.iac"><?php echo '<img src="' . base_path() . path_to_theme() . '/images/fb-icon.png' . '">'; ?></a>
      </div>
      <div class="venue-icon">
        <a
          href="https://twitter.com/ACTIAC"><?php echo '<img src="' . base_path() . path_to_theme() . '/images/twitter-icon.png' . '">'; ?></a>
      </div>
      <div class="venue-icon">
        <a
          href="http://www.linkedin.com/groups?homeNewMember=&gid=1886703&trk=&ut=3z6wjeJ6GXI5M1"><?php echo '<img src="' . base_path() . path_to_theme() . '/images/link-icon.png' . '">'; ?></a>
      </div>
    </div>

    <div id="event_register_btn">
      <?php print $product; ?>
    </div>

    <?php if (og_user_access_entity('administer group', 'node', $node, $user)) { ?>
      <div class="create-badges"><?php print l(t("Create Badges"), "create-badges/$node->nid"); ?></div>
    <?php } ?>

    <?php if (user_access('update from netforum')) { ?>
      <div class="create-badges"><?php print l(t("Update from NetForum"), "update-avectra/event/$node->nid"); ?></div>
    <?php } ?>

    <?php if ($user->uid == 0) { ?>
      <div class="anonymous-addtocart">
        <?php
        $query = drupal_get_destination();
        $query['group_redirect'] = 1;
        $login = l(t('Login'), 'user/login', array('query' => $query));
        $register = l(t('Create an Account'), 'user/register', array('query' => $query));
        print t("Please !login or !register to register for this event.", array(
          '!login' => $login,
          '!register' => $register
        ));
        ?>
      </div>
    <?php } ?>
  </div>
</div>
<?php if ($details) { ?>
  <div class="events-details"><?php print $details; ?></div>
<?php } ?>
