<?php

require_once "ACT.php";
require_once "Profile.php";


//TEST ENVIRONMENT...
/*define ("XWEBUSER", "ACTDrupalCMSxweb");
define ("XWEBPASS", "Du9Xw3Ac$$");
define ("SOAPURL", "https://members.actiac.org/netFORUMACTDev2/xweb/secure/netForumXML.asmx?WSDL");  // Dev1*/


//PRODUCTION ENVIRONMENT.../*
define ("XWEBUSER", "ACTDrupalCMSxweb");
define ("XWEBPASS", "Du9Xw3Ac$$");
define ("SOAPURL", "https://members.actgov.org/xweb/secure/netFORUMXML.asmx?WSDL"); 




//define ("SOAPURL", "http://members.actgov.org/netForumACTDev2/xweb/secure/netForumXML.asmx?WSDL");  // Dev2
//define ("SOAPURL", "https://members.actgov.org/xweb/secure/netForumXML.asmx?WSDL");  //production server

// customer key for laura@arltechgroup.com: '4dd84bc5-4af0-4ae9-b6a0-313a3e82b845' 

// ELC - 'a5c371fd-3cdb-4072-bcd6-8fba7d27c6be' 

$act = new ACT(SOAPURL, XWEBUSER, XWEBPASS);

// Logs the user in - $token will be filled with the SSO token

//DEV Logins...
//$customerKey = $act->loginUser('zzlglynn@actgov.orgzz', 'password', $token);
//$customerKey  = $act->loginUser('zzlaura@arltechgroup.comzz', '3caseboys', $token);
//$customerKey  = $act->loginUser('Stanley.Morgenstein@lmco.com', 'Johnshaw', $token);
//$customerKey = $act->loginUser('zztestsubject2@actgov.orgzz', 'password', $token);
//$customerKey  = $act->loginUser('zzmartha.dorris@gsa.govzz', 'password', $token);
//$customerKey  = $act->loginUser('zznaveen@rivasolutionsinc.comzz', 'password', $token);

//Test 1 - Successful login with user and presentation of customer key
//PROD Logins....
//$customerKey = $act->loginUser('governmenttest1@gsa.gov', 'password', $token);
//$customerKey = $act->loginUser('nonmembertest1@novics.com', 'password', $token);
//$customerKey = $act->loginUser('act-iac@actgov.org', 'actgovorg', $token);
//$customerKey = $act->loginUser('mark@fedbizcoach.com', 'Pretzel-900', $token);
//$customerKey = $act->loginUser('laura@arltechgroup.com', '4children', $token);
//$customerKey = $act->loginUser('Government@actiac.org', 'password', $token);
//$customerKey = $act->loginUser('DrupalIndustry@actiac.org', 'password', $token);
$customerKey = $act->loginUser('Nonmember@actiac.org', 'password', $token);
print_r($customerKey);





echo("Running test script.....<br><br>");
echo("Logged in with customer key:<br>");
var_dump($customerKey);
//exit;
echo("<br><rr>");

$act->setDebug(true);
exit;


//$response = $act->changeUserPasswordForce($res, '3caseboys');
//var_dump($response);

