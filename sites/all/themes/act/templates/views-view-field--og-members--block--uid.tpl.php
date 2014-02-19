<?php
$gid = arg(1);
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
$og_membership = og_get_membership('node', $gid, 'user', $account->uid);
//dsm($og_membership->created);

$field_keep_photo_private = field_get_items('user', $account, 'field_keep_photo_private');
$field_keep_photo_private = !empty($field_keep_photo_private[0]['value']) ? $field_keep_photo_private[0]['value'] : 0;

$field_keep_email_private = field_get_items('user', $account, 'field_keep_email_private');
$field_keep_email_private = !empty($field_keep_email_private[0]['value']) ? $field_keep_email_private[0]['value'] : 0;

$field_keep_phone_number_private = field_get_items('user', $account, 'field_keep_phone_number_private');
$field_keep_phone_number_private = !empty($field_keep_phone_number_private[0]['value']) ? $field_keep_phone_number_private[0]['value'] : 0;

?>

<div class="friend_block"><?php if(!$field_keep_photo_private) { print $picture; } ?>
  <div class="frnd_info">
    <div class="frnd_name"><?php print l(format_username($account),'user/'.$account->uid); ?></div>
    <?php if(function_exists('act_userprofile_get_user_organization')) { ?> <div class="frnd_org"> <?php print act_userprofile_get_user_organization($account->uid); ?> </div> <?php  } ?> 
  <?php /* ?>  <div class="frnd_joined"><?php print t('Joined '); ?><?php print format_date($og_membership->created,'custom','M d, Y'); ?></div>
    <div class="frnd_last_access"><?php print t('Last visited '); ?><?php print format_date($account->access,'custom','M d, Y'); ?> <?php */ ?>
</div>
  </div>
</div>
