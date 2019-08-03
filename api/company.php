<?php
class Company{
	private $conn;
	private $table_name = "company_info";

	public $id;
	public $name;
	public $gatuadress;
	public $postnummer;
	public $telefon;
	public $email;
	public $cctld;
	public $log_save_days;
	public $controlled_mode;
	public $logo_file;
	public $time_zone;

	public function __construct($db){
		$this->conn = $db;
	}
	function read(){

		$query = "SELECT * FROM $table_name";

		error_log($query);
		$stmt = $this->conn->prepare($query);
		$stmt->execute();
		return $stmt;
	}

	function read_by_id($id){
		$query = "SELECT * FROM $table_name WHERE id = $id";
		error_log($query);
		$stmt = $this->conn->prepare($query);
		$stmt->execute();
		return $stmt;
	}

}
