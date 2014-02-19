<?php
  $professional_details = !empty($node->field_perfossional_details[LANGUAGE_NONE]) ? $node->field_perfossional_details[LANGUAGE_NONE] : array();
  $i = 1;
  $counter = 0;
  $counter = count($professional_details);
  $professional_image = '';
  $uri = '';
?>
<article<?php print $attributes; ?>>
  
  <?php if($content['body']): ?>
  <div class="bodysummary">
     <em><?php print render($content['body']);?></em>
  </div>
  <?php endif; ?>
  
  <?php foreach ($professional_details as $key => $values) { 
	    $pro_arr = array();
        $pro_str = '';
	    $professional = entity_load('field_collection_item', array($values['value']));
	    
	    //$professional_image = drupal_render(field_view_field('field_collection_item', $professional[$values['value']], 'field_professional_image'));
        
        $uri = $professional[$values['value']]->field_professional_image[$node->language][0]['uri'];
        $professional_image = theme('image_style',
								array(
									'style_name' => 'people',
									'path' => $uri,
									'attributes' => array(
									'class' => 'people'
										),
									'width' => NULL,
									'height' => NULL,            
									)
								);
        
        $professional_name = strip_tags(str_replace('Professional Name:','',drupal_render(field_view_field('field_collection_item', $professional[$values['value']], 'field_professional_name'))));
        
        $professional_designation = strip_tags(str_replace('Professional Designation:','',drupal_render(field_view_field('field_collection_item', $professional[$values['value']], 'field_professional_designation'))));
        
		
		$professional_biodata = drupal_render(field_view_field('field_collection_item', $professional[$values['value']], 'field_professional_bio_data'));

		
        ?>
  
  <div class="innerpage">
     <div class="left">
         <div class="image">
             <?php print $professional_image; ?>
         </div>
         <div class="name-designation">
             <?php if($professional_name) { print $professional_name; } if($professional_designation) { print ', '.$professional_designation; } ?>
         </div>
         <div class="readmore">
             <?php //print l('View bio', "node/$node->nid",array('query' => drupal_get_destination(),)); ?>
         </div>
     </div>
     <div class="right">
         <div class="biodata">
             <?php print $professional_biodata; ?>
         </div>
     </div>
  </div>
  <?php $i++;} ?>
</article>
