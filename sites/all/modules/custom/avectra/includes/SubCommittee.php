<?php

/*TBE**************************************************************************/

class SubCommittee {
	
	private $Key;
	private $Code;  //cmt_name
	private $Name;
	private $CTPCode;
	private $BeginDate;
	private $EndDate;
	private $Description;
	
	public function __construct($Key,  $Code, $Name, $CTPCode, $BeginDate, $EndDate, $Description) {
		$this->Key = $Key;
		$this->Code = $Code;
		$this->Name = $Name; 
		$this->CTPCode = $CTPCode;
		$this->BeginDate = $BeginDate;
		$this->EndDate = $EndDate; 
		$this->Description = $Description;
	}	
	public function setKey($val) { $this->Key = $val; }
	public function setCode($val) { $this->Code = $val; }
	public function setName($val) { $this->Name = $val; }
	public function setCTPCode($val) { $this->CTPCode = $val; }
	public function setBeginDate($val) { $this->BeginDate = $val; }
	public function setEndDate($val) { $this->EndDate = $val; }
	public function setDescription($val) { $this->Description = $val; }

	public function getKey() { return $this->Key; }
	public function getCode() { return $this->Code; }
	public function getName() { return $this->Name; }
	public function getCTPCode() { return $this->CTPCode; }
	public function getBeginDate() { return $this->BeginDate; }
	public function getEndDate() { return $this->EndDate; }
	public function getDescription() { return $this->Description; }	
	
}




?>