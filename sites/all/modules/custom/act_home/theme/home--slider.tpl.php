<?php
//drupal_add_js(drupal_get_path('module', 'act_home') . '/js/arlslider.js');
//drupal_add_js(drupal_get_path('module', 'act_home') . '/js/USAFRICOM.js');
$path = drupal_get_path('module','act_home').'/images';
$i = $j = 1;

?>
<div id="Billboards" data-billboards="<?php print $count;?>">              
  <?php foreach($results as $result): $node = node_load($result->nid)?>
		<div data-billboard="<?php print $i;?>"  class="billboards-items">
				<div class="billboardPhoto shadow" style="cursor: pointer;">
						<?php $slider = drupal_render(field_view_field('node', $node, 'field_slider_image',array('label' => 'hidden', 'settings' => array('image_style' => 'home-slider')))); ?>
				    <?php print l($slider, 'node/'.$node->nid, array('html' => TRUE));?>
				</div>    
				<div class="billboardCaption">
						<h1><?php print $node->title;?></h1>
						<?php  print render(field_view_field('node', $node, 'body', array( 'label'=>'hidden', 'type' => 'text_summary_or_trimmed', 'settings'=>array('trim_length' => 200),)));?>
						<strong><?php print l(t('Read more'),'node/'.$result->nid);?></strong>
				</div>
		</div> 
  <?php $i++; endforeach; ?>


	 <div class="billboardMenu">
		 <?php foreach($images as $image):?>
			<div data-bbmenuitem="<?php print $j;?>" class="bbMenuItem" onclick="javascript:billboards.moveTo('<?php print $j;?>');">
					<strong><?php print $j;?></strong>
					<span class="bbMenuItemPhoto shadow">
							<span class="croppedImageWrapper" style="height:75px;width:100px;">
								<img alt="<?php print $image->node_title ?>" src="<?php print file_create_url($image->field_field_slider_image[0]['raw']['uri']); ?>" />
						  </span>
					</span>
			</div>
			<?php $j++; endforeach; ?>
	 </div>
	 
</div>
