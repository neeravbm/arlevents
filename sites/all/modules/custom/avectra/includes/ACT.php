<?php
require_once "NFComm.php";
require_once "Profile.php";
require_once "Organization.php";
require_once "Transactions.php";
require_once "Committee.php";
require_once "SubCommittee.php";
require_once "CommitteePositions.php";
require_once "PaymentMethods.php";

define ("EMPTYGUID", "00000000-0000-0000-0000-000000000000");








class ACT {
	private $comm;


	private $customerKey;


	private $ssoToken;


	


	private $debug = false;


	


	


	/******************************************************************/


	public function __construct($soapURL, $xwebUser, $xwebPass) {


		set_time_limit(120);


		


		try {


			$this->comm = new NFComm($soapURL, $xwebUser, $xwebPass);


		} catch(Exception $e) {


			$this->comm = null;


			$this->printDebug($e);


			return;


		}


	}





	/******************************************************************/


	private function printDebug($exception) {


		if ($this->debug == true) {


			echo "<pre>";


			print "Exception contents:\n";


			print_r($exception);


			echo "</pre>";


		}


	}


	


	/******************************************************************/


	public function setDebug($debug) {


		if ($debug == true) {


			$this->debug = true;


		} else {


			$this->debug = false;


		}


		


		if ($this->comm != null) {


			$this->comm->setDebug($this->debug);


		}


	}


	


	/******************************************************************/


	// Given an eWeb username and plain-text password, validates it against netFORUM and returns a Customer Key.  $token is filled with the SSO token.


	//  Returns false on failure - usually due to a bad username or password


	public function loginUser($user, $pass, &$token) {


		


		if ($this->comm == null) {


			return false;


		}


		


		try {


			$response = $this->comm->webLogin($user, $pass);


		


			// Get the Customer Key


			$key = $response->WEBWebUserLoginResult->CurrentKey;


		} catch(Exception $e) {


			$this->printDebug($e);


			return false;


		}


		


		// Check for valid login


		if ($key == "00000000-0000-0000-0000-000000000000") {


			return false;


		}


		


		$token = $response->WEBWebUserLoginResult->Individual->ind_token;


		$this->ssoToken = $token;


		$this->customerKey = $key;


		


		return $key;


	}


	


	/******************************************************************/


	// Returns the Cart object for the currently logged-in user


	public function getCart() {


		if ($this->comm == null)


			return false;


		


		return $this->cart;


	}


	


	/******************************************************************/


	// Fills the Profile object with the user's information


	public function getProfileInfo($key) {


		


		if ($this->comm == null)


			return false;


		


		$profile = new Profile();


		


		try {


			$response = $this->comm->getProfileInfo($key);


			$res = $response->WEBIndividualGetResult;


			


			//var_dump($res);


			$profile->setCustomerKey($key);


			if (isset($res->Individual->ind_first_name)) { $profile->setFirstName($res->Individual->ind_first_name); }


			if (isset($res->Individual->ind_last_name)) { $profile->setLastName($res->Individual->ind_last_name); }


			


			$ind_int_code = "";


			if (isset($res->Individual->ind_int_code))  


			{


				$ind_int_code = $res->Individual->ind_int_code;


			}


			


			$cst_receives_member_benefits_flag = "0";


			if (isset($res->Customer->cst_receives_member_benefits_flag))


			{


				$cst_receives_member_benefits_flag = $res->Customer->cst_receives_member_benefits_flag;


			}


			$profile->setCustomerType($ind_int_code, $cst_receives_member_benefits_flag);


			


			if (isset($res->Customer->cst_recno)) { $profile->setCustomerNumber($res->Customer->cst_recno); }


			if (isset($res->Organization->org_name)) { $profile->setOrganization($res->Organization->org_name); }


			if (isset($res->Organization->org_cst_key)) { $profile->setOrganizationKey($res->Organization->org_cst_key); }


			if (isset($res->Customer->cst_ixo_title_dn)) { $profile->setTitle($res->Customer->cst_ixo_title_dn); }


			if (isset($res->Email->eml_address)) { $profile->setEmail($res->Email->eml_address); }


			if (isset($res->Business_Address->adr_line1)) { $profile->setAddress1($res->Business_Address->adr_line1); }


			if (isset($res->Business_Address->adr_line2)) { $profile->setAddress2($res->Business_Address->adr_line2); }			


			if (isset($res->Business_Address->adr_city)) { $profile->setCity($res->Business_Address->adr_city); }


			if (isset($res->Business_Address->adr_state)) { $profile->setState($res->Business_Address->adr_state); }


			if (isset($res->Business_Address->adr_post_code)) { $profile->setZip($res->Business_Address->adr_post_code); }


			if (isset($res->Business_Address->adr_country)) { $profile->setCountry($res->Business_Address->adr_country); }


			if (isset($res->Business_Phone->phn_number)) { $profile->setPhone($res->Business_Phone->phn_number); }


			if (isset($res->Business_Fax->fax_number)) { $profile->setFax($res->Business_Fax->fax_number); }


		} catch(Exception $e) {


			$this->printDebug($e);


			return false;


		}


		


		return $profile;


	}


	


	/******************************************************************/


	// Retrieves a customer key based on email address


	public function getCustomerKeyFromEmail($email) {


		if (($this->comm == null) Or ($email == ''))


			return false;


		


		$response = $this->comm->GetKeyFromEmail($email);


	


		// Load the XML into an object for easier access


		$response2 = @simplexml_load_string($response);





		return $response2->Result->cst_key;	


	}


	


	/******************************************************************/


	// Inserts a new user with the given Profile object info and returns the new customer key - returns false on error


	public function insertUser($profile) {


	


		// Do not allow a user to be created without an email address


		if ($profile->getEmail() == '') {


			return false;


		}


		


		$ind = Array('ind_prf_code' => '',


				'ind_sfx_code' => '',


				'ind_first_name' => $profile->getFirstName(),


				'ind_last_name' => $profile->getLastName(),


				'ind_designation' => '');


		$cust = Array('cst_ixo_title_dn' => $profile->getTitle(),


				'cst_org_name_dn' => $profile->getOrganization(),


				'cst_phn_number_complete_dn' => $profile->getPhone());


		$email = Array('eml_address' => $profile->getEmail(),


				'eml_primary' => '1');


		$ba = Array('adr_line1' => $profile->getAddress1(),


				'adr_line2' => $profile->getAddress2(),


				'adr_city' => $profile->getCity(),


				'adr_state' => $profile->getState(),


				'adr_post_code' => $profile->getZip(),


				'adr_country' => $profile->getCountry());


		$ph = Array('phn_number' => $profile->getPhone());


		$fax = Array('fax_number' => $profile->getFax());


		$oFacadeObject = array ('Individual' => $ind,


				'Customer' => $cust,


				'Address_Change_Log' => null,


				'TransferToCustomer' => null,


				'Organization_XRef' => null,


				'Organization' => null,


				'Email' => $email,


				'Website' => null,


				'Messaging' => null,


				'Business_Address' => $ba,


				'Business_Address_XRef' => null,


				'Business_Address_State' => null,


				'Business_Address_Country' => null,


				'Business_Phone' => $ph,


				'Business_Phone_Country' => null,


				'Business_Phone_XRef' => null,


				'Business_Fax' => $fax,


				'Business_Fax_Country' => null,


				'Business_Fax_XRef' => null,


				'Home_Address' => null,


				'Home_Address_Country' => null,


				'Home_Address_State' => null,


				'Home_Address_XRef' => null,


				'Home_Phone' => null,


				'Home_Phone_Country' => null,


				'Home_Phone_XRef' => null,


				'Home_Fax' => null,


				'Home_Fax_Country' => null,


				'Home_Fax_XRef' => null,


				'Individual_Custom_Demographics' => null,


				'Social_Links' => null,


				'source_code' => null);


				


		$response = $this->comm->insertUser($oFacadeObject);





		return $response->WEBIndividualInsertResult->CurrentKey;


	}


	


	/******************************************************************/


	// Returns a list of Government users


