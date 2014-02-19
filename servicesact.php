<?php

function curlCommonSettings($cookie_session = NULL) {
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_HEADER, FALSE);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($curl, CURLOPT_FAILONERROR, TRUE);
	if (!is_null($cookie_session)) {
		curl_setopt($curl, CURLOPT_COOKIE, "$cookie_session");
	}

	return $curl;
}

function getCurlData($curl) {
	$response = curl_exec($curl); 
	$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	print "HTTP Code: $http_code\n";
	print "Response: " . $response . "\n";

	if ($http_code == 200) {
		$data = json_decode($response);
		return $data;
	}
	else {
		// Get error message.
		$http_message = curl_error($curl);
		print $http_message . "\n";
		return FALSE;
	}
}

function getEntity($service_url, $entity_type, $entity_id, $cookie_session = NULL) {
	$curl = curlCommonSettings($cookie_session);
	curl_setopt($curl, CURLOPT_URL, $service_url . '/' . $entity_type . '/' . $entity_id . '.json');
	curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept: application/json'));

	return getCurlData($curl);
}

function createEntity($service_url, $entity_type, $data, $cookie_session = NULL) {
	$curl = curlCommonSettings($cookie_session);
	curl_setopt($curl, CURLOPT_URL, $service_url . '/' . $entity_type . '.json');
	curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));

	return getCurlData($curl);
}

function updateEntity($service_url, $entity_type, $entity_id, $data, $cookie_session = NULL) {
	$curl = curlCommonSettings($cookie_session);
	curl_setopt($curl, CURLOPT_URL, $service_url . '/' . $entity_type . '/' . $entity_id . '.json');
	curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
	curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));

	return getCurlData($curl);
}

function deleteEntity($service_url, $entity_type, $entity_id, $cookie_session = NULL) {
	$curl = curlCommonSettings($cookie_session);
	curl_setopt($curl, CURLOPT_URL, $service_url . '/' . $entity_type . '/' . $entity_id . '.json');
	curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept: application/json'));
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');

	return getCurlData($curl);
}

function resourceTypeAction($service_url, $resource_type, $action, $data, $cookie_session = NULL) {
	$curl = curlCommonSettings($cookie_session);
	curl_setopt($curl, CURLOPT_URL, $service_url . '/' . $resource_type . '/' . $action . '.json');
	curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));

	return getCurlData($curl);
}

function resourceTypeTargetedAction($service_url, $resource_type, $entity_id, $action, $data, $cookie_session = NULL) {
	$curl = curlCommonSettings($cookie_session);
	curl_setopt($curl, CURLOPT_URL, $service_url . '/' . $resource_type . '/' . $entity_id . '/' . $action . '.json');
	curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));

	return getCurlData($curl);
}

	
//$service_url = 'http://act.redcrackle.com/rest';
$service_url = 'http://ec2-54-225-125-151.compute-1.amazonaws.com/commons/rest';



/************** Creating New User *************/
/*

$new_username = 'devid';
$new_password = '1234';
$new_user_info = array(
	'username' => $new_username,  
	'name' => 'devid', 
	'pass' => $new_password,                
	'mail' => 'devid@test.com',
	'field_name_first' => array(
			'und' => array(
				0 => array(
					'value' => 'Peter',
				),
			),
	  ),
	  'field_name_last' => array(
			'und' => array(
				0 => array(
					'value' => 'Mac',
				),
			),
	  ),
	  'field_mypoints' => array(
			'und' => array(
				0 => array(
					'value' => 240,
				),
			),
	  ),
	  'field_valid_certificates' => array(
			'und' => array(
				0 => array(
					'value' => 50,
				),
			),
	  ),
	  'field_total_amount' => array(
			'und' => array(
				0 => array(
					'value' => 224,
				),
			),
	  ),
	  'field_user_status' => array(
			'und' => array(
				0 => array(
					'value' => 'Premium Silver',
				),
			),
	  ),
	  'field_user_address' => array(
			'und' => array(
				0 => array(
					'value' => '2101 California Street, Apt. 415,Mountain View, CA 94040.',
				),
			),
	  ),
	  'field_user_contact_email' => array(
			'und' => array(
				0 => array(
					'value' => 'example.example@example.com',
				),
			),
	  ),
	  'field_user_primary_phone' => array(
			'und' => array(
				0 => array(
					'value' => '9999999999',
				),
			),
	  ),
	  'field_facebook_url' => array(
			'und' => array(
				0 => array(
					'url' => 'https://www.facebook.com/',
				),
			),
	  ),
	  'field_linkedin_url' => array(
			'und' => array(
				0 => array(
					'url' => 'http://www.linkedin.com',
				),
			),
	  ),
	  'field_twitter_url' => array(
			'und' => array(
				0 => array(
					'url' => 'https://twitter.com',
				),
			),
	  ),            
);

print "Creating a new user.\n";
$new_user = createEntity($service_url, 'user', $new_user_info);
if (!$new_user) {
  exit;
}
print "User $new_user->uid created.\n";
exit;


/************************ End **********************/