if ($customerKey != false) {
echo('<pre>');
//$result = $act->getCompanies(); //var_dump($result); //echo('done.');
//$res = $act->getCustomerKeyFromEmail('Geoff@oldtownit.com');
//var_dump("Customer key via email:".$res);

//TEST 2:  Verify that we can get the user's profile data...
/*$prof = $act->getProfileInfo($customerKey);
var_dump($prof);*/
/*$eventList = $act->getEventList(TRUE);
print_r($eventList);*/
exit;

/*
echo("<BR><BR><BR>GOVERNMENT<BR><BR>");
$customerKey = $act->loginUser('Government@actiac.org', 'password', $token);
$res = 	$act->getEventPricing("f9586b7f-c949-4dae-b779-142ae9834098", $customerKey);
var_dump($res);

echo("<BR><BR><BR>INDUSTRY<BR><BR>");
$customerKey = $act->loginUser('DrupalIndustry@actiac.org', 'password', $token);
$res = 	$act->getEventPricing("f9586b7f-c949-4dae-b779-142ae9834098", $customerKey);
var_dump($res);

echo("<BR><BR><BR>NONMEMBER<BR><BR>");
$customerKey = $act->loginUser('Nonmember@actiac.org', 'password', $token);
$res = 	$act->getEventPricing("f9586b7f-c949-4dae-b779-142ae9834098", $customerKey);
var_dump($res);
*/
/*
//TEST 3: Get All Committees
$committeeList = $act->getCommitteeList();
var_dump($committeeList);
*/

//$retVal = $act->getActiveInvoicesForCustomerOrganization('42e06af6-70b2-4053-ae8b-e7e687ec8a9e');
//var_dump($retVal);
/*
//active invoices for organization...
echo('ORGANIZATION <BR>');
$retVal = $act->getActiveInvoicesForCustomer('42e06af6-70b2-4053-ae8b-e7e687ec8a9e', 'ba5691f5-2de0-445a-aaa1-6fcfe19b7417');
var_dump($retVal);


echo('<BR>INDIVIDUAL<BR>');
//active invoices for individual...
$retVal = $act->getActiveInvoicesForCustomer('4dd84bc5-4af0-4ae9-b6a0-313a3e82b845');
var_dump($retVal);

//active invoices for individual...
$retVal = $act->getActiveInvoicesForCustomer($customerKey);
var_dump($retVal);
*/

$retVal = $act->getOrganizationMembers('c33fd71b-4674-460c-966e-ebdc784db99c');
var_dump($retVal);
/*
$retVal = $act->getIsIndividualPOCAtOrganization('42e06af6-70b2-4053-ae8b-e7e687ec8a9e', 'ba5691f5-2de0-445a-aaa1-6fcfe19b7417');
if ($retVal)
{
	echo('user is a POC');
}
else
{
	echo('user is not a POC');
}
*/
//TEST 4: Get a list of open invoices...
//$invoiceList = $act->getActiveInvoicesForCustomer('ba5691f5-2de0-445a-aaa1-6fcfe19b7417');
//var_dump($invoiceList);

//GET profile Info For Geoff:
//$profile = $act->getProfileInfo('42e06af6-70b2-4053-ae8b-e7e687ec8a9e');

//$profile = $act->getProfileInfo('42e06af6-70b2-4053-ae8b-e7e687ec8a9e');
//var_dump($profile);

/*
//TEST 4: Get Committees for User...
$res = $act->getCommitteesForUser($customerKey);
var_dump($res);
*/


//Test 5: Add User To Committee...
//$status = $act->addUserToCommittee('3f54b7c2-6b34-4fc7-a7b4-51c365a02833', $customerKey, 'a26678ad-1011-4444-aefd-cbb64cf8111c', '2013-10-23');
//var_dump($status);


/*
//TEST 6: Check that User Was Added To Committee...
$res = $act->getCommitteesForUser($customerKey);
var_dump($res);
*/

/*
//TEST 7: Get Agencies...
$res = $act->getAgencies();
var_dump($res);
*/

/*
//TEST 8: Get Committees Created Between...
$response = $act->getCommitteesCreatedBetween('2012-01-01', '2012-12-30');
var_dump($response);
*/

//TEST 9: Get User Profeil From Email Address..
//$res = $act->getCustomerKeyFromEmail('laura@arltechgroup.com');
//var_dump($res);


//$res = $act->getCommitteesForUser($customerKey);
//var_dump($res);

//$cst = '6131c5e7-9f41-49dd-9c0b-8034055f5c13';
//$cst = '4dd84bc5-4af0-4ae9-b6a0-313a3e82b845';
//$cmt = '304b2cb9-5891-4da2-af30-08fec81b2173';
//$res = $act->isUserNominatedForCommittee($cst, $cmt);
//$res = $act->isUserInCommittee($cst, $cmt);
	
	
//$res = $act->getQuery('CommitteeNominations  @TOP -1', 'count(nom_add_date)', '');
//foreach($res as $obj) {
//var_dump($res);
//}
	
	
//$res = $act->getQuery('Individual', 'ind_customer_type_ext', 'ind_customer_type_ext <> \'Government\'');
//foreach($res as $obj) {
//	echo($obj->ind_customer_type_ext . '<br>');
//}





//$response = $act->changeUserPasswordForce('4dd84bc5-4af0-4ae9-b6a0-313a3e82b845' /*$res*/, '3caseboys');
//var_dump($response);




//$prof = $act->getProfileInfo('4dd84bc5-4af0-4ae9-b6a0-313a3e82b845');
//var_dump($prof);
/*
// Sample code to insert a user
$prof = $act->getProfileInfo('92c86938-85ff-433d-9f9c-001eca02041e');

$prof->setFirstName('TTest');
$prof->setLastName('UUser');
$prof->setEmail('ttestuuser@domain.net');
$prof->setState('NC');
$res = $act->insertUser($prof);
var_dump($res);
*/

/*$exp_date = "2006-01-16";
$todays_date = date("Y-m-d");

$today = strtotime($todays_date);
$expiration_date = strtotime($exp_date);

if ($expiration_date > $today) {
     $valid = "yes";
} else {
     $valid = "no";
}
echo($valid);
*/

/*
 	// Sample for insertOrganization
	$profObject = $act->getProfileInfo('4a396c5d-4afc-42f2-9d4b-cb037f8d0158');
	$orgKey = $profObject->getOrganizationKey();
	//$orgObject = $act->getOrganizationInfo($orgKey);
	var_dump('ORG KEY');
	var_dump($orgKey);
*/	
/*
	$orgObject->setName("Test Org 1");
	$orgObject->setNumEmployees("5");
	$res = $act->insertOrganization($orgObject);
*/


//$profObject = $act->getProfileInfo($customerKey);
//$orgKey = $profObject->getOrganizationKey();
//$orgObject = $act->getOrganizationInfo($orgKey);
//$res = $act->updateOrganization($orgObject);
//var_dump($res);
//print_r($orgObject);


//$orgObject = $act->getOrganizationInfo('b49e7c2c-3345-43ad-9482-d05457f799ca');

//var_dump($orgObject);

//$orgObject->setDateFounded('2013-09-06 00:00:00'); //('09/06/2013');
//$orgObject->setDescription('Testing');
//$orgObject->setNumEmployees('No of employees');
//$orgObject->setCompanyRevenue('Company revenue');
//$orgObject->setGovernmentRevenue('Government revenue');
//$orgObject->setFiscalYearEnd('January');
//$orgObject->setType('Industry');
//$orgObject->setContact('f97ae8a8-8f6f-478e-ac1e-753afc441f74');
//$orgObject->setReferral('Referral');
//$orgObject->setComFedStateLocal('ComFedStateLocal');
//$orgObject->setAddress('Address');
//$orgObject->setPhone('703-555-1212');

//$res = $act->updateOrganization($orgObject);

//var_dump($res);



//	$invoiceArray = $act->getInvoicesForUser($customerKey);
//	$invoiceArray = $act->getInvoicesForUser('afc32dc6-9797-4b12-9f06-74aae4a10353');
//var_dump($invoiceArray);


//$eventsArray = $act->getEventsForUser($customerKey);  
//	$eventsArray = $act->getEventsForUser('95c464f2-53ad-4a36-98e2-0642a3e35305');  // 4 events
//	$eventsArray = $act->getEventsForUser('afc32dc6-9797-4b12-9f06-74aae4a10353');  // 1 event
//	$eventsArray = $act->getEventsForUser($customerKey);   // 0 events
//var_dump($eventsArray);
//print_r($eventsArray);

//	$membershipArray = $act->getMembershipsForUser('a2eb978c-bf3b-4d6a-9dad-a5595f2f9a8d');
//	$membershipArray = $act->getMembershipsForUser($customerKey);
//var_dump($membershipArray);
//	$chaptersArray = $act->getChaptersForUser($customerKey);
	
//	$downloadsArray = $act->getDownloadsForUser($customerKey);

	// getCommitteeList example
//	$committeeList = $act->getCommitteeList(FALSE);
//print_r($committeeList);
		
	// getCommitteeDetail example
	//$committee = $act->getCommitteeDetails('3f54b7c2-6b34-4fc7-a7b4-51c365a02833');
	//var_dump($committee);
	
//$res = $act->getCommitteePositionKeyFromName('9fa26524-a5a7-423a-8151-0be0db8dbf3f', 'member');
//echo('Committee position key: '.$res);

//	$committepositions = $act->getCommitteePositions('da6a5937-f220-4a4d-8e28-cb11c5b2ed44');
//	var_dump($committeepositions);	

	//Add user to committee...
	//$results = $act->addUserToCommittee('9fa26524-a5a7-423a-8151-0be0db8dbf3f', $customerKey, 'a97475e2-bce7-442f-ba30-1ee311db765d','2013-23-10');	
/*
	//get all committees and subcommittees...
$committeeList = $act->getCommitteeList();
	foreach ($committeeList as $obj) {
		echo($obj->getKey() . '<br>');	
		echo($obj->getName().'<br>');
		echo('--------------------------------------------<br>');
		$subCommitteeList = $act->getSubCommitteeList($obj->getKey());
	foreach ($subCommitteeList as $obj2) {
	echo('         subcommittee: '.$obj2->getCode().'<br>');
	}	
	echo('***********************************************<br>');
	}
*/	
/*
	//Get committee members example...
	//dee2b766-1049-4b14-a33a-b64401126ff4
	// 1d16e367-f243-4a98-a69f-8438023baa28 - 2014 Voyager Selection
	$committee = $act->getCommitteeMembers('694c45f2-dd3e-4e99-be37-5518d48379b3');
foreach ($committee as $member) {
	echo($member->cst_sort_name_dn . '  ' . $member->cpo_code . '<br>');
}
//var_dump($committee);
	//echo("COMMITTEE LIST ******************************************<br><br>");
//	print_r($committee);
	*/

//	$events = $act->getEventList(true);
//	var_dump($events);
	//foreach ($events as $obj)
	//{
//		echo($obj->getEventName().' '.$obj->getEventKey().'<br>');/
//	}

	/*
	foreach ($events as $obj) {
		echo($obj->getEventKey() . '  => ' . $obj->getStartTime() . ' ' . $obj->getEndTime().  '<br>');
	}
*/
//	$sessions = $act->getEventSessions('a5c371fd-3cdb-4072-bcd6-8fba7d27c6be');
//	foreach ($sessions as $obj) {
//		echo($obj->ses_title . '<br>');
//	}

//	$tracks = $act->getEventTracks('41818015-95eb-4439-b066-c48d54d93c5a', true);
//var_dump($tracks);

//	foreach ($tracks as $obj) {
//		echo($obj->trk_name . '<br>');
//		$sessions = $act->getSessionsForTrack($obj->trk_key);
//	}
	

//	$eventsArray = $act->getRecentEvents('2013-06-14');
/*
	$users = $act->getUsersCreatedSince('1963-01-01');
	foreach ($users as $obj) {
		 var_dump($obj-ind_cst_key);
	}
*/
//var_dump($users);

	//$eventsArray = $act->getEventList(true);
//	var_dump($eventsArray);
	
	//$boolResult = $act->isUserRegisteredForEvent($customerKey, "6a6986eb-b1ab-44fd-92ce-cb03fbb6385f");
	//var_dump($boolResult);
	// This example adds an event and a session to the cart
	//$eventsArray = $act->getEventList(true);
	//var_dump($eventsArray);	
	//$sessions = $eventsArray[1]->getSessionArray();
	
	//test event 2:  f9586b7f-c949-4dae-b779-142ae9834098

/*
//Production environment sample registration call...
	$eventsArray = array(array('participant_key'=>$customerKey, 'evt_key'=> '24e32330-fa46-4268-93cf-87a6408c856b', 'prc_key' => '547cdfd9-58f8-46e4-aaa0-3ebf82aa15ae', 'sessions'=>'', 'tracks'=>''));	
	$payment  = new Payment("", "", "", "", "","0.00");			
	$regkey = $act->registerForEvent($customerKey, $eventsArray, $payment);
	echo('<br><br><br>RESPONSE XML<br><br><br>');
	var_dump($regkey);
*/


/*
//try to add a membership to the cart...
$eventsArray = '';
$membershipArray = array('611e88da-3c66-4469-8f19-d2fcdb1fc4bd');
	//$payment  = new Payment("", "", "", "", "","0.00");		
	$payment  = new Payment("Thomas Evans", "4111111111111111", "111", "2014/04", "","6.00");			
	$regkey = $act->registerForEvent($customerKey, $eventsArray, $payment, $membershipArray);
	//echo('<br><br><br>RESPONSE XML<br><br><br>');
	//var_dump($regkey);
	*/
//Dev environment sample registration call...
/*
	$eventsArray = array(array('participant_key'=>$customerKey, 'evt_key'=> 'edabadbb-b20b-4835-b6ea-41f3bdc93fdf', 'prc_key' => '590f6aa7-d4ab-439f-b651-c4972052bf00', 'sessions'=>'', 'tracks'=>''));	
	//$payment  = new Payment("", "", "", "", "","0.00");		
	$payment  = new Payment("Thomas Evans", "4111111111111111", "111", "2014/04", "598f9016-e1ab-48dc-a3b6-b53cfff5a8a4","6.00");			
	$regkey = $act->registerForEvent($customerKey, $eventsArray, $payment);
	echo('<br><br><br>RESPONSE XML<br><br><br>');
	var_dump($regkey);
*/	
	//Event 70: edabadbb-b20b-4835-b6ea-41f3bdc93fdf
	//ELC: a5c371fd-3cdb-4072-bcd6-8fba7d27c6be
	//$res = $act->getAllRegistrantTypes();
	//var_dump($res);

//$res = $act->GetRegistrantTypes('ea16bafe-d45d-478a-807a-78b1167ba8fe');
//var_dump($res);

//TEST EVENT 70 -> edabadbb-b20b-4835-b6ea-41f3bdc93fdf
//DRUPAL TEST EVENT -> ea16bafe-d45d-478a-807a-78b1167ba8fe
//DURPAL TEST EVENT 2-> f9586b7f-c949-4dae-b779-142ae9834098
//DRUPAL ACQUISIONT EVENT ->6a6986eb-b1ab-44fd-92ce-cb03fbb6385f
//DRUPAL TEST FREE EVENT -> 24e32330-fa46-4268-93cf-87a6408c856b

//$res = 	$act->getEventPricing("24e32330-fa46-4268-93cf-87a6408c856b", $customerKey);
//var_dump($res);


	
//$res = 	$act->getEventPricing("ea16bafe-d45d-478a-807a-78b1167ba8fe", $customerKey, 'Industry Drupal');
//var_dump($res);
	

//$res = $act->getEventList(true);
//var_dump($res);
/*foreach ($res as $event) {

	$res2 = $act->getEventDetails($event->getEventKey());
	foreach ($res2 as $s) {
//		echo('Event: ' . $event->getEventKey() . '\n');
		
		var_dump($s);
		//echo($s->trk_key . '\n');
	}
}



	$event = $act->getEventDetails('cbb6f9f4-4ff2-4454-a216-785e2e1dfafb');
//	$event = $act->getEventDetails('a5c371fd-3cdb-4072-bcd6-8fba7d27c6be');
//print_r($event);


	$eventsArray = $act->getEventList(false);
		
//	$profile = $act->
Info('95c464f2-53ad-4a36-98e2-0642a3e35305');
//	print_r($profile);
//	$events = $act->getRecentEvents('2012-10-11 00:00');
//var_dump($events);
//	$eventsArray = $act->getEventsForUser('95c464f2-53ad-4a36-98e2-0642a3e35305');  // 4 events
//var_dump($eventsArray);
//	$eventsArray = $act->getEventsForUser('afc32dc6-9797-4b12-9f06-74aae4a10353');  // 1 event
//var_dump($eventsArray);

//	$eventsArray = $act->getEventsForUser('ccde8228-68ba-4594-acd3-564d7f872f95'); 
//var_dump($eventsArray);
	


// Outout all registrants for the ELC	
//$res = $act->getUsersForEvent('4a396c5d-4afc-42f2-9d4b-cb037f8d0158');
//print_r($res);

	$prof = $act->getProfileInfo($customerKey);
//	$prof->setTitle("ACT-IAC Website Contractorr");
//	$prof->setCity("New City");
//	$prof->setEmail("");
//	$response = $act->updateUserInfo($prof);
var_dump($prof);
//	$response = $act->getProfileInfo($customerKey);
//var_dump($response);

//$response = $act->getRecentCommittees('2013-05-01', FALSE);
//var_dump($response);


/*
$profile = $act->getProfileInfo('4a396c5d-4afc-42f2-9d4b-cb037f8d0158');
$events = $act->getRecentEvents('2013-01-01 00:00');
print_r($events);
$eventsArray = $act->getEventsForUser('4a396c5d-4afc-42f2-9d4b-cb037f8d0158');  // 4 events
print_r($eventsArray);
//$eventsArray = $act->getEventsForUser('afc32dc6-9797-4b12-9f06-74aae4a10353');  // 1 event
//print_r($eventsArray);
*/
	
	//$response = $act->getIndustryUsers();
	
//$response = $act->getPaymentMethods();
//var_dump($response);



echo('</pre>');	
	
//}



?>

