<?php
	//use files
	require_once('mysqlconnection.php');
	require_once('exceptions/recordnotfoundexception.php');
	require_once('location.php');
	require_once('buildingtype.php');
	
	class Building {
		//attributes
		private $id;
		private $name;
		private $location;
		private $type;
		
		//getters and setters
		public function getId() { return $this->id; }
		public function setId($value) { $this->id = $value; }
		public function getName() { return $this->name; }
		public function setName($value) { $this->name = $value; }
		public function getLocation() { return $this->location; }
		public function setLocation($value) { $this->location = $value; }
		public function getType() { return $this->type; }
		public function setType($value) { $this->type = $value; }
		
		//constructor
		public function __construct() {
			//empty object
			if (func_num_args() == 0) {
				$this->id = '';
				$this->name = '';
				$this->location = new Location();
				$this->type = new BuildingType();
			}
			//object with data from database
			if (func_num_args() == 1) {
				//get id
				$id = func_get_arg(0);
				//get connection
				$connection = MySqlConnection::getConnection();
				//query
				$query = 'select b.id, b.name, b.latitude, b.longitude, bt.id, bt.description, bt.color 
						  from buildings as b join buildingtype as bt on b.idBuildingType = bt.id 
						  where b.id = ?';
				//command
				$command = $connection->prepare($query);
				//bind parameters
				$command->bind_param('s', $id);
				//execute
				$command->execute();
				//bind results
				$command->bind_result($id, $name, $latitude, $longitude, $idType, $description, $color);
				//fetch data
				$found = $command->fetch();
				//close command
				mysqli_stmt_close($command);
				//close connection
				$connection->close();
				//pass values to the attributes
				if ($found) {
					$this->id = $id;
					$this->name = $name;
					$this->location = new Location($latitude, $longitude);
					$this->type = new BuildingType($idType, $description, $color);
				}
				else {		
					//throw exception if record not found
					throw new RecordNotFoundException();
				}
			}
			//object with data from arguments
			if (func_num_args() == 4) {
				//get arguments
				$arguments = func_get_args();
				//pass arguments to attributes
				$this->id = $arguments[0];
				$this->name = $arguments[1];
				$this->location = $arguments[2];
				$this->type = $arguments[3];
			}
		}
		
		//instance methods
		
		//add
		public function add() {
			//get connection
			$connection = MySqlConnection::getConnection();
			//query
			$query = 'insert into buildings (id, name, latitude, longitude, idBuildingType) values(?, ?, ?, ?, ?)';
			//command
			$command = $connection->prepare($query);
			//bind parameters
			$command->bind_param('ssdds', $this->id, $this->name, $this->location->getLatitude(), $this->location->getLongitude(), $this->type->getId());
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
			$query = 'update buildings 
					  set name = ?, latitude = ?, longitude = ?, idBuildingType = ? 
					  where id = ?';
			//command
			$command = $connection->prepare($query);
			//bind parameters
			$command->bind_param('sddss', 
				$this->name, 
				$this->location->getLatitude(), 
				$this->location->getLongitude(), 
				$this->type->getId(), 
				$this->id);
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
			$query = 'delete from buildings where id = ?';
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
				'name' => $this->name,
				'location' => json_decode($this->location->toJson()),
				'type' => json_decode($this->type->toJson())
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
			$query = 'select b.id, b.name, b.latitude, b.longitude, bt.id, bt.description, bt.color 
					  from buildings as b join buildingtype as bt on b.idBuildingType = bt.id order by b.id';
			//command
			$command = $connection->prepare($query);
			//execute
			$command->execute();
			//bind results
			$command->bind_result($id, $name, $latitude, $longitude, $idType, $description, $color);
			//fetch data
			while ($command->fetch()) {
				$location = new Location($latitude, $longitude);
				$type = new BuildingType($idType, $description, $color);
				array_push($list, new Building($id, $name, $location, $type));
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
				'buildings' => $list
			));
		}
	}
?>








