<?php
	//use files
	require_once('mysqlconnection.php');
	require_once('exceptions/recordnotfoundexception.php');
	//class
	class BuildingType {
		//attributes
		private $id;
		private $description;
		private $color;
		//getters and setters
		public function getId() { return $this->id; }
		public function setId($value) { $this->id = $value; }
		public function getDescription() { return $this->description; }
		public function setDescription($value) { $this->description = $value; }
		public function getColor() { return $this->color; }
		public function setColor($value) { $this->color = $value; }
		
		//constructor
		public function __construct() {
			//empty object
			if (func_num_args() == 0) {
				$this->id = '';
				$this->description = '';
				$this->color = '';
			}
			//object with data from database
			if (func_num_args() == 1) {
				//get id
				$id = func_get_arg(0);
				//get connection
				$connection = MySqlConnection::getConnection();
				//query
				$query = 'select id, description, color from buildingtype where id=?';
				//command
				$command = $connection->prepare($query);
				//bind parameters
				$command->bind_param('s', $id);
				//execute
				$command->execute();
				//bind results
				$command->bind_result($this->id, $this->description, $this->color);
				//fetch data
				$found = $command->fetch();
				//close command
				mysqli_stmt_close($command);
				//close connection
				$connection->close();
				//throw exception if record not found
				if (!$found) throw new RecordNotFoundException();
			}
			//object with data from arguments
			if (func_num_args() == 3) {
				//get arguments
				$arguments = func_get_args();
				//pass arguments to attributes
				$this->id = $arguments[0];
				$this->description = $arguments[1];
				$this->color = $arguments[2];
			}
		}
		
		//instance methods
		
		//add
		public function add() {
			//get connection
			$connection = MySqlConnection::getConnection();
			//query
			$query = 'insert into buildingtype (id, description, color) values(?, ?, ?)';
			//command
			$command = $connection->prepare($query);
			//bind parameters
			$command->bind_param('sss', $this->id, $this->description, $this->color);
			//execute
			$result = $command->execute();
			//close command
			mysqli_stmt_close($command);
			//close connection
			$connection->close();
			//return result
			return $result;
		}
		
		//edit
		public function edit() {
			//get connection
			$connection = MySqlConnection::getConnection();
			//query
			$query = 'update buildingtype set description = ?, color = ? where id = ?';
			//command
			$command = $connection->prepare($query);
			//bind parameters
			$command->bind_param('sss', $this->description, $this->color, $this->id);
			//execute
			$result = $command->execute();
			//close command
			mysqli_stmt_close($command);
			//close connection
			$connection->close();
			//return result
			return $result;
			
		}
		
		//delete
		public function delete() {
			//get connection
			$connection = MySqlConnection::getConnection();
			//query
			$query = 'delete from buildingtype where id = ?';
			//command
			$command = $connection->prepare($query);
			//bind parameters
			$command->bind_param('s', $this->id);
			//execute
			$result = $command->execute();
			//close command
			mysqli_stmt_close($command);
			//close connection
			$connection->close();
			//return result
			return $result;
		}
	
		//represents the object in JSON format
		public function toJson() {
			return json_encode(array(
				'id' => $this->id,
				'description' => $this->description,
				'color' => $this->color
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
			$query = 'select id, description, color from buildingtype';
			//command
			$command = $connection->prepare($query);
			//execute
			$command->execute();
			//bind results
			$command->bind_result($id, $description, $color);
			//fetch data
			while ($command->fetch()) {
				array_push($list, new BuildingType($id, $description, $color));
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
				'buildingTypes' => $list
			));
		}
	}
?>