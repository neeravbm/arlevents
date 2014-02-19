<?php
/*********************************************************/
global $user, $base_url;
$mail = empty($user->uid) ? '' : $user->mail;
?>

<div>
  <div id="event_header"><?php print t('Agenda at a Glance'); ?></div>

  <div id="event_address"><?php print $event_address; ?></div>

  <div id="event_agenda">

    <div class="container gatewayFeature agenda-sessions tabbedContent">

      <ul class="gTabs doubleLineTabs">
        <?php if (!empty($data['tabData'])) {
          echo $data['tabData'];
        } ?>
        <li class="media-tab">
          <?php echo '<a href="javascript:window.print();" ><img src="' . base_path() . path_to_theme() . '/images/print-icon.png' . '"></a>'; ?>
          <?php echo '<a href="mailto:' . $mail . '?subject=I wanted you to see this site&body=Visit event at ' . $base_url . '/agenda/' . $node->nid . '">'; ?>
          <?php echo '<img src="' . base_path() . path_to_theme() . '/images/envelope-icon.png' . '"></a>'; ?>
        </li>
      </ul>

      <div class="gContent">

        <?php if (!empty($data['tabContentData'])) {
          echo $data['tabContentData'];
        } ?>

      </div>

    </div>


  </div>

</div>
