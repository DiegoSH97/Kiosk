<?php
	//access control
	//allow access from outside the server
	header('Access-Control-Allow-Origin: *');
	//allow methods
	header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
	//allow headers
	header('Access-Control-Allow-Headers: user, token');
	//get headers
	$headers = getallheaders();
	//check if headers were received
	if (isset($headers['user']) && isset($headers['token'])) {
		//authenticate token
		require_once($_SERVER['DOCUMENT_ROOT'].'/kiosk/security/security.php');
		if ($headers['token'] != Security::generateToken($headers['user'])) {
			echo json_encode(array(
				'status' => 998,
				'errorMessage' => 'Invalid security token for user '.$headers['user']
			));
			//kill the script
			die();
		}
	}
	else {
		echo json_encode(array(
			'status' => 999,
			'errorMessage' => 'Missing security headers'
		));
		//kill the script
		die();
	}
	
	//use Building class
	require_once($_SERVER['DOCUMENT_ROOT'].'/kiosk/models/building.php');
	
	//GET (Read)
	if ($_SERVER['REQUEST_METHOD'] == 'GET') {
		//parameters
		if (isset($_GET['id'])) {
			try {
				//create object
				$b = new Building($_GET['id']);
				//display
				echo json_encode(array(
					'status' => 0,
					'building' => json_decode($b->toJson())
				));
			}
			catch (RecordNotFoundException $ex) {
				echo json_encode(array(
					'status' => 1,
					'errorMessage' => $ex->get_message()
				));
			}
		}
		else {
			echo Building::getAllJson();
		}
			
	}
	
	//POST (insert)
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		//check parameters
		if (isset($_POST['id']) &&
			isset($_POST['name']) &&
			isset($_POST['latitude']) &&
			isset($_POST['longitude']) &&
			isset($_POST['typeid']) ) {
			//error
			$error = false;
			//building type
			try {
				$bt = new BuildingType($_POST['typeid']);
			}
			catch (RecordNotFoundException $ex) {
				echo json_encode(array(
					'status' => 2,
					'errorMessage' => 'Invalid building type'
				));
				$error = true; //found error
			}
			//add building
			if (!$error) {
				//create empty object
				$b = new Building();
				//set values
				$b->setId($_POST['id']);
				$b->setName($_POST['name']);
				$b->setLocation(new Location($_POST['latitude'], $_POST['longitude']));
				$b->setType($bt);
				//add
				if ($b->add())
					echo json_encode(array(
						'status' => 0,
						'message' => 'Building added successfully'
					));
				else
					echo json_encode(array(
						'status' => 3,
						'errorMessage' => 'Could not add building'
					));
			}
		}
		else 
			echo json_encode(array(
				'status' => 1,
				'errorMessage' => 'Missing parameters'
			));
	}
	
	//PUT (update)
	if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
		//read data
		parse_str(file_get_contents('php://input'), $putData);
		if (isset($putData['data'])) {
			//decode json
			$jsonData = json_decode($putData['data'], true);
			//check parameters
			if (isset($jsonData['id']) &&
				isset($jsonData['name']) &&
				isset($jsonData['latitude']) &&
				isset($jsonData['longitude']) &&
				isset($jsonData['typeid']) ) {
				//error
				$error = false;
				//building type
				try {
					$bt = new BuildingType($jsonData['typeid']);
				}
				catch (RecordNotFoundException $ex) {
					echo json_encode(array(
						'status' => 3,
						'errorMessage' => 'Invalid building type'
					));
					$error = true; //found error
				}
				//edit building
				if (!$error) {
					//create empty object
					try {
						$b = new Building($jsonData['id']);
						
						//set values
						$b->setName($jsonData['name']);
						$b->setLocation(new Location($jsonData['latitude'], $jsonData['longitude']));
						$b->setType($bt);
						//add
						if ($b->edit())
							echo json_encode(array(
								'status' => 0,
								'message' => 'Building edited successfully'
							));
						else
							echo json_encode(array(
								'status' => 5,
								'errorMessage' => 'Could not edit building'
							));
					}
					catch (RecordNotFoundException $ex) {
						echo json_encode(array(
							'status' => 4,
							'errorMessage' => 'Invalid building id'
						));
					}
				}	
			}
			else
				echo json_encode(array(
					'status' => 2,
					'errorMessage' => 'Missing parameters'
				));
		}
		else
			echo json_encode(array(
				'status' => 1,
				'errorMessage' => 'Missing data parameter'
			));
	}
	
	//DELETE (delete)
	if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
		//read id
		parse_str(file_get_contents('php://input'), $putData);
		if (isset($putData['id'])) {
			try {
				//create object
				$b = new Building($putData['id']);
				//delete
				if ($b->delete())
					echo json_encode(array(
						'status' => 0,
						'errorMessage' => 'Building deleted successfully'
					));
				else
					echo json_encode(array(
						'status' => 3,
						'errorMessage' => 'Could not delete building'
					));
			}
			catch(RecordNotFoundException $ex) {
				echo json_encode(array(
					'status' => 2,
					'errorMessage' => 'Invalid building id'
				));
			}
		}
		else {
			echo json_encode(array(
				'status' => 1,
				'errorMessage' => 'Missing id parameter'
			));
		}
	}
?>









