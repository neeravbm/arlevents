<?php

/**
 * @file
 * This template is used to print a single field in a view.
 *
 * It is not actually used in default Views, as this is registered as a theme
 * function which has better performance. For single overrides, the template is
 * perfectly okay.
 *
 * Variables available:
 * - $view: The view object
 * - $field: The field handler object that can process the input
 * - $row: The raw SQL result that can be used
 * - $output: The processed output that will normally be used.
 *
 * When fetching output from the $row, this construct should be used:
 * $data = $row->{$field->field_alias}
 *
 * The above will guarantee that you'll always get the correct data,
 * regardless of any changes in the aliasing that might happen if
 * the view is modified.
 */
?>
<?php 

$session_node = node_load($output);

if(!empty($session_node->field_session_speakers)) {
  $speakers  = $session_node->field_session_speakers[LANGUAGE_NONE];
  global $base_url;
  
  foreach ($speakers as $speaker) {
      $sessenity[]  =  $speaker['value'];
  }
  $sessentites = entity_load('field_collection_item', $sessenity);
 
  $sessenity = array();
  foreach ($sessentites as $sentity) {
      $sessenity[]  =  $sentity->field_sess_speaker_prof[LANGUAGE_NONE][0]['target_id'];
  }
  $snodes = node_load_multiple($sessenity);
  
  $output = '<div class="sess_title">'.$session_node->title.'</div>';
  foreach ($snodes as $snode) {
   
     if(is_object($snode)) {
     $uri = '';
     $field_profile_picture = field_get_items('node', $snode, 'field_profile_picture');
     $uri = $field_profile_picture[0]['uri'];
     $image = empty($uri) ? '' : "<img src='" . image_style_url('100x100', $uri) ."' />";

      $link   = l(t('View Bio'), $base_url .'/'. drupal_lookup_path('alias', 'node/'.$snode->nid). '?width=600&height=500',array('attributes'=>array('class'=>'colorbox-node')));  


      $output .= '<div class="sp_block">
                    <div class="sp_image">'.$image.' </div>
                      <div class="sp_info">
                          <div class="sp_title">'.$snode->title.'</div>';

      if(!empty($snode->field_profile_designation))
      $output .=  '<div class="sp_desg">'.$snode->field_profile_designation[LANGUAGE_NONE][0]['value'].'</div>';

      if(!empty($snode->field_profile_organization))
      $output .=  '<div class="sp_desg">'.$snode->field_profile_organization[LANGUAGE_NONE][0]['value'].'</div>';

       $output .=  '</div>
                      <div class="sp_viewlink">'.$link.'</div>
                  </div>';
    }
  }
  print $output; 
}


?>
