<?php
/*
 * @file
 *  My Profile template
 */
global $user;

drupal_add_js(path_to_theme() . '/js/mytabs.js');
$organization = array();
$user_fields = $user_profile;
$uid = (arg(0) == 'user' && is_numeric(arg(1))) ? arg(1) : '';
$userload = user_load($uid);
$name_first = drupal_render($user_fields['field_name_first']);
$name_last = drupal_render($user_fields['field_name_last']);
$field_mypoints = drupal_render($user_fields['field_mypoints']);
$field_valid_certificates = drupal_render($user_fields['field_valid_certificates']);
$field_user_status = drupal_render($user_fields['field_user_status']);
$field_user_address = drupal_render($user_fields['field_user_address']);
$field_user_contact_email = drupal_render($user_fields['field_user_contact_email']);
$field_user_primary_phone = drupal_render($user_fields['field_user_primary_phone']);
$user_organization_info = $user_fields['user_organization_info'];
$userprivacyform = $user_fields['userprivacyform'];

$field_mailing_address  = ''; 
$state_zip = array();
if(!empty($user_fields['field_mailing_address_addr1']['#items'][0]['value']))
$field_mailing_address .= '<div>' . @$user_fields['field_mailing_address_addr1']['#items'][0]['value'] . ',</div>';

if(!empty($user_fields['field_mailing_address_addr2']['#items'][0]['value']))
$field_mailing_address .= '<div>' . @$user_fields['field_mailing_address_addr2']['#items'][0]['value'] . ',</div>';

if(!empty($user_fields['field_mailing_address_city']['#items'][0]['value']))
$field_mailing_address .= '<div>' . @$user_fields['field_mailing_address_city']['#items'][0]['value'] . ',</div>';

if(!empty($user_fields['field_mailing_address_state']['#items'][0]['value'])){
  $state_zip[] = @$user_fields['field_mailing_address_state']['#items'][0]['value'];
}

if(!empty($user_fields['field_mailing_address_zip']['#items'][0]['value'])){
  $state_zip[] = $user_fields['field_mailing_address_zip']['#items'][0]['value'];
}

if(sizeof($state_zip) > 0){
  $field_mailing_address .= '<div>'.implode(' ', $state_zip).',</div>';
}

if(!empty($user_fields['field_mailing_address_country']['#items'][0]['value']))
$field_mailing_address .= '<div>' . @$user_fields['field_mailing_address_country']['#items'][0]['value'] . '</div>';

if($field_mailing_address != '') $field_mailing_address = '<div>'.t('Mailing Address').':</div>' . $field_mailing_address;

$field_organization = drupal_render($user_fields['field_organization']);
$avectra_pass_change = $user_fields['avectra_pass_change'];
$user_membership = $user_fields['user_membership'];
$user_membership_renewal = $user_fields['user_membership_renewal'];
?>

<div>
  <?php print drupal_render($user_fields['links']);?>
</div>

<div id="tabs" class="ui-tabs">
   <div class="ui-tabs-nav">
		<div id="tab-myprofile" class="tabtitle"><a href="#tabcontent-myprofile"><?php print t('My Profile');?></a></div>
		<div id="tab-mytransactions" class="tabtitle"><a href="#tabcontent-mytransactions"><?php print t('My Transactions');?></a></div>
		<div id="tab-myorganizationinfo" class="tabtitle"><a href="#tabcontent-myorganizationinfo"><?php print t('My Organization Info');?></a></div>
		<div id="tab-changepwd" class="tabtitle"><a href="#tabcontent-changepwd"><?php print t('Change Password');?></a></div>
		
		<?php if(function_exists('act_event_user_is_poc')) {
		   if(act_event_user_is_poc($user->uid)){ ?>
		     <div id="tab-membership" class="tabtitle"><a href="#tabcontent-membership"><?php print t('Membership Renewal');?></a></div>
		<?php }
		} ?>
    
    </div>
   
 <div id="accr">
  <div class="accr_sect"><a><?php print t('My Profile');?></a></div>
   <div id="tabcontent-myprofile" class="tabcontent">
    <div class="content">
        <!--- My Profile start--->
         <div class="myprofile myprofile-right">
		  <div class="details myprofile-block">
			<div class="block_action"><?php print l(t('Edit'),'user/'.$uid.'/edit',array('query' => drupal_get_destination()));?></div>
			<h2 class="block_title"><?php print t('My Profile'); ?></h2>
			<div class="block_content">
			  <div class="welcome"><?php print t('Welcome back'); ?>, <?php print $name_first . ' ' . $name_last;?></div>
			  <div class="info">
				<p><?php print t('SIGN IN INFORMATION'); ?></p>
				<p><?php print t('Sign In Email');?>: <?php print $userload->mail;?></p>
			  </div>
			  <div class="info">
				<p><?php print t('ADDRESS & CONTACT INFO');?></p>
				<p><?php print $field_user_address;?></p>
				<p><?php print $field_mailing_address;?></p>
				<p><?php print $field_user_contact_email;?></p>
				<p><?php print $field_user_primary_phone;?></p>
				<p><?php print $field_organization;?></p>
           
			  </div>
			</div>
		  </div>
		</div>

		<div class="myprofile myprofile-top">
		  <div class="user_status myprofile-block">
			<h2 class="block_title"><?php print t('My Privacy'); ?></h2>
			<div class="block_content">
			  <div id="user_privacy_form"><?php print $userprivacyform; ?></div>
			  </div>
		  </div>
		</div>

        <!--- My Profile end--->
    </div>
   </div>
  <div class="accr_sect"><a><?php print t('My Transactions');?></a></div>
   <div id="tabcontent-mytransactions" class="tabcontent">
        <div class="content" id="avectra_pass_change"><?php print $user_membership; ?></div>
   </div>
  <div class="accr_sect"><a><?php print t('My Organization Info'); ?></a></div>
   <div id="tabcontent-myorganizationinfo" class="tabcontent">
      <div class="content">
          <?php print $user_organization_info ;?>
      </div>
   </div>
  <div class="accr_sect"><a><?php print t('Change Password'); ?></a></div>
   <div id="tabcontent-changepwd" class="tabcontent">
      <div class="content" id="avectra_pass_change"><?php print $avectra_pass_change; ?></div>
   </div>
  
  <?php if(function_exists('act_event_user_is_poc')) {
	 if(act_event_user_is_poc($user->uid)){ ?>
  <div class="accr_sect"><a><?php print t('Membership Renewal'); ?></a></div>
  <div id="tabcontent-membership" class="tabcontent">
     <div class="content"><?php print $user_membership_renewal; ?></div>
  </div>
  <?php }
   }
   ?>
  
 </div>
   
</div> <!-- #tabs end-->


