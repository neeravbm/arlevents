<?php



Class Invoice {
	
	private $invoiceKey;
	private $invoiceNum;
	private $eventName;
	private $total;
	private $date;
	private $code;
	private $balance;
	private $custType;
	
	public function __construct($key, $num, $name, $total, $date, $code, $balance, $custType) {
		$this->invoiceKey = $key;
		$this->invoiceNum = $num;
		$this->eventName = $name;
		$this->total = $total;
		$this->date = $date;
		$this->code = $code;
		$this->balance = $balance;
		$this->custType = $custType;
	}
	
	public function setInvoiceKey($val) { $this->invoiceKey = $val; }
	public function setInvoiceNum($val) { $this->invoiceNum = $val; }
	public function setEventName($val) { $this->eventName = $val; }
	public function setTotal($val) { $this->total = $val; }
	public function setDate($val) {	$this->date = $val;	}
	public function setCode($val) { $this->code = $val; }
	public function setBalance($val) { $this->balance = $val; }
	public function setCustType($val) { $this->custType = $custType; }
	
	public function getInvoiceKey() { return $this->invoiceKey; }
	public function getInvoiveNum() { return $this->invoiceNum; }
	public function getEventName() { return $this->eventName; }
	public function getTotal() { return $this->total; }
	public function getDate() { return $this->date; }
	public function getCode() { return $this->code; }
	public function getBalance() { return $this->balance; }
	public function getCustType() { return $this->custType; }
}

Class Event {
	private $eventKey;
	private $eventName;
	private $eventCode;
	private $description;
	private $regType;
	private $regDate;
	private $startdate;
	private $enddate;
	private $starttime;
	private $endtime;
	private $location;
	private $fee;
	private $prc_key;
	private $postToWeb;
	private $removeFromWeb;
	private $sessionArray = Array();
	private $trackArray = Array();
	
	public function __construct($key, $code, $name, $desc, $startdate, $enddate, $starttime='', $endtime='', $postToWeb='', $removeFromWeb='', $regType='', $regDate='', $location='', $fee='') {
		$this->eventKey = $key;
		$this->eventName = $name;
		$this->eventCode = $code;
		$this->description = $desc;
		$this->startdate = $startdate;
		$this->enddate = $enddate;
		$this->starttime = $starttime;
		$this->endtime = $endtime;
		$this->regType = $regType;
		$this->regDate = $regDate;
		$this->location = $location;
		$this->fee = $fee;
		$this->postToWeb = $postToWeb;
		$this->removeFromWeb = $removeFromWeb;
	}

	public function setLocation($val) { $this->location = $val; }
	public function setFee($val) { $this->fee = $val; }
	public function set_prc_key($val) { $this->prc_key = $val; }
	public function setSessionArray($val) { $this->sessionArray = $val; }
	public function setTrackArray($val) { $this->trackArray = $val; }
	public function addtoSessionArray($val) { array_push($this->sessionArray, $val); }
	public function addtoTrackArray($val) { array_push($this->trackArray, $val); }
		
	public function getEventKey() {	return $this->eventKey;	}
	public function getEventName() { return $this->eventName; }
	public function getEventCode() { return $this->eventCode; }
	public function getDescription() { return $this->description; }	
	public function getRegType() { return $this->regType; }
	public function getRegDate() { return $this->regDate; }
	public function getStartDate() { return $this->startdate; }	
	public function getEndDate() { return $this->enddate; }	
	public function getStartTime() { return $this->starttime; }
	public function getEndTime() { return $this->endtime; }
	public function getLocation() {	return $this->location;	}
	public function getFee() { return $this->fee; }
	public function get_prc_key() { return $this->prc_key; }
	public function getPostToWeb() { return $this->postToWeb; }
	public function getRemoveFromWeb() { return $this->removeFromWeb; }	
	public function getSessionArray() { return $this->sessionArray; }
	public function getTrackArray() { return $this->trackArray; }
}

Class Session {
	private $sessionKey;
	private $sessionTitle;
	private $capacity;
	private $startdate;
	private $enddate;
	private $fee;
	private $prc_key;

	public function __construct($key, $title, $capacity, $startdate, $enddate, $fee='', $prc_key='') {
		$this->sessionKey = $key;
		$this->sessionTitle = $title;
		$this->capacity = $capacity;
		$this->startdate = $startdate;
		$this->enddate = $enddate;
		$this->fee = $fee;
		$this->prc_key = $prc_key;
	}

	public function getSessionKey() {	return $this->sessionKey; }
	public function getSessionTitle() {	return $this->sessionTitle;	}
	public function getSessionCapacity() { return $this->capacity;	}
	public function getStartDate() { return $this->startdate; }
	public function getEndDate() { return $this->enddate; }
	public function getFee() { return $this->fee; }
	public function get_prc_key() { return $this->prc_key; }
}

Class Track {
	private $trackKey;
	private $trackTitle;
	private $description;
	private $fee;
	private $prc_key;
	
	public function __construct($key, $title, $desc, $fee='', $prc_key='') {
		$this->trackKey = $key;
		$this->trackTitle = $title;
		$this->description = $desc;
		$this->fee = $fee;
		$this->prc_key = $prc_key;
	}
	
	public function getTrackKey() { return $this->trackKey; }
	public function getTrackTitle() { return $this->trackTitle; }	
	public function getTrackDescription() { return $this->description; }
	public function getTrackFee() { return $this->fee; }
	public function get_prc_key() { return $this->prc_key; }
}

Class Membership {
	private $key;
	private $association;
	private $type;
	private $status;
	private $effective;
	private $expires;
	private $joined;
	private $terminate;
	
	public function __construct($key, $assoc, $type, $effective, $expires, $joined, $terminate) {
		$this->key = $key;
		$this->association = $assoc;
		$this->type = $type;
		$this->effective = $effective;
		$this->expires = $expires;
		$this->joined = $joined;
		$this->terminate = $terminate;
	}
	
	public function getKey() { return $this->key; }
	public function getAssociation() { return $this->association; }
	public function getType() { return $this->type; }
	public function getStatus() { return $this->status; }
	public function getEffective() { return $this->effective; }
	public function getExpires() { return $this->expires; }
	public function getJoined() { return $this->joined; }
	public function getTerminate() { return $this->terminate; }
}

Class Chapter {
	private $key;
	private $type;
	private $status;
	private $joined;
	private $expires;
	private $terminates;
	
	public function __construct($key, $type, $status, $joined, $expires, $terminates) {
		$this->key = $key;
		$this->type = $type;
		$this->status = $status;
		$this->joined = $joined;
		$this->expires = $expires;
		$this->terminates = $terminates;
	}
	
	public function getKey() { return $key; }
	public function getType() { return $type; }
	public function getStatus() { return $status; }
	public function getJoined() { return $joined; }
	public function getExpires() { return $expires; }
	public function getTerminates() { return $terminates; }	
}

Class Download {
	private $key;
	private $name;
	private $code;
	private $price;
	private $date;
	private $downloadURL;
	private $daysLeft;

	public function __construct($key, $name, $code, $price, $date, $url, $daysleft) {
		$this->key = $key;
		$this->name = $name;
		$this->code = $code;
		$this->price = $price;
		$this->date = $date;
		$this->downloadURL = $url;
		$this->daysLift = $daysleft;
	}

	public function getKey() { return $key;	}
	public function getName() { return $name; }
	public function getCode() { return $code; }
	public function getPrice() { return $price;	}
	public function getDate() {	return $date; }
	public function getDownloadURL() { return $downloadURL; }
	public function getDaysLeft() { return $daysLeft; }	
}


?>