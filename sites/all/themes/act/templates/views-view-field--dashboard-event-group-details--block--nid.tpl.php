<?php
$group_image = '';
$all_topics = array();
$node = node_load($output);
$loaduser = user_load($node->uid);

if(is_object($loaduser)){
$name = array();

$name_first = field_get_items('user', $loaduser, 'field_name_first');
$name[] = $name_first[0]['value'];

$name_last = field_get_items('user', $loaduser, 'field_name_last');
$name[] = $name_last[0]['value'];

$fullname = implode(' ',$name);         
  if(is_object($loaduser)){
	if(is_object($loaduser->picture)){
	  $uri = $loaduser->picture->uri;
    } else {
	  $uri = variable_get('user_picture_default'); 
	}
	$user_images = theme('image_style',
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
	}
}
$term_arr = array();
$field_topics = field_get_items('node', $node, 'field_topics');
if(!empty($field_topics) && sizeof($field_topics) > 0){
  foreach($field_topics as $topics){
	$term = taxonomy_term_load($topics['tid']);
    $term_arr[] = l($term->name, 'taxonomy/term/'.$term->tid);
  }
}

if(sizeof($term_arr) > 0){
  $all_topics = implode(', ',$term_arr);	
}

$group_logo = field_get_items('node', $node, 'field_group_logo');
$uri = $group_logo[0]['uri'];

if(!empty($uri) ) {

$group_image = theme('image_style',
		array(
			'style_name' => 'thumbnail',
			'path' => $uri,
			'attributes' => array(
			'class' => 'avatar'
				),
			'width' => NULL,
			'height' => NULL,            
			)
		);
}else {
$group_image = '';
}

 // Getting Member records
  $members = '';
  $view = views_get_view('event_group_counts');
  $view->set_display("block_1"); 
  $view->set_arguments(array($node->nid));
  $view->pre_execute();
  $view->execute();
  $view->render();   
  $members = count($view->result);
  
  // Getting Upcoming Events
  $upcoming_events = '';
  $view = views_get_view('event_group_events_counts');
  $view->set_display("block"); 
  $view->set_arguments(array($node->nid,));
  $view->execute();
  $view->render(); 
  $view->render();   
  $upcoming_events = count($view->result);
  
  // Getting Past event
  $past_events = '';
  $view = views_get_view('event_group_events_counts');
  $view->set_display("block_1"); 
  $view->set_arguments(array($node->nid,));
  $view->execute();
  $view->render(); 
  $view->render();   
  $past_events = count($view->result);
  
  
?>

<div class="event-group-details">
  <div class="image">
     <?php print $group_image;?> 
  </div>
  
  <div class="grouplocation">
      <div class="locality"> <?php print l($node->title,'node/'.$node->nid);?></div>
      <div class="founded"><?php print t('Founded'); ?> <?php print format_date($node->created, 'custom', 'M d, Y');?></div>
  </div>
  
  <div class="aboutus-link bottom-line">
     <div class="aboutus">
         <?php print l(t('About us...'),'node/'.$node->nid, array('query'=> drupal_get_destination()));?>
     </div>
  </div>
  
  <div class="group-stats bottom-line">
    <div class="label"><?php print l(t('Members'), 'members/'.$node->nid, array('query' => drupal_get_destination()));?></div>
    <div class="value"><?php print l($members, 'members/'.$node->nid, array('query' => drupal_get_destination()));?></div>
    
    <div class="label"><?php print l(t('Upcoming Events'), 'upcoming-event/'.$node->nid, array('query' => drupal_get_destination())); ?></div>
    <div class="value"><?php print l($upcoming_events, 'upcoming-event/'.$node->nid, array('query' => drupal_get_destination()));?></div>
    
    <div class="label"><?php print l(t('Past Events'), 'past-event/'.$node->nid, array('query' => drupal_get_destination()));?></div>
    <div class="value"><?php print l($past_events, 'past-event/'.$node->nid, array('query' => drupal_get_destination()));?></div>
    
    <div class="label"><?php print  l(t('Our Calendar'), 'node/'.$node->nid, array('html' => 'true', 'fragment' => 'calendar'));?></div>
    <?php
        $img = '<div class="value calendar-icon"></div>';
        print l($img, 'node/'.$node->nid, array('html' => 'true', 'fragment' => 'calendar'));
    ?>
  </div>
  
  <?php if(sizeof($term_arr) > 0){?>
  <div class="weare-about bottom-line">
     <div class="heading"><?php print t('We are about:')?></div>
			 <div class="des">
					<?php print $all_topics;?>
			 </div>
  </div>
  <?php } ?>
  
  <div class="organizers bottom-line">
     <div class="heading"><?php print t('Organizers:')?></div>
     <div class="des">
        <div class="names">
          <?php print l($fullname,'user/'.$loaduser->uid);
                //print views_embed_view('event_group_counts', $display_id = 'block_1',array($node->nid));
          ?>
        </div>
        <div class="picture">
            <?php print $user_images;?>
        </div>
     </div>
     
  </div>
  
</div>
