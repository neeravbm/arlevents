<?php
/*
 * @file
 *  My Public Profile template
 */
global $user;

//drupal_render($user_avatar);
//define('FLAG_FRIEND_BOTH', 0);
//define('FLAG_FRIEND_FLAGGED', 1);
//define('FLAG_FRIEND_UNFLAGGED', 2);
//define('FLAG_FRIEND_APPROVAL', 3);
//define('FLAG_FRIEND_PENDING', 4);


$email = $account->mail;
$account_id = $account->uid;
$friend = '';
$flag = flag_get_flag('friend');
$status = flag_friend_determine_friend_status($flag, $user->uid, $account->uid);
if ($status == FLAG_FRIEND_APPROVAL || $status == FLAG_FRIEND_FLAGGED) {
  $friend .= flag_friend_create_link('unfriend', $account->uid);
}
else {
  if ($status == FLAG_FRIEND_UNFLAGGED) {
    $friend .= l(t('Add friend'), "flag/confirm/flag/friend/$account_id",
      array('query' => drupal_get_destination(), 'html' => TRUE, 'attributes' => array('title' => $flag->flag_short),));
  }
  else {
    if ($status == FLAG_FRIEND_PENDING) {
      $friend .= l(t('Friend Requested. Cancel?'), "flag/confirm/unflag/friend/$account_id", array(
        'query' => drupal_get_destination(),
        'html' => TRUE,
        'attributes' => array('title' => t('Friend Requested. Cancel?')),
      ));
    }
    else {
      $friend .= flag_friend_create_link('friend', $account->uid);
    }
  }
}

$name = array();
$name_first = field_get_items('user', $account, 'field_name_first');
$name[] = $name_first[0]['value'];

$name_last = field_get_items('user', $account, 'field_name_last');
$name[] = $name_last[0]['value'];
if (sizeof($name) > 0) {
  $fullname = implode(' ', $name);
}
$subject = t('Friend Message from ACT');

$field_keep_photo_private = field_get_items('user', $account, 'field_keep_photo_private');
$field_keep_photo_private = !empty($field_keep_photo_private[0]['value']) ? $field_keep_photo_private[0]['value'] : 0;

$field_keep_email_private = field_get_items('user', $account, 'field_keep_email_private');
$field_keep_email_private = !empty($field_keep_email_private[0]['value']) ? $field_keep_email_private[0]['value'] : 0;

$field_keep_phone_number_private = field_get_items('user', $account, 'field_keep_phone_number_private');
$field_keep_phone_number_private =
  !empty($field_keep_phone_number_private[0]['value']) ? $field_keep_phone_number_private[0]['value'] : 0;

$field_keep_name_private = field_get_items('user', $account, 'field_keep_name_private');
$field_keep_name_private = !empty($field_keep_name_private[0]['value']) ? $field_keep_name_private[0]['value'] : 0;

?>
<div>
  <?php print drupal_render($user_fields['links']); ?>
</div>

<div class="public-profile">

  <?php if (!$field_keep_photo_private): ?>
    <div class="image">
      <?php print $user_picture; ?>
    </div>
  <?php endif; ?>

  <div class="user_info">

    <div class="user-details">
      <div class="name"><?php print $fullname; ?></div>
      <?php /* if (!$field_keep_email_private): ?><div class="email"><?php print $email;?></div><?php endif; */ ?>
      <?php if (!$field_keep_phone_number_private): ?>
        <div class="phone"><?php print drupal_render(field_view_field('user', $account,
          'field_user_primary_phone')); ?></div><?php endif; ?>
      <?php if ($user->uid != $account->uid || $user->uid == 1): ?>
        <div class="send-message"><?php print l('Send Message', 'messages/new/' . $account->uid . '/' . $subject,
            array('query' => drupal_get_destination())); ?></div>
      <?php endif; ?>

      <?php if ($user->uid != $account->uid): ?>
        <div class="friends-link"><?php print $friend; ?></div>
      <?php endif; ?>

    </div>


    <div class="user_bio">
      <?php print drupal_render(field_view_field('user', $account, 'field_bio')); ?>
    </div>

    <div class="user_organization">
      <?php print drupal_render(field_view_field('user', $account, 'field_organization')); ?>
    </div>

    <div class="user_social_links">
      <?php print drupal_render(field_view_field('user', $account, 'field_facebook_url')); ?>
      <?php print drupal_render(field_view_field('user', $account, 'field_linkedin_url')); ?>
      <?php print drupal_render(field_view_field('user', $account, 'field_twitter_url')); ?>
    </div>

  </div>

</div>


