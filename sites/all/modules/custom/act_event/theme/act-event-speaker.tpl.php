<div id="event_header"><?php print t('Keynote Speaker'); ?></div>
<div Class="gTabsMain">
  <?php

  $mail = '';
  $userload = user_load($node->uid);
  $mail = $userload->mail;

  if (isset($node->field_keynote_speakers) && sizeof($node->field_keynote_speakers['und']) > 0) {
    foreach ($node->field_keynote_speakers['und'] as $key => $value) {
      $speaker = entity_load('field_collection_item', array($value['value']));
      $speaker = $speaker[$value['value']];
      if (!empty($speaker->field_key_speaker_profile)) {
        $speaker_profile = $speaker->field_key_speaker_profile['und'][0]['target_id'];
        $speaker_profile = node_load($speaker_profile);
        $profile_nid = $speaker_profile->nid;
        //$image  = drupal_render(field_view_field('node', $speaker_profile, 'field_profile_picture'));
        $image = drupal_render(field_view_field('node', $speaker_profile, 'field_profile_picture', array('settings' => array('image_style' => '158x158_keynote'))));
        $name = $speaker_profile->title;
        $org = drupal_render(field_view_field('node', $speaker_profile, 'field_profile_organization'));
        $desg = drupal_render(field_view_field('node', $speaker_profile, 'field_profile_designation'));
        $body = field_get_items('node', $speaker_profile, 'body');
        $bio = $body[0]['value'];
      }

      $agenda = drupal_render(field_view_field('field_collection_item', $speaker, 'field_key_speaker_agenda'));

      $date = field_get_items('field_collection_item', $speaker, 'field_keynote_speaker_date');
      $date = $date[0]['value'];

      $room = field_get_items('field_collection_item', $speaker, 'field_keynote_speaker_room');
      $room = $room[0]['value'];

      ?>

      <div id="speaker_photo">
        <div class="sp_content"> <?php print @$image; ?> </div>
        <div class="sp_content" id="speaker_photo_info">
          <div class="sp_title"><h2><?php print @$name; ?> </h2></div>
          <div class="sp_dep"> <?php print @$desg; ?> </div>
          <div id="speaker_session">
            <div id="speaker_sess_icon">
              <?php echo '<img src="' . base_path() . path_to_theme() . '/images/microphone.png' . '">'; ?>
            </div>
            <div id="speaker_session_info">
              <?php if ($date) { ?>
                <div class="sess_content"> <?php print date('g:i A', strtotime($date)); ?> </div>
                <div class="sess_content"> <?php print date('l, M jS Y', strtotime($date)); ?> </div>
              <?php }
              if ($room) { ?>
                <div class="sess_content"> <?php print $room; ?> </div>
              <?php } ?>
            </div>
          </div>
          <div id="event_media">
            <div class="venue-icon">
              <a href="https://www.facebook.com/act.iac">
                <?php echo '<img src="' . base_path() . path_to_theme() . '/images/fb-icon.png' . '">'; ?>
              </a>
            </div>
            <div class="venue-icon">
              <a href="https://twitter.com/ACTIAC">
                <?php echo '<img src="' . base_path() . path_to_theme() . '/images/twitter-icon.png' . '">'; ?>
              </a>
            </div>
            <div class="venue-icon">
              <a href="http://www.linkedin.com/groups?homeNewMember=&gid=1886703&trk=&ut=3z6wjeJ6GXI5M1">
                <?php echo '<img src="' . base_path() . path_to_theme() . '/images/link-icon.png' . '">'; ?>
              </a>
            </div>
          </div>
        </div>
      </div>



      <div id='speaker_info'>
        <?php if (!empty($bio)): ?>
          <div class="container gatewayFeature keynote-speaker tabbedContent">
            <ul class="gTabs doubleLineTabs" id="customgTabs">
              <li class="tab-1 selected" id="boi<?php print $profile_nid; ?>">
                <a href="#"><?php echo t($name . "'s Bio"); ?></a>
              </li>
              <?php if (!empty($agenda)): ?>
                <li class="tab-2" id="agenda<?php print $profile_nid; ?>">
                  <a href="#"><?php echo t('Agenda'); ?></a>
                </li>
              <?php endif; ?>
              <li class="media-tab">
                <div class="eventprint"><a
                    href="javascript:window.print();"><?php echo '<img src="' . base_path() . path_to_theme() . '/images/print-icon.png' . '">'; ?></a>
                </div>
                <div class="eventmailto"><a
                    href="mailto:<?php print $mail; ?>?subject=I wanted you to see this site&body=The body of the email"><?php echo '<img src="' . base_path() . path_to_theme() . '/images/envelope-icon.png' . '">'; ?></a>
                </div>
              </li>
            </ul>

            <div class="gContent">
              <div class="gHeading"><a href="#"><?php echo t('Bio'); ?></a></div>

              <div class="gInner1 gInner selected" id="contentboi<?php print $profile_nid; ?>">
                <?php if (!empty($bio)): ?>
                  <div class="tabMain">
                    <?php echo $bio; ?>
                  </div>
                <?php endif; ?>
              </div>

              <?php if (!empty($agenda)): ?>
                <div class="gHeading"><a href="#"><?php echo t('Agenda'); ?></a></div>
                <div class="gInner2 gInner" id="contentagenda<?php print $profile_nid; ?>">
                  <?php if (!empty($agenda)): ?>
                    <div class="tabMain">
                      <?php echo $agenda; ?>
                    </div>
                  <?php endif; ?>
                </div>
              <?php endif; ?>

            </div>
            <!-- gContent end -->
          </div> <!-- tabbedContent end -->
        <?php endif; ?>
      </div>

    <?php }
  } ?>
</div>
