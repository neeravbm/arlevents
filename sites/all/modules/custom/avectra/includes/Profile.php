<?php



class Profile {
	
	private $custKey;
	private $indToken;
	private $custNum;
	private $custType;
	private $firstName;
	private $lastName;
	private $title;
	private $organization;
	private $orgkey;
	private $addr1;
	private $addr2;
	private $city;
	private $state;
	private $zip;
	private $country;
	private $phone;
	private $email;
	private $fax;
	
	
	public function getCustomerKey() { return $this->custKey; }
	public function getIndividualToken() { return $this->indToken; }
	public function getCustomerNumber() { return $this->custNum; }
	public function getCustomerType() { return $this->custType; }
	public function getFirstName() { return $this->firstName; }
	public function getLastName() { return $this->lastName; }
	public function getTitle() { return $this->title; }
	public function getOrganization() { return $this->organization; }
	public function getOrganizationKey() { return $this->orgkey; }
	public function getAddress1() { return $this->addr1; }
	public function getAddress2() { return $this->addr2; }
	public function getCity() { return $this->city; }
	public function getState() { return $this->state; }
	public function getZip() { return $this->zip; }
	public function getCountry() { return $this->country; }
	public function getPhone() { return $this->phone; }
	public function getEmail() { return $this->email; }
	public function getFax() { return $this->fax; }
	
	public function setCustomerKey($val) { $this->custKey = $val; }
	public function setIndividualToken($val) { $this->indToken = $val; }
	public function setCustomerNumber($val) { $this->custNum = $val; }
	
	public function setCustomerType($code, $benefits) {
		if ($code=='null') $code="";
		if ($benefits=='null' || $benefits=="") $benefits = '0';
		$retVal = "Unknown User Type";
		if ($code=="ACT") {
			$retVal = "Government";
		}
		elseif ($benefits=="1") {
			$retVal = "Industry";
		}
		else {
			$retVal = "NonMember";
		}
	
		$this->custType  = $retVal;
	}
	
	public function setFirstName($val) { $this->firstName = $val; }
	public function setLastName($val) { $this->lastName = $val; }
	public function setTitle($val) { $this->title = $val; }
	public function setOrganization($val) { $this->organization = $val; }
	public function setOrganizationKey($val) { $this->orgkey = $val; }
	public function setAddress1($val) { $this->addr1 = $val; }
	public function setAddress2($val) { $this->addr2 = $val; }
	public function setCity($val) { $this->city = $val; }
	public function setState($val) { $this->state = $val; }
	public function setZip($val) { $this->zip = $val; }
	public function setCountry($val) { $this->country = $val; }
	public function setPhone($val) { $this->phone = $val; }
	public function setEmail($val) { $this->email = $val; }	
	public function setFax($val) { $this->fax = $val; }
		
}




?>