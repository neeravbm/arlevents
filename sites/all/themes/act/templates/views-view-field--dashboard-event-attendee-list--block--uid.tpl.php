<?php
if($output){
	$account = user_load($output);
	if(is_object($account->picture)){
	  $uri = $account->picture->uri;
	} else {
	  $uri = variable_get('user_picture_default'); 
	}
	  $picture = theme('image_style',
		array(
			'style_name' => '50x50',
			'path' => $uri,
			'attributes' => array(
			'class' => 'avatar'
				),
			'width' => NULL,
			'height' => NULL,            
			)
		);


$field_keep_photo_private = field_get_items('user', $account, 'field_keep_photo_private');
$field_keep_photo_private = !empty($field_keep_photo_private[0]['value']) ? $field_keep_photo_private[0]['value'] : 0;

?>
<div class="friend_block"><?php if(!$field_keep_photo_private) { print $picture; } ?></div>

<?php } ?>
