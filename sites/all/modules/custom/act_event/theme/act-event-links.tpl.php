<?php
global $base_url, $user;
$group = og_context();
$session = FALSE;
$track = FALSE;
if (empty($group)) {
  $group_node = check_plain(arg(1));
}
else {
  $group_node = $group['gid'];
}

$mod_path = drupal_get_path('module', 'act_event') . '/images/';

$event_path = $base_url . '/' . drupal_lookup_path('alias', 'node/' . $group_node);

$node = node_load($group_node);
$product = drupal_render(field_view_field('node', $node, 'field_product'));


$field_event_session = field_get_items('node', $node, 'field_event_session');
if (isset($node->field_event_session) && !empty($field_event_session)) {
  foreach ($field_event_session as $key => $value) {
    $session_track = entity_load('field_collection_item', array($value['value']));
    $session_track = $session_track[$value['value']];

    $field_session_reference = field_get_items('field_collection_item', $session_track, 'field_session_reference');
    $field_track_reference = field_get_items('field_collection_item', $session_track, 'field_track_reference');

    if (sizeof($field_session_reference) > 0) {
      $nid = $field_session_reference[0]['target_id'];
      if ($nid) {
        $session_load = node_load($nid);
        if (is_object($session_load) && isset($session_load->status) && $node->status && $session_load->status == 1) {
          $session = TRUE;
        }
      }
    }
    if (sizeof($field_track_reference) > 0) {
      $nid = $field_track_reference[0]['target_id'];
      if ($nid) {
        $track_load = node_load($nid);
        if ($track_load->status == 1 && $node->status) {
          $track = TRUE;
        }
      }
    }
  }
}
$ceu_credits = field_get_items('node', $node, 'field_ceu_credits');
$ceu_credits = $ceu_credits[0]['value'];

$field_program_committee = field_get_items('node', $node, 'field_program_committee');
$field_program_committee = $field_program_committee[0]['value'];

$field_supporting_organizations = field_get_items('node', $node, 'field_supporting_organizations');
$field_supporting_organizations = $field_supporting_organizations[0]['value'];

$field_floor_plans = field_get_items('node', $node, 'field_floor_plans');
$field_floor_plans = $field_floor_plans[0]['value'];

$field_sponsorship_opportunities = field_get_items('node', $node, 'field_sponsorship_opportunities');
$field_sponsorship_opportunities = $field_sponsorship_opportunities[0]['value'];

$field_accommodation = field_get_items('node', $node, 'field_accommodation');
$field_accommodation = $field_accommodation[0]['value'];

$field_travel = field_get_items('node', $node, 'field_travel');
$field_travel = $field_travel[0]['value'];

$field_press_releases = field_get_items('node', $node, 'field_press_releases');
$field_press_releases = $field_press_releases[0]['value'];

$field_contact_us = field_get_items('node', $node, 'field_contact_us');
$field_contact_us = $field_contact_us[0]['value'];

$field_keynote_speakers = field_get_items('node', $node, 'field_keynote_speakers');
$field_keynote_speakers = $field_keynote_speakers[0]['value'];

$field_contact_us = field_get_items('node', $node, 'field_contact_us');
$field_contact_us = $field_contact_us[0]['value'];

$field_media_partners = field_get_items('node', $node, 'field_media_partners');
$field_media_partners = $field_media_partners[0]['value'];

$field_social_media = field_get_items('node', $node, 'field_social_media');
$field_social_media = $field_social_media[0]['value'];
//dsm($node);

/*********** Labels ********/

$field_label_keynotes = field_get_items('node', $node, 'field_label_keynotes');
$field_label_keynotes = $field_label_keynotes[0]['value'];
$field_label_keynotes = (!empty($field_label_keynotes)) ? $field_label_keynotes : t('Keynotes');

$field_label_travel = field_get_items('node', $node, 'field_label_travel');
$field_label_travel = $field_label_travel[0]['value'];
$field_label_travel = (!empty($field_label_travel)) ? $field_label_travel : t('Travel');