	public function getGovernmentUsers() {


		if ($this->comm == null)


			return false;


	


		$where = 'ind_customer_type_ext = \'Government\'';


		$response = $this->comm->GetQuery('Individual @TOP -1', 'ind_cst_key', $where, '');


			


		// Load the XML into an object for easier access


		$xml = $response->GetQueryResult->any;


		$response2 = @simplexml_load_string($xml);





		return $response2;			


	}


	


	/******************************************************************/


	// Returns a list of Industry users


	public function getIndustryUsers() {


		if ($this->comm == null)


			return false;


	


		$where = 'ind_customer_type_ext = \'Industry\'';


		$response = $this->comm->GetQuery('Individual @TOP -1', 'ind_cst_key', $where, '');


			


		// Load the XML into an object for easier access


		$xml = $response->GetQueryResult->any;


		$response2 = @simplexml_load_string($xml);





		return $response2;			


	}


	


	/****************getmem**************************************************/


	public function getCompanies() {


		if ($this->comm == null)


			return false;


		


		$where = 'org_ogt_code LIKE \'Industry%\'';





		$response = $this->comm->getQuery('Organization @TOP -1', 'org_name, org_cst_key, org_delete_flag, mbr_terminate_date', $where, '');


			


		// Load the XML into an object for easier access


		$xml = $response->GetQueryResult->any;


		$response2 = @simplexml_load_string($xml);





		return $response2;


	}


	


	/******************************************************************/


	public function getAgencies() {


		if ($this->comm == null)


			return false;


		


		$where = 'org_ogt_code LIKE \'Government%\'';





		$response = $this->comm->getQuery('Organization @TOP -1', 'org_name, org_cst_key', $where, '');


			


		// Load the XML into an object for easier access


		$xml = $response->GetQueryResult->any;


		$response2 = @simplexml_load_string($xml);





		return $response2;


	}


	


	/******************************************************************/


	// Returns a list of customer keys for users created since the given date (in the format 'YYYY-mm-dd HH:MM') - if no date is passed, defaults to yesterday


	public function getUsersCreatedSince($createdSince='') {


		if ($this->comm == null)


			return false;





		try {


			$where = 'ind_add_date > \'';


			


			// If no criteria is passed-in, default to yesterday


			if ($createdSince == '') {


				$date = new DateTime();


				$date->add(DateInterval::createFromDateString('yesterday'));


				$createdSince = $date->format('Y-m-d') . '\' AND cst_eml_address_dn is not null';


			}


			


//			$where .= $createdSince . '\'';


			$where .= $createdSince . '\' AND cst_eml_address_dn is not null';


				


			$response = $this->comm->GetQuery('Individual @TOP -1', 'ind_cst_key', $where, '');


			


			// Load the XML into an object for easier access


			$xml = $response->GetQueryResult->any;


			$response2 = @simplexml_load_string($xml);


		


			return $response2;


		} catch(Exception $e) {


			$this->printDebug($e);


			return false;


		}


	}


	


	/******************************************************************/


	// Returns a list of customer keys for users created since the given date (in the format 'YYYY-mm-dd HH:MM') - if no date is passed, defaults to yesterday


	public function getUsersCreatedBetween($createdFrom='', $createdTo='') {


		if ($this->comm == null)


			return false;


	


		try {


			// If no criteria is passed-in, default to all users


			if (($createdFrom == '') or ($createdTo == '')) {


				$where = 'ind_add_date > \'1963-01-01\' AND cst_eml_address_dn is not null';


			}


			else {


				$where = '(ind_add_date > \'';


				


//				$where .= $createdFrom . '\') AND (ind_add_date <= \'' . $createdTo . '\')';


				$where .= $createdFrom . '\') AND (ind_add_date <= \'' . $createdTo . '\')  AND cst_eml_address_dn is not null';


			}


			


			$response = $this->comm->GetQuery('Individual @TOP -1', 'ind_cst_key', $where, '');


				


			// Load the XML into an object for easier access


			$xml = $response->GetQueryResult->any;


			$response2 = @simplexml_load_string($xml);


	


			return $response2;


		} catch(Exception $e) {


			$this->printDebug($e);


			return false;


		}


	}


	


	/******************************************************************/


	// Takes a Profile object as the parameter, updates the netFORUM information to match, and return true on success, false otherwise


	public function updateUserInfo($profile) {


		if ($this->comm == null)


			return false;





		try {


			// Start by retrieving the current user information


			$response = $this->comm->getProfileInfo($profile->getCustomerKey());


	


			$user = $response->WEBIndividualGetResult;


			


			// Do not allow the email, firstname or lastname to be cleared


			if (($profile->getEmail() == '') or ($profile->getFirstName() == '') or ($profile->getLastName() == ''))


				return false;


		


			$user->Individual->ind_first_name = $profile->getFirstName();


			$user->Individual->ind_last_name = $profile->getLastName();


			$user->Customer->cst_ixo_title_dn = $profile->getTitle();


			$user->Email->eml_address = $profile->getEmail();
			$user->Customer->cst_eml_address_dn = $profile->getEmail();


			$user->Email->eml_primary = 1;


			$user->Business_Address->adr_line1 = $profile->getAddress1();


			$user->Business_Address->adr_line2 = $profile->getAddress2();


			$user->Business_Address->adr_city = $profile->getCity();


			$user->Business_Address->adr_state = $profile->getState();


			$user->Business_Address->adr_post_code = $profile->getZip();


			$user->Business_Address->adr_country = $profile->getCountry();


			$user->Business_Phone->phn_number = $profile->getPhone();


			$user->Business_Fax->fax_number = $profile->getFax();


			


			$response = $this->comm->updateUserInfo($user);


			if ($response == null) {


				return false;


			} else {			


				return $response->WEBIndividualUpdateResult;


			}


		} catch(Exception $e) {


			$this->printDebug($e);


			return false;


		}


				


	}





	/******************************************************************/


	// Updates the organization with the given object info.


	public function updateOrganization($org) {		


		


		


		// First get the full Organization object	


		$oorg = $this->comm->getOrganizationInfo($org->getKey());


		$oorg = $oorg->WEBOrganizationGetResult;


				


		// Update the members that may have changed


		$oorg->Organization->org_name = $org->getName();


		$oorg->Organization->org_date_founded = $org->getDateFounded();


		$oorg->Organization->org_description_ext = $org->getDescription();


		$oorg->Organization->org_num_of_employees_ext = $org->getNumEmployees();


		$oorg->Organization->org_total_annual_co_revenue_ext = $org->getCompanyRevenue();


		$oorg->Organization->org_annual_govt_revenue_ext = $org->getGovernmentRevenue();


		$oorg->Organization->org_fiscal_year_end_ext = $org->getFiscalYearEnd();


		$oorg->Organization->org_ogt_code = $org->getType();


		$oorg->Organization->org_firstlearn_ext = $org->getReferral();


		$oorg->Organization->org_com_fed_st_lcl_ext = $org->getComFedStateLocal();


		$oorg->Primary_Contact->con__cst_key = $org->getContact();


		$oorg->Address_XRef_1->cxa_mailing_label_html = $org->getAddress();


		$oorg->Address_XRef_1->cxa_mailing_label = str_replace('<br>', ' ', $org->getAddress());


		$oorg->Phone->phn_number_display = $org->getPhone();


		$oorg->Phone->phn_number = $org->getPhone();		


		


		$response = $this->comm->updateOrganization($oorg);


		return $response->WEBOrganizationUpdateResult;


	}


	


	/******************************************************************/


	// Inserts a new organization with the given object info and returns the new organization key


	public function insertOrganization($org) {


	


		$o = Array('org_name' => $org->getName(),


				'org_ogt_code' => $org->getType(),


				'org_num_employee' => $org->getNumEmployees(),


				'org_date_founded' => $org->getDateFounded());





		$oFacadeObject = array ('Organization' => $o,


				'Customer' => null,


				'Parent_Customer' => null,


				'Address_Change_Log' => null,


				'Email' => null,


				'Website' => null,


				'Messaging' => null,


				'Phone' => null,


				'Phone_Country' => null,


				'Phone_XRef' => null,


				'Fax' => null,


				'Fax_Country' => null,


				'Fax_XRef' => null,


				'Address_1' => null,


				'Address_XRef_1' => null,


				'Billing_Address_1' => null,


				'Address_1_State' => null,


				'Address_1_Country' => null,


				'Billing_Address_1_State' => null,


				'Billing_Address_1_Country' => null,


				'Billing_Address_XRef_1' => null,


				'Customer_X_Customer' => null,


				'Primary_Contact' => null,


				'Social_Links' => null,


				'Organization_Custom_Demographics' => null,


				'source_code' => null);


	


		$response = $this->comm->insertOrganization($oFacadeObject);


		return $response->WEBOrganizationInsertResult->CurrentKey;


	}


	


