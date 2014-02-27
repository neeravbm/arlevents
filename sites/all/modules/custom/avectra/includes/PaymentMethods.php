<?php
/*TBE**************************************************************************/class PaymentMethods {	//require_once "PaymentMethods.php";
	private $Key;	private $Type; 	private $Method;	
	public function __construct($Key,  $Type, $Method) {		$this->Key = $Key;		$this->Type = $Type;		$this->Method = $Method;									}	
	public function setKey($val) { $this->Key = $val; }	public function setType($val) { $this->Type = $val; }	public function setMethod($val) { $this->Method = $val; }
	public function getKey() { return $this->Key; }	public function getType() { return $this->Type; }	public function getMethod() { return $this->Method; }
}




?>