$field_label_accommodation = field_get_items('node', $node, 'field_label_accommodation');
$field_label_accommodation = $field_label_accommodation[0]['value'];
$field_label_accommodation = (!empty($field_label_accommodation)) ? $field_label_accommodation : t('Accommodation');

$field_label_agenda_glance = field_get_items('node', $node, 'field_label_agenda_glance');
$field_label_agenda_glance = $field_label_agenda_glance[0]['value'];
$field_label_agenda_glance = (!empty($field_label_agenda_glance)) ? $field_label_agenda_glance : t('Agenda at a Glance');

$field_label_meet_speakers = field_get_items('node', $node, 'field_label_meet_speakers');
$field_label_meet_speakers = $field_label_meet_speakers[0]['value'];
$field_label_meet_speakers = (!empty($field_label_meet_speakers)) ? $field_label_meet_speakers : t('Meet the Speakers');

$field_label_program_committee = field_get_items('node', $node, 'field_label_program_committee');
$field_label_program_committee = $field_label_program_committee[0]['value'];
$field_label_program_committee = (!empty($field_label_program_committee)) ? $field_label_program_committee : t('Program Committee');

$field_label_ceu_credits = field_get_items('node', $node, 'field_label_ceu_credits');
$field_label_ceu_credits = $field_label_ceu_credits[0]['value'];
$field_label_ceu_credits = (!empty($field_label_ceu_credits)) ? $field_label_ceu_credits : t('CEU Credits');

$field_label_conference_sponsors = field_get_items('node', $node, 'field_label_conference_sponsors');
$field_label_conference_sponsors = $field_label_conference_sponsors[0]['value'];
$field_label_conference_sponsors = (!empty($field_label_conference_sponsors)) ? $field_label_conference_sponsors : t('Conference Sponsors');

$field_label_supporting_organizat = field_get_items('node', $node, 'field_label_supporting_organizat');
$field_label_supporting_organizat = $field_label_supporting_organizat[0]['value'];
$field_label_supporting_organizat = (!empty($field_label_supporting_organizat)) ? $field_label_supporting_organizat : t('Supporting Organizations');

$field_label_sponsorship_opportun = field_get_items('node', $node, 'field_label_sponsorship_opportun');
$field_label_sponsorship_opportun = $field_label_sponsorship_opportun[0]['value'];
$field_label_sponsorship_opportun = (!empty($field_label_sponsorship_opportun)) ? $field_label_sponsorship_opportun : t('Sponsorship Opportunities');

$field_label_floor_plans = field_get_items('node', $node, 'field_label_floor_plans');
$field_label_floor_plans = $field_label_floor_plans[0]['value'];
$field_label_floor_plans = (!empty($field_label_floor_plans)) ? $field_label_floor_plans : t('Floor Plans');

$field_label_press_release = field_get_items('node', $node, 'field_label_press_release');
$field_label_press_release = $field_label_press_release[0]['value'];
$field_label_press_release = (!empty($field_label_press_release)) ? $field_label_press_release : t('Press release');

$field_label_media_partners = field_get_items('node', $node, 'field_label_media_partners');
$field_label_media_partners = $field_label_media_partners[0]['value'];
$field_label_media_partners = (!empty($field_label_media_partners)) ? $field_label_media_partners : t('Media Partners');

$field_label_social_media = field_get_items('node', $node, 'field_label_social_media');
$field_label_social_media = $field_label_social_media[0]['value'];
$field_label_social_media = (!empty($field_label_social_media)) ? $field_label_social_media : t('Social Media');

$field_label_session_listing = field_get_items('node', $node, 'field_label_session_listing');
$field_label_session_listing = $field_label_session_listing[0]['value'];
$field_label_session_listing = (!empty($field_label_session_listing)) ? $field_label_session_listing : t('Session Listing');

