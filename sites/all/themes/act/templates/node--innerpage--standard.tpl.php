<?php
$professional_details = $node->field_perfossional_details[$node->language];
$i = 1;
$counter = 0;
$counter = count($professional_details);
$professional_image = '';
$uri = '';
?>

<article<?php print $attributes; ?>>
  <div class="innerpage-standard">
     <?php foreach ($professional_details as $key => $values) { 
	    $pro_arr = array();
        $pro_str = '';
	    
	    $professional = entity_load('field_collection_item', array($values['value']));
	    
	   //print_r($professional);
	    
	   // $professional_image = drupal_render(field_view_field('field_collection_item', $professional[$values['value']], 'field_professional_image'));
        
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

		$pro_arr[] = $professional_name;

		$pro_arr[] = $professional_designation;

		if(sizeof($pro_arr) > 0){
		  $pro_str = implode(', ',$pro_arr);
         }
        //print_r($values);
	  ?>
     <div class="col">
        <div class="innerdata">
			 <div class="image">
				 <?php print $professional_image; ?>
			 </div>
		</div>
		<div class="summary">
			<div class="name-designation">
					 <b><?php if($professional_name) { print $professional_name;} ?></b><?php if($professional_designation) { print ', '.$professional_designation; }?>
			</div>
			<div class="readmore">
					 <?php //print l('View bio', "node/$node->nid"); ?>
			</div>
		</div>
        <div class="biodata">
             <?php print $professional_biodata; ?>
        </div>
     </div>
     
     <?php $i++; } ?>
  
  </div>
</article>