	/******************************************************************/


	public function getOrganizationInfo($key) {





		if ($this->comm == null)


			return false;


				


		try {


			$org = new Organization();


			


			$response = $this->comm->getOrganizationInfo($key);


//var_dump($response);


			$org->setKey($key);


			$org->setName($response->WEBOrganizationGetResult->Organization->org_name);


			if (isset($response->WEBOrganizationGetResult->Organization->org_date_founded)) { $org->setDateFounded($response->WEBOrganizationGetResult->Organization->org_date_founded); }


			if (isset($response->WEBOrganizationGetResult->Organization->org_description_ext)) { $org->setDescription($response->WEBOrganizationGetResult->Organization->org_description_ext); }


			if (isset($response->WEBOrganizationGetResult->Organization->org_num_of_employees_ext)) { $org->setNumEmployees($response->WEBOrganizationGetResult->Organization->org_num_of_employees_ext); }


			if (isset($response->WEBOrganizationGetResult->Organization->org_total_annual_co_revenue_ext)) { $org->setCompanyRevenue($response->WEBOrganizationGetResult->Organization->org_total_annual_co_revenue_ext); }


			if (isset($response->WEBOrganizationGetResult->Organization->org_annual_govt_revenue_ext)) { $org->setGovernmentRevenue($response->WEBOrganizationGetResult->Organization->org_annual_govt_revenue_ext); }


			if (isset($response->WEBOrganizationGetResult->Organization->org_fiscal_year_end_ext)) { $org->setFiscalYearEnd($response->WEBOrganizationGetResult->Organization->org_fiscal_year_end_ext); }


			if (isset($response->WEBOrganizationGetResult->Organization->org_ogt_code)) { $org->setType($response->WEBOrganizationGetResult->Organization->org_ogt_code); }


			if (isset($response->WEBOrganizationGetResult->Primary_Contact->con__cst_key)) { $org->setContact($this->getPOCMembersForOrganization($key)); }


			if (isset($response->WEBOrganizationGetResult->Organization->org_firstlearn_ext)) { $org->setReferral($response->WEBOrganizationGetResult->Organization->org_firstlearn_ext); }


			if (isset($response->WEBOrganizationGetResult->Organization->org_com_fed_st_lcl_ext)) { $org->setComFedStateLocal($response->WEBOrganizationGetResult->Organization->org_com_fed_st_lcl_ext); }


			if (isset($response->WEBOrganizationGetResult->Address_XRef_1->cxa_mailing_label_html)) { $org->setAddress($response->WEBOrganizationGetResult->Address_XRef_1->cxa_mailing_label_html); }


			if (isset($response->WEBOrganizationGetResult->Phone->phn_number_display)) { $org->setPhone($response->WEBOrganizationGetResult->Phone->phn_number_display);


			if (isset($response->WEBOrganizationGetResult->Organization->org_delete_flag)) { $org->setDeleteFlag($response->WEBOrganizationGetResult->Organization->org_delete_flag); }

$terminationDate = "";
			$where = "org_cst_key = '".$key."'";
			//var_dump($where);
			$qResponse = $this->comm->GetQuery('Organization', ', mbr_terminate_date', $where, '');
			$xml = $qResponse->GetQueryResult->any;
			$xResponse = @simplexml_load_string($xml);
			foreach ($xResponse->OrganizationObject as $obj) {
				$terminationDate = $obj->mbr_terminate_date;
				}

			if (isset($terminationDate)) { $org->setTerminationDate($terminationDate); }


			}





				


			$org->setOrgRoster($this->getOrganizationMembers($key));


		


			return $org;


		} catch(Exception $e) {


			$this->printDebug($e);


			return false;


		}


		


	}


	


	/******************************************************************/


	// Returns an array of Member objects


	//  Return false on error


	public function getOrganizationMembers($orgKey) {





		if ($this->comm == null)


			return false;


		


		try {


			$members = Array();


			


			$response = $this->comm->GetQuery('Individual @TOP -1', 'ind_first_name, ind_last_name, co_email.eml_address, co_individual_x_organization.ixo_rlt_code', 'org_cst_key=\'' . $orgKey . '\' and ixo_end_date is null', '');	


			


			// Load the XML into an object for easier acces


			$xml = $response->GetQueryResult->any;


			libxml_use_internal_errors();


			$response2 = @simplexml_load_string($xml);


		


			// Parse through the array of objects


			foreach ($response2->IndividualObject as $obj) {
				
				$found = false;
				foreach($members as $obj2)
				{
					if (($obj->ind_cst_key) == ($obj2->getCustomerKey()))
					{
						$found = true;
					}
				}
				
				if (!$found)
				{

				$mem = new Member($obj->ind_cst_key, $obj->ind_first_name, $obj->ind_last_name, $obj->eml_address, $obj->ixo_rlt_code);



				array_push($members, $mem);
}

			}


			


			return $members;


		} catch(Exception $e) {


			$this->printDebug($e);


			return false;


		}


	}


	


	/******************************************************************/


	// Returns an array of Invoice objects


	//  Returns false on error

	


	public function getInvoicesForUser($custKey) {


			$retVal1 = $this->getClosedInvoicesForCustomerOrganization($custKey);
			$retVal2 = $this->getClosedInvoicesForCustomer($custKey);
			
			$retVal3 = array_merge($retVal1, $retVal2);
			return $retVal3;
			
/*

		if ($this->comm == null)


			return false;





		try {


			$invoices = Array();


			


			$response = $this->comm->GetQuery('Invoice', 'inv_key, inv_code_cp, ac_invoice_detail.ivd_amount_cp, oe_product.prd_name, inv_trx_date, inv_code, vw_ac_invoice.sum_linebalance, ind_int_code', 'inv_cst_key=\'' . $custKey . '\'', '');


	


			// Load the XML into an object for easier acces


			$xml = $response->GetQueryResult->any;


			libxml_use_internal_errors();


			$response2 = @simplexml_load_string($xml);


			


			foreach ($response2->InvoiceObject as $obj) {


				$inv = new Invoice($obj->inv_key, $obj->inv_code_cp, $obj->prd_name, $obj->ivd_amount_cp, $obj->inv_trx_date, $obj->inv_code, $obj->sum_linebalance, $obj->ind_int_code);


				array_push($invoices, $inv);


			}


			


			return $invoices;	


		} catch(Exception $e) {


			$this->printDebug($e);


			return false;


		}
*/

	}


	


	/******************************************************************/


	// Returns an array of Membership objects


	//  Returns false on error


	public function getMembershipsForUser($custKey) {


	


		if ($this->comm == null)


			return false;


		


		try {


			$memberships = Array();


	


			$response = $this->comm->getMembershipsForUser($custKey);		


			


			// Load the XML into an object for easier acces


			$xml = $response->WEBActivityGetPurchasedMembershipsByCustomerResult->any;


			$response2 = @simplexml_load_string($xml);





			foreach ($response2 as $obj) {


				$mem = new Membership($obj->mbr_key, $obj->Association, $obj->MbrType, $obj->Effective, $obj->Expire, $obj->Join, $obj->Terminate);


				array_push($memberships, $mem);


			}


	


			return $memberships;


		} catch(Exception $e) {


			return false;


		}


	}


	


	


	/******************************************************************/


	// Returns an array of Chapter objects


	//  Returns false on error


