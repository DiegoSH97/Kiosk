<?php
	//allow access
	header('Access-Control-Allow-Access: *');
	//allow methods
	header('Access-Control-Allow-Methods: GET');
	//allow headers
	header('Access-Control-Allow-Headers: user, password');
	//read headers
	$headers = getallheaders();
	//use user class
	require_once($_SERVER['DOCUMENT_ROOT'].'/kiosk/models/user.php');
	//use security
	require_once($_SERVER['DOCUMENT_ROOT'].'/kiosk/security/security.php');
	//check if headers were received
	if (isset($headers['user']) && isset($headers['password'])) {
		//authenticate user
		try {
			//create user
			$user = new User($headers['user'], $headers['password']);
			//diplay
			echo json_encode(array(
				'status' => 0,
				'user' => json_decode($user->toJson()),
				'token' => Security::generateToken($headers['user'])
			));
		}
		catch (InvalidUserException $ex) {
			echo json_encode(array(
			'status' => 2,
			'errorMessage' => $ex->get_message()
		));
		}
	}
	else
		echo json_encode(array(
			'status' => 1,
			'errorMessage' => 'Missing headers'
		));
?>