$field_label_track_listing = field_get_items('node', $node, 'field_label_track_listing');
$field_label_track_listing = $field_label_track_listing[0]['value'];
$field_label_track_listing = (!empty($field_label_track_listing)) ? $field_label_track_listing : t('Track Listing');


$group_access = field_get_items('node', $node, 'group_access');
$group_access = $group_access[0]['value'];

//og_is_member('node', $node->nid, 'user', $user, array(OG_STATE_ACTIVE))

if (event_group_access($node)) {

  ?>

  <div id='event-links-container'>


    <div class="event-links-item">

      <div
        class="el-icon home"><?php echo '<img src="' . base_path() . path_to_theme() . '/images/homeicon.png' . '">'; ?></div>

      <div class="el-link"><?php echo l(t('Home'), $event_path); ?></div>

    </div>

    <!--<div class="event-links-item">
    
    <div class="el-icon"><?php echo '<img src="' . base_path() . path_to_theme() . '/images/pencil.png' . '">'; ?></div>
    
    <div class="el-link"><a href="#"><?php print t('Register Now');?></a></div>
    
  </div>-->

    <?php if (!empty($field_keynote_speakers)) { ?>
      <div class="event-links-item">

        <div
          class="el-icon"><?php echo '<img src="' . base_path() . path_to_theme() . '/images/microphone.png' . '">'; ?></div>

        <div class="el-link"><?php echo l($field_label_keynotes, 'keynote-speaker/' . $group_node) ?></div>

      </div>
    <?php } ?>

    <?php if ($session) { ?>
      <div class="event-links-item">

        <div
          class="el-icon"><?php print '<img src="' . base_path() . path_to_theme() . '/images/list.png' . '">'; ?></div>

        <div class="el-link"><?php print l($field_label_session_listing, "session-listing/$group_node"); ?></div>

      </div>
    <?php } ?>

    <?php if (!empty($field_travel) || !empty($field_accommodation)) { ?>
      <div class="event-links-item">

        <div
          class="el-icon"><?php echo '<img src="' . base_path() . path_to_theme() . '/images/airplane.png' . '">'; ?></div>

        <div class="el-link"><?php print t('Travel & Accommodation'); ?></div>

        <?php if (!empty($field_travel)) { ?>
          <div class="el-sub-link"><?php print l($field_label_travel, 'travel/' . $group_node) ?></div>
        <?php } ?>

        <?php if (!empty($field_accommodation)) { ?>
          <div class="el-sub-link"><?php print l($field_label_accommodation, 'accommodation/' . $group_node) ?></div>
        <?php } ?>

        <!--<div class="el-sub-link"><a href="#">Justification Letter</a></div>-->

      </div>
    <?php } ?>

    <?php if ($agenda_count || $meetspeaker_count || !empty($field_keynote_speakers) || $session || $track || !empty($ceu_credits)) { ?>
      <div class="event-links-item">

        <div
          class="el-icon"><?php echo '<img src="' . base_path() . path_to_theme() . '/images/calender.png' . '">'; ?></div>

        <div class="el-link"><?php print t('Agenda & Sessions'); ?></div>

        <?php if ($agenda_count > 0) { ?>
          <div class="el-sub-link"><?php echo l($field_label_agenda_glance, 'agenda/' . $group_node) ?></div>
        <?php } ?>

        <?php if ($meetspeaker_count > 0) { ?>
          <div class="el-sub-link"><?php echo l($field_label_meet_speakers, 'meet-the-speakers/' . $group_node) ?></div>
        <?php } ?>

        <?php if (!empty($field_keynote_speakers)) { ?>
          <div class="el-sub-link"><?php echo l($field_label_keynotes, 'keynote-speaker/' . $group_node) ?></div>
        <?php } ?>

        <?php if ($session) { ?>
          <div
            class="el-sub-link"><?php print l($field_label_session_listing, "session-listing/$group_node"); ?></a></div>
        <?php } ?>

        <?php if ($track) { ?>
          <div class="el-sub-link"><?php echo l($field_label_track_listing, 'track-listing/' . $group_node) ?></div>
        <?php } ?>

        <?php if (!empty($field_program_committee)) { ?>
          <div
            class="el-sub-link"><?php echo l($field_label_program_committee, 'program-committee/' . $group_node) ?></div>
        <?php } ?>

        <?php if (!empty($ceu_credits)) { ?>
          <div class="el-sub-link"><?php echo l($field_label_ceu_credits, 'event-ceu-credit/' . $group_node) ?></div>
        <?php } ?>

      </div>

    <?php } ?>


    <?php
    //  Custom links 

    if (!empty($customLinks)) {

      $data = '';

      foreach ($customLinks as $key => $value) {

        $data .= '<div class="event-links-item">';
        $data .= '<div class="el-icon"><img src="' . base_path() . path_to_theme() . '/images/group.png' . '"></div>';

        $data .= '<div class="el-link">' . $value['label'] . '</div>';

        foreach ($value['bxlinks'] as $key2 => $value2) {

          $data .= '<div class="el-sub-link">' . l($value2['label'], $value2['link']) . '</div>';

        }

        $data .= '</div>';
      }


      print $data;
    }

    ?>

    <?php if ($conference_sponsors_count || !empty($field_supporting_organizations) || !empty($field_sponsorship_opportunities) || !empty($field_floor_plans)) { ?>
      <div class="event-links-item">

        <div
          class="el-icon"><?php echo '<img src="' . base_path() . path_to_theme() . '/images/group.png' . '">'; ?></div>

        <div class="el-link"><?php print t('Conference Supporters'); ?></div>

        <?php if ($conference_sponsors_count > 0) { ?>
          <div
            class="el-sub-link"><?php echo l($field_label_conference_sponsors, 'conference-sponsors/' . $group_node) ?></div>
        <?php } ?>

        <?php if (!empty($field_supporting_organizations)) { ?>
          <div
            class="el-sub-link"><?php echo l($field_label_supporting_organizat, 'event-supporting-organization/' . $group_node) ?></div>
        <?php } ?>

        <?php if (!empty($field_sponsorship_opportunities)) { ?>
          <div
            class="el-sub-link"><?php echo l($field_label_sponsorship_opportun, 'sponsorship-opportunities/' . $group_node) ?></div>
        <?php } ?>

        <?php if (!empty($field_floor_plans)) { ?>
          <div class="el-sub-link"><?php echo l($field_label_floor_plans, 'floor-plans/' . $group_node) ?></div>
        <?php } ?>

      </div>
    <?php } ?>

    <?php if (!empty($field_press_releases) || !empty($field_media_partners) || !empty($field_social_media)) { ?>

      <div class="event-links-item">

        <div
          class="el-icon"><?php echo '<img src="' . base_path() . path_to_theme() . '/images/conversation.png' . '">'; ?></div>

        <div class="el-link"><?php print t('For Press'); ?></div>

        <?php if (!empty($field_press_releases)) { ?>
          <div class="el-sub-link"><?php echo l($field_label_press_release, 'press-release/' . $group_node) ?></div>
        <?php } ?>

        <?php if (!empty($field_media_partners)) { ?>
          <div class="el-sub-link"><?php echo l($field_label_media_partners, 'media-partners/' . $group_node) ?></div>
        <?php } ?>


        <?php if (!empty($field_social_media)) { ?>
          <div class="el-sub-link"><?php echo l($field_label_social_media, 'social-media/' . $group_node) ?></div>
        <?php } ?>

      </div>

    <?php } ?>


    <?php if ($field_contact_us) { ?>
      <div class="event-links-item">

        <div
          class="el-icon"><?php echo '<img src="' . base_path() . path_to_theme() . '/images/iPhone.png' . '">'; ?></div>

        <div class="el-link"><?php echo l(t('Contact Us'), 'contact-us/' . $group_node) ?></div>

      </div>
    <?php } ?>

  </div>

<?php } ?>