	public function getChaptersForUser($custKey) {


	


		if ($this->comm == null)


			return "0";


	


		try {


			$chapters = Array();


		


			$response = $this->comm->getChaptersForUser($custKey);


			


			// Load the XML into an object for easier acces


			$xml = $response->WEBActivityGetPurchasedChapterMembershipsByCustomerResult->any;


			$response2 = @simplexml_load_string($xml);


			


			foreach ($response2->Result as $obj) {


				$evt = new Chapter($obj->mbr_key, $obj->MbrType, $obj->MbrStatus, $obj->Join, $obj->Expire, $obj->Terminate);


				array_push($chapters, $evt);


			}


			


			return $chapters;


		} catch(Exception $e) {


			$this->printDebug($e);


			return false;


		}


	}


	


	/******************************************************************/


	// Returns an array of Download objects that the user has purchased


	//  Returns false on error


	public function getDownloadsForUser($custKey) {


	


		if ($this->comm == null)


			return false;


	


		try {


			$downloads = Array();


		


			$response = $this->comm->getDownloadsForUser($custKey);


			


			// Load the XML into an object for easier acces


			$xml = $response->WEBActivityGetPurchasedDownoadableProductsByCustomerResult->any;


			$response2 = @simplexml_load_string($xml);


		


			foreach ($response2->Result as $obj) {


				$evt = new Download($obj->ivd_key, $obj->prd_name, $obj->prd_code, $obj->ivd_price, $obj->ivd_add_date, $obj->prd_download_url, $obj->daysleft);


				array_push($downloads, $evt);


			}


			


			return $downloads;


		} catch(Exception $e) {


			$this->printDebug($e);


			return false;


		}


	}


	


	/******************************************************************/


	// Returns an array of Event objects that the user is registered for


	//  Returns false on error


	public function getEventsForUser($custKey) {


	


		if ($this->comm == null)


			return false;


	


		try {	


			$events = Array();


	


			$response = $this->comm->getEventsForUser($custKey);


	


			// Load the XML into an object for easier access


			$xml = $response->WEBActivityGetPurchasedEventsByCustomerResult->any;


			$response2 = @simplexml_load_string($xml);	





			foreach ($response2->Result as $obj) {


				$type = $obj->rgt_code;


				$date = $obj->dateinfo;


				$response = $this->comm->getEventsForUser2($obj->reg_key);


				$xml = $response->WEBActivityGetRegistrantEventsResult->any;


				$response3 =  @simplexml_load_string($xml);	


				foreach ($response3->Result as $obj2) {


					$res = $this->getEventDetails($obj2->reg_evt_key);


					$obj = $res->EventObject;





					$evt = new Event($obj->evt_key, $obj->evt_code, $obj->evt_title, $obj->evt_short_description, $obj->evt_start_date, $obj->evt_end_date, $obj->evt_start_time, $obj->evt_end_time, '', '', $type, $date, $obj->loc_name);


						


					array_push($events, $evt);


				}


			}


	


			return $events;


		} catch(Exception $e) {


			$this->printDebug($e);


			return false;


		}


	}


		


	/******************************************************************/


	// Returns an array of Event objects created in netFORUM since the date/time given - if no date is specified, returns events created in the past day


	//   Return false on error


	public function getRecentEvents($createdSince = '') {


	


		if ($this->comm == null)


			return false;


	


		try {


			$events = Array();


	


			$where = 'evt_add_date > \'';


		


			// If no criteria is passed-in, default to yesterday


			if ($createdSince == '') {


				$date = new DateTime();


				$date->add(DateInterval::createFromDateString('yesterday'));


				$createdSince = $date->format('Y-m-d');


			}


		


			$where .= $createdSince . '\'';


	


			$response = $this->comm->GetQuery('Event', 'evt_key, evt_code, evt_title, evt_short_description, evt_start_date, evt_end_date, evt_start_time, evt_end_time, evt_post_to_web_date, evt_remove_from_web_date', $where, '');


		


			// Load the XML into an object for easier access


			$xml = $response->GetQueryResult->any;


			$response2 = @simplexml_load_string($xml);


	


			foreach ($response2->EventObject as $obj) {


				$ev = new Event($obj->evt_key, $obj->evt_code, $obj->evt_title, $obj->evt_short_description, $obj->evt_start_date, $obj->evt_end_date, $obj->evt_start_time, $obj->evt_end_time, $obj->evt_post_to_web_date, $obj->evt_remove_from_web_date);


					


				$where = 'evt_key = \'' . $obj->evt_key . '\'';


				$response = $this->comm->GetQuery('EventsLocation', 'evl_location_name', $where, '');


					


				$xml = $response->GetQueryResult->any;


				$response3 = @simplexml_load_string($xml);


				$loc = $response3->EventsLocationObject->evl_location_name;


					


				$ev->setLocation($loc);


					


				array_push($events, $ev);


			}


		


			return $events;


		} catch(Exception $e) {


			$this->printDebug($e);


			return false;


		}


	}


	


	/******************************************************************/


	// Returns an array of Event objects created in netFORUM between the two dates given - if no dates are specified, returns all events


	//   Return false on error


	public function getEventsCreatedBetween($createdFrom = '', $createdTo = '') {


	


		if ($this->comm == null)


			return false;


	


		try {


			$events = Array();


	


			// If no criteria is passed-in, return all events


			if (($createdFrom == '') or ($createdTo == '')) {


				$where = 'evt_add_date > \'1963-01-01\'';


			} else {


				$where = '(evt_add_date > \'' . $createdFrom . '\') AND (evt_add_date <= \'' . $createdTo . '\')';


			}


		


			$response = $this->comm->GetQuery('Event', 'evt_key, evt_code, evt_title, evt_short_description, evt_start_date, evt_end_date, evt_start_time, evt_end_time, evt_post_to_web_date, evt_remove_from_web_date', $where, '');


		


			// Load the XML into an object for easier access


			$xml = $response->GetQueryResult->any;


			$response2 = @simplexml_load_string($xml);


	


			foreach ($response2->EventObject as $obj) {


				$ev = new Event($obj->evt_key, $obj->evt_code, $obj->evt_title, $obj->evt_short_description, $obj->evt_start_date, $obj->evt_end_date, $obj->evt_start_time, $obj->evt_end_time, $obj->evt_post_to_web_date, $obj->evt_remove_from_web_date);


					


				$where = 'evt_key = \'' . $obj->evt_key . '\'';


				$response = $this->comm->GetQuery('EventsLocation', 'evl_location_name', $where, '');


					


				$xml = $response->GetQueryResult->any;


				$response3 = @simplexml_load_string($xml);


				$loc = $response3->EventsLocationObject->evl_location_name;


					


				$ev->setLocation($loc);


					


				array_push($events, $ev);


			}


		


			return $events;


		} catch(Exception $e) {


			$this->printDebug($e);


			return false;


		}


	}


	


	/******************************************************************/


	public function getUsersForEvent($evt_key) {


		try {


			$users = Array();


	


			$where = 'reg_evt_key = \'' . $evt_key . '\'';


			


			$response = $this->comm->GetQuery('EventsRegistrant @TOP -1', 'reg_cst_key, ind_customer_type_ext, reg_evt_key, ind_first_name, ind_last_name, org_name_dn, ', $where, '');


			


			// Load the XML into an object for easier access


			$xml = $response->GetQueryResult->any;


			$response2 = @simplexml_load_string($xml);	





			return $response2;


		} catch(Exception $e) {


			$this->printDebug($e);


			return false;


		}


	}





	/******************************************************************/


	private function determineEventCode($code)


	{


		if ($code == "Government")


		{


			return "Government";


		}


		else if ($code == "Industry")


		{


			return "Industry";


		}


		else { //non-member


			return "Nonmember";


		}


	}


	


	/******************************************************************/


	public function getEventPricing($evt_key, $cust_key, $code="")