/******************** Update User ******************/
/*
// Log into devendra account.
$login_info = array(
	'username' => 'devid',
	'password' => '1234',
);

// Log in.
print "Logging in as devid.\n";
$n_user = resourceTypeAction($service_url, 'user', 'login', $login_info);
if (!$n_user) {
	// Login failed.
	exit;
}

// Now that we are logged in as an devendra, save the cookie.
$cookie_session = $n_user->session_name . '=' . $n_user->sessid;


$resourceData = array( 
			  'field_name_first' => array(
					'und' => array(
						0 => array(
							'value' => 'Neerav',
						),
					),
			  ),
			  'field_name_last' => array(
					'und' => array(
						0 => array(
							'value' => 'Mehta',
						),
					),
			  ),
			  'field_mypoints' => array(
					'und' => array(
						0 => array(
							'value' => 24000000,
						),
					),
			  ),
			  'field_valid_certificates' => array(
					'und' => array(
						0 => array(
							'value' => 5000000,
						),
					),
			  ),
			  'field_total_amount' => array(
					'und' => array(
						0 => array(
							'value' => 220000,
						),
					),
			  ),
			  'field_user_status' => array(
					'und' => array(
						0 => array(
							'value' => 'Premium Silver GOLD',
						),
					),
			  ),
			  'field_user_address' => array(
					'und' => array(
						0 => array(
							'value' => '2101 California Street, Apt. 415,Mountain View, CA 94040.',
						),
					),
			  ),
			  'field_user_contact_email' => array(
					'und' => array(
						0 => array(
							'value' => 'example.example@example.com',
						),
					),
			  ),
			  'field_user_primary_phone' => array(
					'und' => array(
						0 => array(
							'value' => '9999999999',
						),
					),
			  ),
			  'field_facebook_url' => array(
					'und' => array(
						0 => array(
							'url' => 'https://www.facebook.com/',
						),
					),
			  ),
			  'field_linkedin_url' => array(
					'und' => array(
						0 => array(
							'url' => 'http://www.linkedin.com',
						),
					),
			  ),
			  'field_twitter_url' => array(
					'und' => array(
						0 => array(
							'url' => 'https://twitter.com',
						),
					),
			  ),                    
			); 

	print "Updated User Profile " . $n_user->user->uid . ".\n";
	$result = updateEntity($service_url, 'user', $n_user->user->uid, $resourceData, $cookie_session);
    $anonymous_user = resourceTypeAction($service_url, 'user', 'logout', array(), $cookie_session);
exit;

/****************** End **************************/



/******************* Delete User *****************/
    
    // Log in as the new user.
    $login_info = array(
	    'username' => 'devid',
	    'password' => '1234',
    );

    // Log in.
    print "Logging in as user testuser.\n";
    $new_user = resourceTypeAction($service_url, 'user', 'login', $login_info);

    if (!$new_user) {
	  // Login failed.
	  exit;
    }
    // Save the cookie.
    $cookie_session = $new_user->session_name . '=' . $new_user->sessid;

    
    
    print "Log in as admin account.\n";
	// Log in as admin first.
	$login_info = array(
		'username' => 'admin',
		'password' => '1234',
	);

    // Log in.
	$admin_user = resourceTypeAction($service_url, 'user', 'login', $login_info);

	if (!$admin_user) {
		// Login failed.
		exit;
	}

	// Now that we are logged in as an admin, save the cookie.
	$cookie_session = $admin_user->session_name . '=' . $admin_user->sessid;


    /// Deleting the user.
	print "Deleting user " . $new_user->user->uid . ".\n";
	$new_status = deleteEntity($service_url, 'user', $new_user->user->uid, $cookie_session);
    
    
    // Log out of the admin account.
	print "Logging out of admin account.\n";
	$anonmous_user = resourceTypeAction($service_url, 'user', 'logout', array(), $cookie_session);
	exit;

/******************** End ***************/




/******************* User Logout *********/
/*
// Log in as the new user.
    $login_info = array(
	    'username' => 'admin',
	    'password' => '1234',
    );

    // Log in.
    print "Logging in as user ".$login_info['username'].".\n";
    $new_user = resourceTypeAction($service_url, 'user', 'login', $login_info);

    if (!$new_user) {
	  // Login failed.
	  exit;
    }
    // Save the cookie.
    $cookie_session = $new_user->session_name . '=' . $new_user->sessid;
    //exit;
    //Log out of the user account.
	print "Logging out of user account(".$new_user->user->uid.").\n";
	$anonmous_user = resourceTypeAction($service_url, 'user', 'logout', array(), $cookie_session);
	exit;

/******************** End ***************/
?>

