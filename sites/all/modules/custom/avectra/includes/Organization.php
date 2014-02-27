<?php

class Member {
	private $custKey;
	private $firstName;
	private $lastName;
	private $email;
	private $relationship;
	
	/******************************************************************/
	public function __construct($key, $first, $last, $eml, $rlt) {
		$this->custKey = $key;
		$this->firstName = $first;
		$this->lastName = $last;
		$this->email = $eml;
		$this->relationship = $rlt;		
	}
	
	public function getCustomerKey() { return $this->custKey; }
	public function getFirstName() { return $this->firstName; }
	public function getLastName() { return $this->lastName; }
	public function getEmail() { return $this->email; }
	public function getRelationship() { return $this->relationship; }
	
}


class Organization {

	private $orgKey;
	private $orgName;
	private $orgDateFounded;
	private $orgDescription;
	private $orgNumEmployees;
	private $orgCompanyRevenue;
	private $orgGovRevenue;
	private $orgFiscalYearEnd;
	private $orgType;
	private $contactKey;
	private $referral;
	private $comFedStateLocal;
	private $address;
	private $phone;
	private $deleteflag;
	private $terminationDate;

	
/*	
Main phone
Org event registrations?
*/
	
	private $orgRoster;

	public function getKey() { return $this->orgKey; }
	public function getName() { return $this->orgName; }
	public function getDateFounded() { return $this->orgDateFounded; }
	public function getDescription() { return $this->orgDescription; }
	public function getNumEmployees() { return $this->orgNumEmployees; }
	public function getCompanyRevenue() { return $this->orgCompanyRevenue; }
	public function getGovernmentRevenue() { return $this->orgGovRevenue; }
	public function getFiscalYearEnd() { return $this->orgFiscalYearEnd; }
	public function getType() { return $this->orgType; }
	public function getOrgRoster() { return $this->orgRoster; }
	public function getContact() { return $this->contactKey; }
	public function getReferral() { return $this->referral; }
	public function getComFedStateLocal() { return $this->comFedStateLocal; }
	public function getAddress() { return $this->address; }
	public function getPhone() { return $this->phone; }
	public function getDeleteFlag() { return $this->deleteflag; }
	public function getTerminationDate() { return $this->terminationDate; }
	
	public function setKey($val) { $this->orgKey = $val; }
	public function setName($val) { $this->orgName = $val; }
	public function setDateFounded($val) { $this->orgDateFounded = $val; }
	public function setDescription($val) { $this->orgDescription = $val; }
	public function setNumEmployees($val) { $this->orgNumEmployees = $val; }
	public function setCompanyRevenue($val) { $this->orgCompanyRevenue = $val; }
	public function setGovernmentRevenue($val) { $this->orgGovRevenue = $val; }
	public function setFiscalYearEnd($val) { $this->orgFiscalYearEnd = $val; }
	public function setType($val) { $this->orgType = $val; }
	public function setOrgRoster($val) { $this->orgRoster = $val; }
	public function setContact($val) { $this->contactKey = $val; }
	public function setReferral($val) { $this->referral = $val; }
	public function setComFedStateLocal($val) { $this->comFedStateLocal = $val; }
	public function setAddress($val) { $this->address = $val; }	
	public function setPhone($val) { $this->phone = $val; }
	public function setDeleteFlag($val) {if ($val == 1) {$this->deleteflag = true;} else {$this->deleteflag = false;}}
	public function setTerminationDate($val) { $this->terminationDate = $val; }		
		
}

?>
