<?php

/*TBE**************************************************************************/

class CommitteePositions {
	
	private $Key;
	private $Code;  //cmt_name
	
	public function __construct($Key,  $Code) {
		$this->Key = $Key;
		$this->Code = $Code;
	}	
	public function setKey($val) { $this->Key = $val; }
	public function setCode($val) { $this->Code = $val; }
	

	public function getKey() { return $this->Key; }
	public function getCode() { return $this->Code; }
	
}




?>