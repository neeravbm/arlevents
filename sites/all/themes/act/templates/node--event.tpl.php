<?php
$address = array();
$addresses = '';

$body = field_get_items('node', $node, 'body');
$body = $body[0]['value'];

$field_date = field_get_items('node', $node, 'field_date');

//$field_date123 = field_get_items('node', $node, 'field_my_date');

$timestamp = strtotime($field_date[0]['value']);
$timestamp2 = strtotime($field_date[0]['value2']);

//$event_start_date = format_date($timestamp, 'custom', 'l, M d, Y');
//$event_start_time = format_date($timestamp, 'custom', 'H:i A');


$event_timezone = new DateTimeZone(date_default_timezone());

$event_from_date = new DateTime($field_date[0]['value'], $event_timezone);
$event_from_offset = $event_timezone->getOffset($event_from_date);

$event_start_date = date('l, M d, Y', $event_from_date->format('U') + $event_from_offset);
$event_start_time = date('h:i A', $event_from_date->format('U') + $event_from_offset);



$event_from_date2 = new DateTime($field_date[0]['value2'], $event_timezone);
$event_from_offset2 = $event_timezone->getOffset($event_from_date2);

$event_end_date = date('l, M d, Y', $event_from_date2->format('U') + $event_from_offset2);
$event_end_time = date('h:i A', $event_from_date2->format('U') + $event_from_offset2);

//$event_end_date = format_date($timestamp2, 'custom', 'l, M d, Y');
//$event_end_time = format_date($timestamp2, 'custom', 'H:i A');

$field_address = field_get_items('node', $node, 'field_address');
$address[] = $field_address[0]['thoroughfare'];
$address[] = $field_address[0]['premise'];
$address[] = $field_address[0]['postal_code'];
$address[] = $field_address[0]['dependent_locality'];
$address[] = $field_address[0]['locality'];
$address[] = $field_address[0]['sub_administrative_area'];
$address[] = $field_address[0]['administrative_area'];
$address[] = $field_address[0]['country'];
$address = array_filter($address);
if(sizeof($address) > 0){
 $addresses = implode(', ',$address);
}
?>
<article<?php print $attributes; ?>>
  
  <?php print render($title_prefix); ?>
  <?php if ($title): ?>
  <header>
    <h2<?php print $title_attributes; ?>><?php print $title ?></h2>
  </header>
  <?php endif; ?>
  <?php print render($title_suffix); ?>
  
  <div class="event-date bottom-line">
        <div class="date-time-calender">
            <div class="date headline"><?php //print render($content['field_date']);?></div>
        </div> 
        
        <div class="date-icon event-bg"></div>
        <div class="date-time">
            <div class="date headline"><?php print $event_start_date;?></div>
            <div class="time subtext"><?php print $event_start_time;?></div>
        </div> 
        
        <div class="dateto"><?php print t('TO'); ?></div>
        
        
        <div class="date-icon event-bg"></div>
        
        <div class="date-time">
            <div class="date headline"><?php print $event_end_date;?></div>
            <div class="time subtext"><?php print $event_end_time;?></div>
        </div>
        
  </div>
  
  <div class="event-location bottom-line"> 
         <div class="location-icon event-bg"></div>
         <div class="locations subtext">
             <?php print $addresses; ?>
         </div>
         
  </div>
  
  <div class="event-description bottom-line"><?php print $body; ?>
    <div class="attending-event"><?php print render($content['attending']);?></div> 
  </div>
  
  
  <div<?php print $content_attributes; ?>>
    <?php
      // We hide the comments and links now so that we can render them later.
      hide($content['comments']);
      hide($content['links']);
      //print render($content);
      //print_r($content);
    ?>
  </div>
  
  <div class="clearfix">
    <?php if (!empty($content['links'])): ?>
      <nav class="links node-links clearfix"><?php print render($content['links']); ?></nav>
    <?php endif; ?>

    <?php //print render($content['comments']); ?>
  </div>
</article>