	{


		$profile = $this->getProfileInfo($cust_key);


		$eventCode = $this->determineEventCode($profile->getCustomerType());


				


		$registrant = $this->getEventRegistrant($cust_key);


		$cart = $this->getNewShoppingCart($cust_key);


		


		$RegTypes = $this->getRegistrantTypes($evt_key);


		//var_dump($RegTypes);


		foreach ($RegTypes as $obj)


		{


			//echo($obj->rgt_code.'('.$obj->rgt_key.')');


			


			//echo("<br>");


			$registrant->Registrant->reg_evt_key = $evt_key;


			$registrant->Registrant->reg_rgt_key =  $obj->rgt_key.'';


			$response = $this->comm->getEventFees($registrant, $cart);


			// Load the XML into an object for easier access


			$xml2 = $response->WEBCentralizedShoppingCartGetEventFeesResult->any;


			$response3 = @simplexml_load_string($xml2);


			//var_dump($response3);


			foreach ($response3 as $obj)


			{
				$eventCode2 = '('.$eventCode.')';
	

				if ((strpos(strtolower($obj->prc_display_name), strtolower($eventCode2))) !== false)

				{

					$arr = Array('prc_Key'=>$obj->prc_Key, 'Price'=>$obj->Price);

					return $arr;

				}

			}


			foreach ($response3 as $obj)


			{

				//echo('<Br>------------------<BR>PRC_DISPLAY_NAME:'.$obj->prc_display_name.' - '.$obj->Price.'<br>');
				$eventCode1 = $eventCode.'-Drupal';

				if ((strtolower($obj->prc_display_name) == strtolower($eventCode1)))

				{

					$arr = Array('prc_Key'=>$obj->prc_Key, 'Price'=>$obj->Price);

					return $arr;

				}

			}
			

		


		}


		return "no price found";


		}


	


	/******************************************************************/


	// Returns an array of upcoming events.  IF withFees is true then the currently logged-in user is used to determine fees, otherwise no fees are returned


	public function getEventList($withFees=true) {


		try {


			$eventArray = Array();


			


			if ($withFees == false) {


				$registrant = null;


				$cart = null;


			} else {


				// Get an Registrant object for the user


				$registrant = $this->getEventRegistrant($this->customerKey);


			}


			


			$response = $this->comm->getEventList();	


			


			// Load the XML into an object for easier access


			$xml = $response->WEBCentralizedShoppingCartGetEventListResult->any;


			$response2 = @simplexml_load_string($xml);


	


			foreach ($response2 as $obj) {


				$ev = new Event($obj->evt_key, $obj->evt_code, $obj->evt_title, $obj->evt_short_description, $obj->evt_start_date, $obj->evt_end_date, $obj->evt_start_time, $obj->evt_end_time, $obj->evt_post_to_web_date, $obj->evt_remove_from_web_date, $obj->etp_code, $obj->loc_name);


				


				if ($withFees == true) {


					$cart = $this->getNewShoppingCart($this->customerKey);


					


					// Get the fee			


					$registrant->Registrant->reg_evt_key = $obj->evt_key;


					$response = $this->comm->getEventFees($registrant, $cart);





					// Load the XML into an object for easier access


					$xml2 = $response->WEBCentralizedShoppingCartGetEventFeesResult->any;


					$response3 = @simplexml_load_string($xml2);


					//var_dump($response3);	


					$ev->setFee($response3->Result->Price);


					$ev->set_prc_key($response3->Result->prc_Key);


				}


	


				// Get the sessions for this event


				$response4 = $this->getEventSessions($obj->evt_key, $registrant, $cart);


				$ev->setSessionArray($response4);


	


				// Get the tracks for this event


				$response5 = $this->_getEventTracks($obj->evt_key, $registrant, $cart);


				$ev->setTrackArray($response5);


				


				array_push($eventArray, $ev);


			}


			


			return $eventArray;


		} catch(Exception $e) {


			$this->printDebug($e);


			return false;


		}


	}


	


	


	/******************************************************************/


	// Returns the raw data from netFORUM for the given event


	public function getEventDetails($evt_key) {


		try {


			$where = 'evt_key = \'' . $evt_key . '\'';


			


			$response = $this->comm->GetQuery('Event', '*', $where, '');


			


			// Load the XML into an object for easier access


			$xml = $response->GetQueryResult->any;


			$response2 = @simplexml_load_string($xml);


			


			return $response2;


		} catch(Exception $e) {


			$this->printDebug($e);


			return false;


		}


	}


	


	/******************************************************************/


	// Returns an array of Session objects - if $registrant or $cart is null, no fees are returned


	public function getEventSessions($evt_key, $registrant=null, $cart=null) {


		try {	


			$sessions = Array();


		


			if (($registrant == null) or ($cart == null)) {


				// If no registrant or cart, then we can't retrieve the fee info, so just return the rest of the session info


				$response = $this->comm->getEventSessions($evt_key);


	


				// Load the XML into an object for easier access


				$xml = $response->WEBCentralizedShoppingCartGetSessionListByEventKeyResult->any;


				$response2 = @simplexml_load_string($xml);


	


				foreach ($response2 as $obj) {


					$ev = new Session($obj->ses_key, $obj->ses_title, $obj->ses_capacity, $obj->ses_start_date, $obj->ses_end_date);


						


					array_push($sessions, $ev);


				}


			} else {


				$response = $this->comm->getEventSessionFees($registrant, $cart);


			


				// Load the XML into an object for easier access


				$xml = $response->WEBCentralizedShoppingCartGetEventSessionFeesResult->any;


				$response2 = @simplexml_load_string($xml);


	


				foreach ($response2 as $obj) {


					$ev = new Session($obj->ses_key, $obj->Product, $obj->Capacity, $obj->ses_starts, $obj->ses_ends, $obj->Price, $obj->prc_Key);


						


					array_push($sessions, $ev);


				}


			}


			


			return $sessions;


		} catch(Exception $e) {


			$this->printDebug($e);


			return false;


		}


	}





	/******************************************************************/


	// Returns an array of Session objects


	public function getSessionsForTrack($trk_key) {


		try {


			$sessions = Array();


			


			$response = $this->comm->getSessionsForTrack($trk_key);


			


			// Load the XML into an object for easier access


			$xml = $response->WEBCentralizedShoppingCartGetSessionListByTrackKeyResult->any;


			$response2 = @simplexml_load_string($xml);


	


			foreach ($response2 as $obj) {


				$ev = new Session($obj->ses_key, $obj->ses_title, $obj->ses_capacity, $obj->ses_start_date, $obj->ses_end_date);


						


				array_push($sessions, $ev);


			}


	


			return $sessions;


		} catch(Exception $e) {


			$this->printDebug($e);


			return false;


		}


	}


	


	/******************************************************************/


	// Returns an array of Track objects - if withFees is true, then the currently logged-in user is used to determine fees, otherwise no fees are returned


	public function getEventTracks($evt_key, $withFees=false) {


	


		if ($withFees == false) {


			$registrant = null;


			$cart = null;


		} else {


			// Get an Registrant object for the user


			$registrant = $this->getEventRegistrant($this->customerKey);


			$cart = $this->getNewShoppingCart($this->customerKey);


			


			$registrant->Registrant->reg_evt_key = $evt_key;	


		}


		


		return $this->_getEventTracks($evt_key, $registrant, $cart);		


	}


	


	/******************************************************************/


	// Returns an array of Track objects - if $registrant or $cart is null, no fees are returned


	private function _getEventTracks($evt_key, $registrant=null, $cart=null) {


			


		try {


			$tracks = Array();


			


			if (($registrant == null) or ($cart == null)) {


				// If no registrant or cart, then we can't retrieve the fee info, so just return the rest of the session info


				$response = $this->comm->getEventTracks($evt_key);


	


				// Load the XML into an object for easier access


				$xml = $response->WEBCentralizedShoppingCartGetTrackListByEventKeyResult->any;


				$response2 = @simplexml_load_string($xml);


	


				foreach ($response2 as $obj) {


					$ev = new Track($obj->trk_key, $obj->trk_name, $obj->trk_short_description);


						


					array_push($tracks, $ev);


				}


			} else {


				$response = $this->comm->getEventTrackFees($registrant, $cart);


				


				// Load the XML into an object for easier access


				$xml = $response->WEBCentralizedShoppingCartGetEventTrackFeesResult->any;


				$response2 = @simplexml_load_string($xml);





				foreach ($response2 as $obj) {


					$ev = new Track($obj->trk_key, $obj->Product, $obj->prd_short_description, $obj->Price, $obj->prc_Key);


						


					array_push($tracks, $ev);


				}


			}


			


			return $tracks;


		} catch(Exception $e) {


			$this->printDebug($e);


			return false;


		}


	}





