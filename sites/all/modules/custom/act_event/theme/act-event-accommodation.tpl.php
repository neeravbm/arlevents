<?php
$speaker = entity_load('field_collection_item', array($node->field_events_speaker['und'][0]['value']));
$speaker = $speaker[$node->field_events_speaker['und'][0]['value']];
$image = drupal_render(field_view_field('field_collection_item', $speaker, 'field_speaker_photo'));
?>

<div>
  <div id="event_header"><?php print t('Agenda at a Glance'); ?></div>
</div>
