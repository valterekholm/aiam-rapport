<?php
class Apikey{

	private $conn;
	private $table_name = "api_keys";

	public $id;
	public $id_user;
	public $nyckel;
	public $created;

	public function __construct($db){
		$this->conn = $db;
	}
	function read($user_id){
		$query = "SELECT * FROM api_keys WHERE id_user = $user_id";
		$stmt = $this->conn->prepare($query);
		$stmt->execute();
		return $stmt;
	}

	function get_users_temp_keys($user_id, $key){
		$query = "SELECT * FROM api_keys ak LEFT JOIN api_temp_keys atk using (id_user) WHERE ak.id_user = $id AND ak.nyckel = '$key'";
		$stmt = $this->conn->prepare($query);
		$stmt->execute();
		return $stmt;
	}

	function key_auth($user_id, $key){
		$query = "SELECT COUNT(*) c FROM api_keys WHERE id_user = $user_id AND nyckel = '$key'";
		$stmt = $this->conn->prepare($query);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		return $result["c"] > 0;
	}

	function key_auth_get_id($key){
		$query = "SELECT id_user FROM api_keys WHERE nyckel = '$key'";
		$stmt = $this->conn->prepare($query);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		if($result){
			return $result["id_user"];
		}
		else{
			return false;
		}
	}

	function temp_key_auth($key, $temp_key){
		error_log("temp_key_auth($key, $temp_key)");
		$id_user = $this->key_auth_get_id($key);

		if(!is_numeric($id_user)){
			return false;
		}

		$query = "SELECT COUNT(*) c FROM api_temp_keys WHERE id_user = $id_user AND nyckel = '$temp_key'";
		$stmt = $this->conn->prepare($query);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		return $result["c"] > 0;

	}

	function make_temp_key($user_id, $string=FALSE){
		error_log("make_temp_key med $user_id");
		if($string == FALSE){
			error_log("no string supplied");
			$string = $this->random_str(60);
		}

		$query = "INSERT INTO api_temp_keys (id_user, nyckel) VALUES ($user_id, '$string')";
		$stmt = $this->conn->prepare($query);
		$success = $stmt->execute();

		if($success){
			$this->clear_old_temp_keys();
			return $string;
		}
		else return false;
	}
/*
	function generateRandomString($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
 */ //not 100% safe, comm.out 8 apr -19

	 function random_str($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZåäöÅÄÖ_')
	 {
		 $pieces = [];
		 $max = mb_strlen($keyspace, '8bit') - 1;
		 for ($i = 0; $i < $length; ++$i) {
			 $pieces []= $keyspace[random_int(0, $max)];
		 }
		 return implode('', $pieces);
	 }

	function clear_old_temp_keys($limit = 10){
		/*$now = date("Y-m-d H:i:s");
		$one_hour_ago = date("Y-m-d H:i:s", time())
		$hour_ago = strtotime('-1 hour'); exempel*/
		$date = new DateTime(date("Y-m-d H:i:s"));
		error_log("new date " . $date->format("Y-m-d H:i:s"));
		$date->sub(new DateInterval('PT1H'));
		$datef =  $date->format('Y-m-d H:i:s');
		error_log("1 Hour ago: $datef");
		$query = "DELETE FROM api_temp_keys WHERE created < '$datef' LIMIT $limit";
		error_log($query);
		$stmt = $this->conn->prepare($query);
		$success = $stmt->execute();
		return $success;
	}
}