	/******************************************************************/


	// Add the given events/sessions to the user's cart - parameter is an array of Event and/or Session objects which must have been loaded with fee information.


	//     The main Event must be the first object in the array


	public function registerForEvent($custKey, $eventsArray, $payment, $membershipArray = '') {


		try {


			$evt_cct_key = "";
			$evt_act_cct_key_ext = "";


			// Initialize the cart object


			$cart = $this->getNewShoppingCart($custKey);

			


			foreach ($eventsArray as $event)


			{	


			


			$sqlres = $this->comm->GetQuery('Event', 'evt_key, evt_cct_key, evt_act_cct_key_ext', 'evt_key=\''.$event['evt_key'].'\'', '');


			$sqlxml = $sqlres->GetQueryResult->any;


			//var_dump($sqlres);


			


			$sqlxml2 = @simplexml_load_string($sqlxml);


			foreach ($sqlxml2->EventObject as $obj) {


				//echo('<br>Event:'.$obj->evt_key);


				//echo('<br>EventCCT:'.$obj->evt_cct_key);


				//echo('<br>evt_act_cct_key_ext:'.$obj->evt_act_cct_key_ext);


				$evt_cct_key = $obj->evt_cct_key;


				$evt_act_cct_key_ext = $obj->evt_act_cct_key_ext;


			}





				$registrant = $this->getEventRegistrant($event['participant_key'], $event['evt_key']);


								


				$feeCollection = Array();


	


				$oFeeCollection = array ( "Fee"  => 


				Array(


					'prc_key' => $event['prc_key'], // $item->get_prc_key();


					'ivd_key' => $cart->Invoice->inv_key,


					'qty' => 1,


					'overrideamount' => 0.00,


					'action' => 'Add'


					)


				);


				


				//add the sessions and tracks...


				foreach ($event['sessions'] as $session)


				{


					$Fee = array ( "Fee"  => 


						Array(


						'prc_key' => $session, // $item->get_prc_key();


						'ivd_key' => $cart->Invoice->inv_key,


						'qty' => 1,


						'overrideamount' => 0.00,


						'action' => 'Add'


						))	;	


				array_push($oFeeCollection, $Fee);


				}





				foreach ($event['tracks'] as $track)


				{


					$Fee = array ( "Fee"  => 


						Array(


						'prc_key' => $track, // $item->get_prc_key();


						'ivd_key' => $cart->Invoice->inv_key,


						'qty' => 1,


						'overrideamount' => 0.00,


						'action' => 'Add'


						))	;	


						


				array_push($oFeeCollection, $Fee);


				}





				$registrant->Registrant->reg_cancel_date = "";


								//var_dump($registrant);	


				$result = $this->comm->setCartLineItems($registrant, $oFeeCollection);


				$registranto = $result->WEBCentralizedShoppingCartEventRegistrantSetLineItemsResult;


				


				//$cart->RegistrantCollection->EventsRegistrant->Registrant->reg_cancel_date = "";


				


				$result = $this->comm->addRegistrantToCart($registranto, $cart);	


			$cart = $result->WEBCentralizedShoppingCartAddEventRegistrantResult;


			


			}


			


			foreach ($membershipArray as $obj)


			{


				//var_dump($cart);


				$memresult = $this->comm->WEBCentralizedShoppingCartMembershipOpenInvoiceGet($obj);


				//var_dump($memresult);
				$feeCollection = Array();


	


				$oFeeCollection = array ( "Fee"  => 


				Array(


					'prc_key' => $event['prc_key'], // $item->get_prc_key();


					'ivd_key' => $cart->Invoice->inv_key,


					'qty' => 1,


					'overrideamount' => 0.00,


					'action' => 'Add'


					)


				);

				$cart = $this->comm->WEBCentralizedShoppingCartMembershipOpenInvoiceAdd($cart, $memresult);


				//var_dump($cart);


				//return 0;


			}


				
			
			//var_dump($cart);	
			$result = $this->comm->purchaseCart($cart, $payment, $evt_cct_key, $evt_act_cct_key_ext);


		


			


			//sample event....


			//a5c371fd-3cdb-4072-bcd6-8fba7d27c6be


			//sample session...


			//3d2d17bf-ce52-4c51-8b0a-17abb99a2196


			// Create the FeeCollection object


					


			





			return $result;


		} catch(Exception $e) {


			$this->printDebug($e);


			return false;


		}


	}


					


	/******************************************************************/


	// Returns true if the customer is already registered for the event, false otherwise


	public function isUserRegisteredForEvent($custKey, $eventKey) {


		try {


			$response = $this->comm->isUserRegisteredForEvent($custKey, $eventKey);


	


			//var_dump($response);


			return $response->WEBActivityAlreadyRegisteredForEventResult;


			


		} catch(Exception $e) {


			$this->printDebug($e);


			return false;


		}


	}





	/******************************************************************/


	// Returns true if the customer is already registered for the event, false otherwise


	public function getNewShoppingCart($custKey) {


		try {	


			$response = $this->comm->getNewShoppingCart($custKey);


	


			return $response;


		} catch(Exception $e) {


			$this->printDebug($e);


			return false;


		}


	}


	


	/******************************************************************/


	public function getRegistrantTypes($eventKey) {


		try {


			$response = $this->comm->getRegistrantTypes($eventKey);


			$response2 = @simplexml_load_string($response);


			return $response2;


		} catch(Exception $e) {


			$this->printDebug($e);


			return false;


		}


			


	}


	


	/******************************************************************/


	public function getRegistrantGuestTypes($eventKey) {


		try {


			$response = $this->comm->getRegistrantGuestTypes($eventKey);


			$response2 = @simplexml_load_string($response);


			return $response2;


		} catch(Exception $e) {


			$this->printDebug($e);


			return false;


		}


			


	}


	


	/******************************************************************/


	public function getAllRegistrantTypes() {


		try {


			$response = $this->comm->getAllRegistrantTypes();


			$response2 = @simplexml_load_string($response);


			return $response2;


		} catch(Exception $e) {


			$this->printDebug($e);


			return false;


		}


			


	}


	


	/******************************************************************/


	// Retrieves an EventRegistrant object for the user - includes the specific event if the event key is included


	public function getEventRegistrant($custKey, $eventKey='') {


		try {


			$response = $this->comm->getEventRegistrant($custKey, $eventKey);


		


	//		$response->Registrant->reg_rgt_key = $custKey;


			$response->Registrant->reg_cst_key = $custKey;


			


			return $response;


		} catch(Exception $e) {


			$this->printDebug($e);


			return false;


		}


	}





	/******************************************************************/


	// Returns an array of Committee objects created in netFORUM since the date/time given - if no date is specified, returns events created in the past day


	//   Return false on error


	public function getRecentCommittees($createdSince = '', $include_deleted = TRUE) {





		if ($this->comm == null)


			return false;





		try {


			$comm = Array();


			$where = 'cmt_add_date > \'';


	


			// If no criteria is passed-in, default to yesterday


			if ($createdSince == '') {


				$date = new DateTime();


				$date->add(DateInterval::createFromDateString('yesterday'));


				$createdSince = $date->format('Y-m-d') . '\'';


			}


	


			$where .= $createdSince . '\'';


	


			if ($include_deleted)


			{


			}


			else


			{


				$where .=' and ' . 'cmt_delete_flag = 0';


			}


	


			$response = $this->comm->GetQuery('Committee @TOP -1', 'cmt_key, cmt_name, cmt_asn_code, cmt_code, cmt_ctp_code, cmt_begin_date, cmt_end_date, cmt_description, cmt_delete_flag ', $where, '');


	


			// Load the XML into an object for easier access


			$xml = $response->GetQueryResult->any;


			$response2 = @simplexml_load_string($xml);


	


			foreach ($response2->CommitteeObject as $obj) {


				$committee = new Committee($obj->cmt_key, $obj->cmt_name, '', $obj->cmt_code, $obj->cmt_ctp_code, $obj->cmt_begin_date, $obj->cmt_end_date, $obj->cmt_description, $obj->cmt_delete_flag);			


				array_push($comm, $committee);


			}


	


			return $comm;


		} catch(Exception $e) {


			$this->printDebug($e);


			return false;


		}


	}





