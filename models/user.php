<?php
	//use files
	require_once('mysqlconnection.php');
	require_once('exceptions/invaliduserexception.php');
	//class
	class User {
		//attributes
		private $id;
		private $name;
		private $password;
		
		//getters and setters
		public function getId() { return $this->id; }
		public function setId($value) { $this->id = $value; }
		public function getName() { return $this->name; }
		public function setName($value) { $this->name = $value; }
		public function setPassword($value) { $this->password = $value; }
		
		//constructor
		public function __construct() {
			//empty object
			if (func_num_args() == 0) {
				$this->id = '';
				$this->name = '';
				$this->password = '';
			}
			//object with data from database
			if (func_num_args() == 2) {
				//get arguments
				$arguments = func_get_args();
				$id = $arguments[0];
				$password = $arguments[1];
				//get connection
				$connection = MySqlConnection::getConnection();
				//query
				$query = 'select id, name from users where id = ? and password = sha1(?)';
				//command
				$command = $connection->prepare($query);
				//bind parameters
				$command->bind_param('ss', $id, $password);
				//execute
				$command->execute();
				//bind results
				$command->bind_result($this->id, $this->name);
				//fetch data
				$found = $command->fetch();
				//close command
				mysqli_stmt_close($command);
				//close connection
				$connection->close();
				//throw exception if record not found
				if (!$found) throw new InvalidUserException($id);
			}
			//object with data from arguments
			if (func_num_args() == 3) {
				//get arguments
				$arguments = func_get_args();
				//pass arguments to attributes
				$this->id = $arguments[0];
				$this->name = $arguments[1];
				$this->password = $arguments[2];
			}
		}
		
		//instance methods
		
		//add
		public function add() {
			
		}
		
		//edit
		public function edit() {	
			
		}
		
		//delete
		public function delete() {
			
		}
		
		//reset password
		public function resetPassword($newPassword) {
			
		}
	
		//represents the object in JSON format
		public function toJson() {
			return json_encode(array(
				'id' => $this->id,
				'name' => $this->name
			));
		}
		
		//class methods
		
		//get all
		public static function getAll() {
			//list
			$list = array();
			//get connection
			$connection = MySqlConnection::getConnection();
			//query
			$query = 'select id, name from users';
			//command
			$command = $connection->prepare($query);
			//execute
			$command->execute();
			//bind results
			$command->bind_result($id, $name);
			//fetch data
			while ($command->fetch()) {
				array_push($list, new User($id, $name));
			}
			//close command
			mysqli_stmt_close($command);
			//close connection
			$connection->close();
			//return list
			return $list;
		}
	
		//get all in JSON format
		public static function getAllJson() {
			//list
			$list = array();
			//get all
			foreach(self::getAll() as $item) {
				array_push($list, json_decode($item->toJson()));
			}
			//return json encoded array
			return json_encode(array(
				'users' => $list
			));
		}
	}
?>