<?php
class Report{
	private $conn;
	private $table_name = "arbets_rapport";

	public $id;
	public $personal_id;
	public $arbetstillfalle_id;
	public $check_in_time;
	public $longi_in;
	public $lati_in;
	public $check_out_time;
	public $longi_out;
	public $lati_out;
	public $benamning;
	public $rast_minuter;

	public function __construct($db){
		$this->conn = $db;
		date_default_timezone_set('Europe/Stockholm');//TODO: get city from company_info
	}
	function read(){

		$query = "SELECT id, personal_id, arbetstillfalle_id, check_in_time, longi_in, lati_in, check_out_time, longi_out, lati_out, benamning, rast_minuter FROM $table_name";

		error_log($query);
		$stmt = $this->conn->prepare($query);
		$stmt->execute();
		return $stmt;
	}

	function read_by_id($id){
		$query = "SELECT id, personal_id, arbetstillfalle_id, check_in_time, longi_in, lati_in, check_out_time, longi_out, lati_out, benamning, rast_minuter FROM $table_name WHERE id = $id";
		error_log($query);
		$stmt = $this->conn->prepare($query);
		$stmt->execute();
		return $stmt;
	}

	function get_description($id){
		$query = "SELECT benamning FROM arbets_rapport WHERE id = $id";
		$stmt = $this->conn->prepare($query);
		$stmt->execute();
		$row = $stmt->fetch();
		return $row["benamning"];
	}

        function get_last_completed($pers_id){
                $query = "SELECT * FROM arbets_rapport WHERE personal_id = $pers_id ORDER BY check_out_time DESC LIMIT 1";
                $stmt = $this->conn->prepare($query);
                $stmt->execute();
                return $stmt;
        }

        function is_report_unfinished($pers_id){
                error_log("is_report_unfinished");
                $query = "SELECT * FROM arbets_rapport WHERE personal_id = $pers_id AND check_out_time IS NULL";
                error_log($query);
                $stmt = $this->conn->prepare($query);
                $stmt->execute();
                error_log("rowCount " . $stmt->rowCount());
                return $stmt->rowCount()>0;
        }

        function get_unfinished($pers_id){
                $query = "SELECT * FROM arbets_rapport WHERE personal_id = $pers_id AND check_out_time IS NULL";
                $stmt = $this->conn->prepare($query);
                $stmt->execute();
                return $stmt;
        }

        /*POST*/
	function check_in($pers_id, $user_clock, $longi_in, $lati_in, $benamning, $arbetstillfalle_id=0){
		$query = "INSERT INTO arbets_rapport (personal_id,
			arbetstillfalle_id,
			check_in_time,
			longi_in,
			lati_in,
			benamning,
			server_in_time
		) VALUES ($pers_id, $arbetstillfalle_id, '$user_clock', $longi_in, $lati_in, '$benamning', NOW())";
		error_log("check_in query: $query");
		$stmt = $this->conn->prepare($query);
		$stmt->execute();
		$rowCount = $stmt->rowCount();
		if($rowCount == 1)
			$new_id = $stmt->insert_id;
		else
			$new_id = false;
		error_log("check in, rowCount " . $rowCount);
		return $new_id;
	}
	function check_out($pers_id, $report_id, $user_clock, $longi_out, $lati_out, $benamning, $rast_min, $arbetstillfalle_id=0){

		//hämta nuvarande benämning

		$ben = $this->get_description($report_id);

		if($benamning != ""){
			if($ben != ""){
				$benamning = $ben . " ... " . $benamning;
			}
		}
		else{
			$benamning = $ben;
		}



		$query = "UPDATE arbets_rapport
			SET arbetstillfalle_id = $arbetstillfalle_id,
			check_out_time = '$user_clock',
			longi_out = $longi_out,
			lati_out = $lati_out,
			benamning = '$benamning',
			rast_minuter = $rast_min,
			server_out_time = NOW()
			WHERE id = $report_id
			AND personal_id = $pers_id";
		error_log("check_out query: $query");
		$stmt = $this->conn->prepare($query);
		$stmt->execute();
		$rowCount = $stmt->rowCount();
		error_log("check out, rowCount " . $rowCount);
		return $rowCount == 1;
	}

}