	/******************************************************************/


	// Returns an array of Committee objects created between the dates given - if no dates are specified, returns all committees


	//   Return false on error


	public function getCommitteesCreatedBetween($createdFrom = '', $createdTo = '', $include_deleted = TRUE) {





		if ($this->comm == null)


			return false;





		try {


			$comm = Array();





			// If no criteria is passed-in, return all


			if (($createdFrom == '') or ($createdTo == '')) {


				$createdSince ='cmt_add_date > \'1963-01-01\'';


			} else {


				$where = '(cmt_add_date > \'' . $createdFrom . '\') AND (cmt_add_date <= \'' . $createdTo . '\')';


			}


	


			if ($include_deleted)


			{


			}


			else


			{


				$where .=' and ' . 'cmt_delete_flag = 0';


			}


	


			$response = $this->comm->GetQuery('Committee @TOP -1', 'cmt_key, cmt_name, cmt_asn_code, cmt_code, cmt_ctp_code, cmt_begin_date, cmt_end_date, cmt_description, cmt_delete_flag ', $where, '');


	


			// Load the XML into an object for easier access


			$xml = $response->GetQueryResult->any;


			$response2 = @simplexml_load_string($xml);


	


			foreach ($response2->CommitteeObject as $obj) {


				$committee = new Committee($obj->cmt_key, $obj->cmt_name, '', $obj->cmt_code, $obj->cmt_ctp_code, $obj->cmt_begin_date, $obj->cmt_end_date, $obj->cmt_description, $obj->cmt_delete_flag);			


				array_push($comm, $committee);


			}


	


			return $comm;


		} catch(Exception $e) {


			$this->printDebug($e);


			return false;


		}


	}


	


	/*TBE*****************************************************************/


	// Returns an array of committe objects


	public function getCommitteeList($include_deleted = TRUE) {


		


		try {


			$committeeArray = Array();


	


			$response = $this->comm->getCommitteeList();


	


			// Load the XML into an object for easier access


			$xml = $response->WEBCommitteeGetCommitteeListResult->any;


			$response2 = @simplexml_load_string($xml);


	


			foreach ($response2 as $obj) {


				if ($include_deleted)


				{


					$committee = new Committee( $obj->cmt_key, $obj->cmt_name, '', $obj->cmt_code, $obj->cmt_ctp_code, $obj->cmt_begin_date, $obj->cmt_end_date, $obj->cmt_description, '');			


				}


				else


				{


					$committee = $this->getCommitteeDetails($obj->cmt_key);


				}


	


				if ($include_deleted || $committee->getIsDeleted() == '0')


				{


					array_push($committeeArray, $committee);


				}


			}


	


			return $committeeArray;


		} catch(Exception $e) {


			$this->printDebug($e);


			return false;


		}


	}





	/*TBE****************************************************************/


	// Returns the detailed information for the given committee


	public function getCommitteeDetails($cmt_key) {		





		try {


			$response = $this->comm->getCommitteeDetails($cmt_key); 


	


			// Load the XML into an object for easier access


			$xml = $response->WEBCommitteeGetCommitteeByKeyResult->any;


			$response2 = @simplexml_load_string($xml);


			$response2 = $response2->CommitteeObject;


	


			$committee = new Committee( $response2->cmt_key, $response2->cmt_name, '', $response2->cmt_code, $response2->cmt_ctp_code, $response2->cmt_begin_date, $response2->cmt_end_date, $response2->cmt_description, $response2->cmt_delete_flag);			


	


			return $committee;


		} catch(Exception $e) {


			$this->printDebug($e);


			return false;


		}


	}	


	


	/*TBE******************************************************************/


	public function getCommitteeMembers($cmt_key)


	{





		try {


			$response = $this->comm->getCommitteeMembers($cmt_key);


			// Load the XML into an object for easier access


			$xml = $response->WEBCommitteeGetMembersResult->any;


			$response2 = simplexml_load_string($xml);


			$response = array();


			


			foreach ($response2 as $obj) {


				$response[] = $obj;


			}


			return $response;


		} catch(Exception $e) {


			$this->printDebug($e);


			return false;


		}


	}





	


	/*******************************************************************/


	//


	public function getCommitteesForUser($cust_key)


	{


		try {


			// 


			$where = 'cmc_cst_key = \'' . $cust_key . '\'';


			$res = $this->comm->getQuery('mb_committee_x_customer', 'cmc_cmt_key', $where, '');			


			


			$xml = $res->GetQueryResult->any;


			$response2 = Array();			
			$response2 = @simplexml_load_string($xml);



			$ret = Array();


			foreach ($response2 as $obj) {


				array_push($ret, $obj->cmc_cmt_key);


			}


			


			return $ret;	


	


		} catch(Exception $e) {


			$this->printDebug($e);


			return false;


		}


	}


		


/*TBE******************************************************************/


	//Returns 0 for fail and 1 for success...maybe?


	public function addUserToCommittee($cmt_key, $cust_key, $positionkey, $nom_date)


	{


		// Check if the user is already a member of the committee


		if ($this->isUserInCommittee($cust_key, $cmt_key))


			return false;


		


		// Check if the user is already in the nomination queue


		if ($this->isUserNominatedForCommittee($cust_key, $cmt_key))


			return false;


		


		$response = $this->comm->addUserToCommittee($cmt_key, $cust_key, $positionkey, $nom_date);





		if ($response <> null) {


			$res = $response->WEBCommitteeNominationInsertResult;


		


			//$response2 = @simplexml_load_string($xml);





			$response2 = $res->CurrentKey;


			return $response2;


		} else {


			return false;


		}


	}


	


	/*******************************************************************/


	// Returns true if the user has a pending committee nomination


	public function isUserNominatedForCommittee($cust_key, $cmt_key) {


		


		try {


			$found = false;


			


			// Check for active nominations


			$where = 'nom_cst_key = \'' . $cust_key . '\' AND nom_cmt_key = \'' . $cmt_key . '\'';


			$res = $this->comm->getQuery('CommitteeNominations', '*', $where, '');


		


			$xml = $res->GetQueryResult->any;


			$response2 = @simplexml_load_string($xml);





			foreach ($response2 as $obj) {


				$found = true;


			}


			


			return $found;	


	


		} catch(Exception $e) {


			$this->printDebug($e);


			return false;


		}


	}


	


	/*******************************************************************/


	// Returns true if the user is already a member of the committee


	public function isUserInCommittee($cust_key, $cmt_key) {


		


		try {


			$found = false;


			


			// Check membership


			$res = $this->comm->getCommitteeMembers($cmt_key); 


						


			$xml = $res->WEBCommitteeGetMembersResult->any;


			$response2 = @simplexml_load_string($xml);


			foreach ($response2 as $obj) {


				//echo($obj->cmc_cst_key . '<br>');


				if ($obj->cmc_cst_key == $cust_key) {


					$found = true;


				}


			}





			return $found;	


	


		} catch(Exception $e) {


			$this->printDebug($e);


			return false;


		}


	}


	


	/*TBE******************************************************************/


	public function getCommitteePositions($cmt_key)


	{


		try {


			$committeePositionsArray = Array();


			


			$response = $this->comm->WEBCommitteeGetPositionList($cmt_key); 


			$xml = $response->WEBCommitteeGetPositionListResult->any;


			$response2 = @simplexml_load_string($xml);


			//echo('<pre>');


			//var_dump($response2);


			foreach ($response2 as $obj) {


				$committeePositions = new CommitteePositions( $obj->cop_key, $obj->cpo_code);


				array_push($committeePositionsArray, $committeePositions);


			}





			return $committeePositionsArray;


		} catch(Exception $e) {


			$this->printDebug($e);


			return false;


		}


	}


		


	/*TBE*****************************************************************/


	// Returns an array of sub committe objects


