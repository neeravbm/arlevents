<?php
/*
 * @file
 *  My Profile template
 */
global $user;
$user_fields = user_load($user->uid);
$name_first = $user_fields->field_name_first['und'][0]['value'];
$name_last = $user_fields->field_name_last['und'][0]['value'];
$field_mypoints = $user_fields->field_mypoints['und'][0]['value'];
$field_valid_certificates = $user_fields->field_valid_certificates['und'][0]['value'];
$field_total_amount = $user_fields->field_total_amount['und'][0]['value'];
$field_user_status = $user_fields->field_user_status['und'][0]['value'];
$field_user_address = $user_fields->field_user_address['und'][0]['value'];
$field_user_contact_email = $user_fields->field_user_contact_email['und'][0]['value'];
$field_user_primary_phone = $user_fields->field_user_primary_phone['und'][0]['value'];

?>

<div class="myprofile-right">
    <div class="details">
        <div class="block">
				<h2 class="block_title"><?php print t('My Profile'); ?></h2>
				    <div class="block_content">
					    <div class="welcome"> <?php print t('Welcome back'); ?>,<?php print $user->name;?></div>
					    <div class="info"> 
					        
					        <p><?php print t('SIGN IN INFORMATION'); ?></p>
					        <p><?php print t('Sign In email');?>: <?php print $user->mail;?></p>
					    </div>
					    <div class="info"> 
					        <p><?php print t('ADDRESS & CONTACT INFO'); ?></p>
					        <p><?php print t('Mailing Address'); ?>: <?php print $field_user_address;?></p>
					        <p><?php print t('Email Address'); ?>: <?php print $field_user_contact_email;?></p>
					        <p><?php print t('Primary Phone'); ?>: <?php print $field_user_primary_phone;?></p>
					    </div>
				    </div>
			</div>
    </div>
</div>

<div class="myprofile-top">
    <div class="certificate">
        <div class="left">
            <div class="block">
				<h2 class="block_title"><?php print ('My Certificate'); ?></h2>
				    <div class="block_content">
					    <div class="points">
					       <span><?php print t('My Points'); ?></span>
					       <p><strong><?php print $field_mypoints;?></strong></p>
					    </div>
					    <div class="reward">
					       <span><?php print t('My Reward Certificates'); ?></span>
					       <div class="txt">$5</div>
					       <div class="txt">$5</div>
					       <div class="txt">$5</div>
					       <div class="label">Validate Certificate:</div><div class="value"><?php print $field_valid_certificates;?></div>
					       <div class="label">Total Amount:</div><div class="value"><?php print $field_total_amount;?></div>
					    </div>
				    </div>
			</div>
        </div>
        <div class="right"> 
            <div class="block">
				<h2 class="block_title">My Status</h2>
				    <div class="block_content">
					    <p><?php print $field_user_status;?></p>
				    </div>
			</div>
        </div>
    </div>
    <div class="point">
        <div class="block">
				<h2 class="block_title">Point and purchase</h2>
				    <div class="block_content">
						<table>
						    <tr><th>Date</th><th>Description</th><th>Amount</th><th>Points</th></tr>
							<tr><td>01/06/2013</td><td>$5 Reward Zone Certificate </td><td> $5.00</td><td>250</td></tr>
							<tr><td>01/06/2013</td><td>$5 Reward Zone Certificate </td><td>$5.00</td><td>250</td></tr>
							<tr><td>01/06/2013</td><td>$5 Reward Zone Certificate </td><td>$5.00</td><td>250</td></tr>
							<tr><td>01/06/2013</td><td>$5 Reward Zone Certificate </td><td>$5.00</td><td>250</td></tr>
							<tr><td>01/06/2013</td><td>$5 Reward Zone Certificate </td><td>$5.00</td><td>250</td></tr>
							<tr><td>01/06/2013</td><td>$5 Reward Zone Certificate </td><td>$5.00</td><td>250</td></tr>
						</table>
						
				    </div>
			</div>
    </div>
</div>

