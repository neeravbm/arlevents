<?php
/*TBE**************************************************************************/class Payment {	//require_once "CommitteePositions.php";	private $pin_cc_cardholder_name; // = 'John Tester';	private $pin_cc_number; // = '4111111111111111';	private $pin_cc_security_code; // = '123';	private $pin_cc_expire; // = '2013/12';	private $pin_apm_key; // = $apm_key;	private $pin_check_amount; // = 0.00;	private $pin_check_amountSpecified = 'true';
	public function __construct($pin_cc_cardholder_name, $pin_cc_number, $pin_cc_security_code, $pin_cc_expire, $pin_apm_key,$pin_check_amount) { 		$this->pin_cc_cardholder_name = $pin_cc_cardholder_name; 		$this->pin_cc_number = $pin_cc_number; 		$this->pin_cc_security_code = $pin_cc_security_code;		$this->pin_cc_expire = $pin_cc_expire; 		$this->pin_apm_key = $pin_apm_key; 		$this->pin_check_amount = $pin_check_amount; 	}
	public function getpin_cc_cardholder_name() { return $this->pin_cc_cardholder_name; }	public function getpin_cc_number() { return $this->pin_cc_number; }	public function getpin_cc_security_code() { return $this->pin_cc_security_code; }	public function getpin_cc_expire() { return $this->pin_cc_expire; }	public function getpin_apm_key() { return $this->pin_apm_key; }	public function getpin_check_amount() { return $this->pin_check_amount; }	public function getpin_check_amountSpecified() { return $this->pin_check_amountSpecified; }	
}




?>