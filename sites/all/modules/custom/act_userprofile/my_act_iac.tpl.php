<?php 
/*
 * @file
 *  My ACT-IAC template file
 */
 
$user_fields = $variables['users'];
drupal_add_js(drupal_get_path('theme','act') . '/js/act.js');
$name = array();
$name[] =  $user_fields->field_name_first['und'][0]['value'];
$name[] =  $user_fields->field_name_last['und'][0]['value'];
if(sizeof($name) > 0){
 $username = implode(' ',$name);	
}
?>
<div id='myDiv'></div>
<div class="content-top">
	<div class="last"><?php print l(t('Log out'),'user/logout');?></div>
	<div class="first"><?php print t('My ACT-IAC');?></div>
	<div class="list"><?php print t('Hello'); ?>, <?php print $username;?></div>
</div>

<div class="clear"></div>

<div class="content-bottom">
	<div id="tabs-titles">
		<div id="tab-myEvents" class="current"> <!-- default (on page load), first one is currently displayed -->
			<a href="#tab-myEvents"><?php print t('MyEvents');?></a>
		</div>
		<div id="tab-myCommunities">
		   <a href="#tab-myCommunities"><?php print t('MyCommunities');?></a>
		</div>
		<div id="tab-myKnowledgeBank">
			<a href="#tab-myKnowledgeBank"><?php print t('MyKnowledgeBank');?></a>
		</div>
		<div id="tab-myContacts">
			<a href="#tab-myContacts"><?php print t('MyContacts');?></a>
		</div>
		<div id="tab-mySubmittals">
		    <a href="#tab-mySubmittals"><?php print t('MySubmittals');?></a>
		</div>
		<div id="tab-myAnnouncements">
		    <a href="#tab-myAnnouncements"><?php print t('MyAnnouncements');?></a>
		</div>
	</div>

    <div id="tabs-contents">
        <div id="tabcontent-myEvents" class="content">
              <h2 class="block_title"><?php print t('My Events'); ?></h2>
		      <table id="myevent">
				<tr><td>Small Business Conference</td><td><?php print date('M d, Y');?></td><td> The Hotel</td></tr>
				<tr><td>MOC</td><td><?php print date('M d, Y');?></td><td>The Hotel</td></tr>
				<tr><td>Small Business Conference</td><td><?php print date('M d, Y');?></td><td> The Hotel</td></tr>
				<tr><td>MOC</td><td><?php print date('M d, Y');?></td><td>The Hotel</td></tr>
				<tr><td>Small Business Conference</td><td><?php print date('M d, Y');?></td><td> The Hotel</td></tr>
				<tr><td>MOC</td><td><?php print date('M d, Y');?></td><td>The Hotel</td></tr>
				<tr><td>Small Business Conference</td><td><?php print date('M d, Y');?></td><td> The Hotel</td></tr>
				<tr><td>MOC</td><td><?php print date('M d, Y');?></td><td>The Hotel</td></tr>
				<tr><td>Small Business Conference</td><td><?php print date('M d, Y');?></td><td> The Hotel</td></tr>
				<tr><td>MOC</td><td><?php print date('M d, Y');?></td><td>The Hotel</td></tr>
			   </table>
        
        </div>
        <div id="tabcontent-myCommunities" class="content">This information is pulled from Avectra software.  Develop this so the information lands on the page separated by a light grey back ground on the first row and white background on second row – follow the pattern.</div>
        <div id="tabcontent-myKnowledgeBank" class="content">This information is pulled from Avectra software.  Develop this so the information lands on the page separated by a light grey back ground on the first row and white background on second row – follow the pattern.</div>
        <div id="tabcontent-myContacts" class="content">This information is pulled from Avectra software.  Develop this so the information lands on the page separated by a light grey back ground on the first row and white background on second row – follow the pattern.</div>
        <div id="tabcontent-mySubmittals" class="content">This information is pulled from Avectra software.  Develop this so the information lands on the page separated by a light grey back ground on the first row and white background on second row – follow the pattern.</div>
        <div id="tabcontent-myAnnouncements" class="content">This information is pulled from Avectra software.  Develop this so the information lands on the page separated by a light grey back ground on the first row and white background on second row – follow the pattern.</div>
    </div>

</div>



