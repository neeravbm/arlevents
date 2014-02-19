<?php

require_once "ACT.php";
require_once "Profile.php";

define ("XWEBUSER", "ACTDrupalCMSxweb");
define ("XWEBPASS", "Du9Xw3Ac$$");
define ("SOAPURL", "https://members.actgov.org/netFORUMACTDev/xweb/secure/netForumXML.asmx?WSDL");
//define ("SOAPURL", "http://members.actgov.org/netForumACTDev2/xweb/secure/netForumXML.asmx?WSDL");

// customer key for laura@arltechgroup.com: '4dd84bc5-4af0-4ae9-b6a0-313a3e82b845' 


$act = new ACT(SOAPURL, XWEBUSER, XWEBPASS);

$eventsArray = array(array('evt_key'=> 'a5c371fd-3cdb-4072-bcd6-8fba7d27c6be', 'sessions'=>Array('3d2d17bf-ce52-4c51-8b0a-17abb99a2196'), 'tracks'=>''));
$payment  = new Payment("Thomas Evans", "4111111111111111", "121", "2013/12", "598f9016-e1ab-48dc-a3b6-b53cfff5a8a4","0.00");			
$regkey = $act->registerForEvent($eventsArray, $payment);
print_r($regkey);
exit;

?>
