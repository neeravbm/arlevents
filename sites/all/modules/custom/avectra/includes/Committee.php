<?php
/*TBE**************************************************************************/class Committee {	//require_once "CommitteePositions.php";
	private $Key;	private $Name;  //cmt_name	private $ASNCode;	private $Code;	private $CTPCode;	private $BeginDate;	private $EndDate;	private $Description;	private $is_deleted;
	public function __construct($Key,  $Name, $ASNCode, $Code, $CTPCode, $BeginDate, $EndDate, $Description, $is_deleted) {		$this->Key = $Key;		$this->Name = $Name;		 $this->ASNCode = $ASNCode;		$this->Code = $Code; 		$this->CTPCode = $CTPCode;		$this->BeginDate = $BeginDate;		$this->EndDate = $EndDate; 		$this->Description = $Description;		$this->is_deleted = $is_deleted;		$this->is_closed = $is_deleted;			}	
	public function setKey($val) { $this->Key = $val; }	public function setName($val) { $this->Name = $val; }	public function setASNCode($val) { $this->ASNCode = $val; }	public function setCode($val) { $this->Code = $val; }	public function setCTPCode($val) { $this->CTPCode = $val; }	public function setBeginDate($val) { $this->BeginDate = $val; }	public function setEndDate($val) { $this->EndDate = $val; }	public function setDescription($val) { $this->Description = $val; }	public function setIsDeleted($val) { $this->is_deleted = $val; }	public function setIsClosed($val) { $this->is_closed = $val; }
	public function getKey() { return $this->Key; }	public function getName() { return $this->Name; }	public function getASNCode() { return $this->ASNCode; }	public function getCode() { return $this->Code; }	public function getCTPCode() { return $this->CTPCode; }	public function getBeginDate() { return $this->BeginDate; }	public function getEndDate() { return $this->EndDate; }	public function getDescription() { return $this->Description; }		public function getIsDeleted() { return $this->is_deleted; }		public function getIsClosed() { return $this->is_closed; }	
}




?>