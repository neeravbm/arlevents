<?php
global $user;
$friend = '';
$flag = flag_get_flag('friend');
$status = flag_friend_determine_friend_status($flag, $user->uid, $output);
  if ($status == FLAG_FRIEND_APPROVAL || $status == FLAG_FRIEND_FLAGGED) {
    $friend .= flag_friend_create_link('unfriend', $output);
  } else if($status == FLAG_FRIEND_UNFLAGGED){
	$friend .= l( t('Add friend'), "flag/confirm/flag/friend/$output", array('query' => drupal_get_destination(), 'html' => TRUE,'attributes'  => array('title' => $flag->flag_short),));
  } else if($status == FLAG_FRIEND_PENDING){
	$friend .=l(t('Friend Requested. Cancel?'),"flag/confirm/unflag/friend/$output", array('query' => drupal_get_destination(), 'html' => TRUE,'attributes'  => array('title' => t('Friend Requested. Cancel?')),));
  } else {
	$friend .= flag_friend_create_link('friend', $output);
  }
  if($user->uid != $output){
    echo  $friend;
  }
?>
