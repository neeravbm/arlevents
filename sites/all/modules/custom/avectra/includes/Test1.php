<?php

require_once "ACT.php";
require_once "Profile.php";

//PRODUCTION ENVIRONMENT...
define ("XWEBUSER", "ACTDrupalCMSxweb");
define ("XWEBPASS", "Du9Xw3Ac$$");
define ("SOAPURL", "https://members.actgov.org/xweb/secure/netFORUMXML.asmx?WSDL");

$act = new ACT(SOAPURL, XWEBUSER, XWEBPASS);

$customerKey = $act->loginUser('Nonmember@actiac.org', 'password', $token);
print_r($customerKey);

$eventList = $act->getEventList(TRUE);
print_r($eventList);

foreach ($eventList as $event) {
  $eventKey = (string) $event->getEventKey();
  print_r($eventKey);

  $price = $act->getEventPricing($eventKey, $customerKey);
  $fee = (string) $price['Price'];
  print_r('Fee is: ' . $fee);
  $price_key = (string) $price['prc_Key'];
  print_r('Price key is: ' . $price_key);
}

exit;

?>