	public function getSubCommitteeList($cmt_key) {


		try {


			$subcommitteeArray = Array();


			


			$response = $this->comm->getSubCommitteeList($cmt_key);


	


			// Load the XML into an object for easier access


			$xml = $response->WEBCommitteeGetSubCommitteeListByCommitteeResult->any;


			$response2 = @simplexml_load_string($xml);


		


			foreach ($response2 as $obj) {


				$subcommittee = new SubCommittee( $obj->cmt_key, $obj->cmt_code, $obj->cmt_name, $obj->cmt_ctp_code, $obj->cmt_begin_date, $obj->cmt_end_date, $obj->cmt_description);		


				array_push($subcommitteeArray, $subcommittee);


			}


			return $subcommitteeArray;


		} catch(Exception $e) {


			$this->printDebug($e);


			return false;


		}


	}	


	


	public function getUserRecno($key) {


	


		$response = $this->comm->getUserInfo($key);


	


		//echo("<pre>");


		//var_dump($response);


	


		$xml = $response->WEBWebUserGetResult->Customer->cst_recno; //->Customer->cst_recno;


		//		echo("<pre>");


		//		var_dump($xml);


	


		return $xml;


	


	}


	


	public function changeUserPassword($custKey, $oldPwd, $newPwd)


	{





		try {


			$recno = $this->getUserRecno($custKey);


		


			$res = $this->comm->changeUserPassword($recno, $oldPwd, $newPwd);


			$xml = $res->WEBWebUserChangePasswordResult;


				


			return $xml;


		} catch(Exception $e) {


			$this->printDebug($e);


			return false;


		}


	}


	


	// Returns true on success, false otherwise


	public function changeUserPasswordForce($cst_key, $password)

	{

		$response = $this->comm->changeUserPasswordForce($cst_key, $password);

		$xml = $response->UpdateFacadeObjectResult->any;

		$response2 = @simplexml_load_string($xml);


		foreach ($response2 as $obj) {

			return true;

		}

		return false;


	}


	


	public function getQuery($table, $field_list, $where_clause, $orderBy = '')


	{


		try {


			$response = $this->comm->GetQuery($table, $field_list, $where_clause, $orderBy);


		


			// Load the XML into an object for easier access


			$xml = $response->GetQueryResult->any;


			$response2 = @simplexml_load_string($xml);


			return $response2;


		} catch (SoapFault $exception) {


			$this->printDebug($exception);


		}


	}	


	


	public function getCommitteePositionKeyFromName($cmt_key, $position_name)


	{


		$res_array = $this->getCommitteePositions($cmt_key);


	


		foreach ($res_array as $obj) {


			if (strtolower($position_name) == strtolower($obj->getCode()))


				return $obj->getKey();


		}


	}


	


	public function  getPaymentMethods()


	{


		$payments = Array();


		$cart = $this->getNewShoppingCart($this->customerKey);


		$resultant = $this->comm->getPaymentMethods($cart);


		$xml = $resultant->WEBCentralizedShoppingCartGetPaymentOptionsResult->any;


		$response2 = @simplexml_load_string($xml);


	


		foreach ($response2->Result as $obj) {


			//var_dump($obj->apm_key);


			$payment = new PaymentMethods($obj->apm_key, $obj->apm_type, $obj->apm_method);


			array_push($payments, $payment);


		}


		


		return $payments;


	}


public function getClosedInvoicesForCustomerOrganization($cust_key)


	{


	try {
			$invoiceArray = Array();

			//get their organization...


			$profile = $this->getProfileInfo($cust_key);


			$org_key = $profile->getOrganizationKey();
			//var_dump($org_key);

			if ($org_key == '' || $org_key  == null)


				return $invoiceArray;

if ($this->getIsIndividualPOCAtOrganization($cust_key, $org_key))	{


					$lookup_key = $org_key;


				}


				else {


					return $invoiceArray;


				}



			$invoiceArray = $this->getClosedInvoicesForCustomer($org_key);


			return $invoiceArray;


			


			


		} catch(Exception $e) {


			$this->printDebug($e);


			return false;


		}


	


	}


	


	/*TBE*****************************************************************/


	
public function getClosedInvoicesForCustomer($cust_key) {


		try {


			$invoiceArray = Array();


	


			//see if an organization was included, if so use it as the lookup....


			


			$invoices = Array();


			


			$customer_invoices = $this->comm->GetQuery('Invoice', 'inv_key, inv_code_cp, ac_invoice_detail.ivd_amount_cp, oe_product.prd_name, inv_trx_date, inv_code, vw_ac_invoice.sum_linebalance, ind_int_code', 'inv_cst_key=\'' . $cust_key . '\'', '');
			
			

			// Load the XML into an object for easier acces
			$xml = $customer_invoices->GetQueryResult->any;

			libxml_use_internal_errors();

			$customer_invoices2 = @simplexml_load_string($xml);


			

			foreach ($customer_invoices2->InvoiceObject as $obj) {


				$inv = new Invoice($obj->inv_key, $obj->inv_code_cp, $obj->prd_name, $obj->ivd_amount_cp, $obj->inv_trx_date, $obj->inv_code, $obj->sum_linebalance, $obj->ind_int_code);


				array_push($invoices, $inv);


			}

			

			return $invoices;	


		} catch(Exception $e) {


			$this->printDebug($e);


			return false;


		}


	}




	public function getActiveInvoicesForCustomerOrganization($cust_key)


	{


	try {


			//get their organization...


			$profile = $this->getProfileInfo($cust_key);


			$org_key = $profile->getOrganizationKey();


			if ($org_key == '' || $org_key  == null)


				return null;


			$retVal = $this->getActiveInvoicesForCustomer($cust_key, $org_key);


			return $retVal;


			


			


		} catch(Exception $e) {


			$this->printDebug($e);


			return false;


		}


	


	}


	


	/*TBE*****************************************************************/


	// Returns an array of committe objects


	public function getActiveInvoicesForCustomer($cust_key, $org_key='') {


		try {


			$invoiceArray = Array();


	


			//see if an organization was included, if so use it as the lookup....


			if ($org_key == '')


				$lookup_key = $cust_key;


			else


			{


				if ($this->getIsIndividualPOCAtOrganization($cust_key, $org_key))	{


					$lookup_key = $org_key;


				}


				else {


					return false;


				}


			}


	


			$response = $this->comm->getActiveInvoicesForCustomer($lookup_key);


	


			// Load the XML into an object for easier access


			$xml = $response->WEBCentralizedShoppingCartMembershipOpenInvoiceGetListResult->any;


			$response2 = @simplexml_load_string($xml);


	


			foreach ($response2 as $obj) {	


				$inv = new Invoice($obj->inv_key, $obj->inv_code_cp, $obj->prd_name, $obj->ivd_amount_cp, $obj->inv_trx_date, $obj->inv_code, $obj->sum_linebalance, $obj->ind_int_code);


				//array_push($invoices, $inv);


	


				array_push($invoiceArray, $obj);


			}


	


			return $invoiceArray;


		} catch(Exception $e) {


			$this->printDebug($e);


			return false;


		}


	}


	


	public function getIsIndividualPOCAtOrganization($cust_key, $org_key)


	{


		try {


			$res = $this->getQuery('OrganizationAffiliation  @TOP -1', 'ixo_rlt_code', "ind_cst_key='".$cust_key."' and org_cst_key='".$org_key."' and ixo_end_date is null");


			foreach($res as $obj) {


				if ($obj->ixo_rlt_code == "POC")


					return 1;


			}


			return 0;


		} catch(Exception $e) {


			$this->printDebug($e);


			return false;


		}


	}

public function getPOCMembersForOrganization($org_key)
	{
		try {
			
			$retVal = Array();
			
			$res = $this->getQuery('OrganizationAffiliation  @TOP -1', 'ixo_rlt_code, ind_cst_key', "org_cst_key='".$org_key."' and ixo_end_date is null");
			foreach($res as $obj) {
				if ($obj->ixo_rlt_code == "POC")
					//echo($obj->ind_cst_key.'<br>');
					array_push($retVal, $obj->ind_cst_key);
			}
			return $retVal;
		} catch(Exception $e) {
			$this->printDebug($e);
			return false;
		}
	}
	

	


}

















?>


