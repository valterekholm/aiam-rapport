<?php
date_default_timezone_set('Europe/Stockholm');

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

error_log("post_data");

error_log(print_r($_POST, true));

if(isset($_POST["key"]) && isset($_POST["temp_key"])){


	$key = $_POST["key"];
	$temp_key = $_POST["temp_key"];

	include_once 'database.php';
	include_once 'apikey.php';
	include_once 'company.php';
	include_once 'report.php';
	//TODO: controll keys
	error_log("Got key $key & temp_key $temp_key");


	$database = new Database();
	$db = $database->getConnection();

	$apikey = new Apikey($db);
	if(! $apikey->temp_key_auth($key, $temp_key)){
		http_response_code(401);
		echo json_encode(
			array("message" => "No access")
		);
		exit();

	}
	$user = $apikey->key_auth_get_id($key);

	if(!$user){
		http_response_code(401);
		$resp = array("message" => "No user");
		echo json_encode($resp);
		exit();
	}


	if(isset($_POST["direction"])){//TODO: skip what_info
		/*action, key, tempKey, whatInfo, reportId, lat, lon, benamning*/

		$report = new Report($db);

		$direction = $_POST["direction"];

		if(isset($_POST["lat"]) && isset($_POST["lon"])){
			$lat = $_POST["lat"];
			$lon = $_POST["lon"];
		}
		else{
			$lat = "NULL";
			$lon = "NULL";
		}//test

		if(isset($_POST["benamning"])){
			$benamning = $_POST["benamning"];
		}
		else{
			$benamning = "NULL";
		}

		if(isset($_POST["time"])){
			$time = $_POST["time"];
		}
		else{
			$time = date("Y-m-d H:i:s");
		}



		if($direction == "in"){
			error_log("dir in");
			//$sql = "INSERT INTO arbets_rapport (personal_id, arbetstillfalle_id, check_in_time, longi_in, lati_in, check_out_time, longi_out, lati_out, benamning) VALUES ($user, NULL, '$time', $lon, $lat, NULL, NULL, NULL, '$benamning')";
			//error_log($sql);
			$newid = $report->check_in($user, $time, $lon, $lat, $benamning);

			if($newid){
				http_response_code(200);
				$resp = array("message" => "Success, new id $newid");
			}
			else{
				http_response_code(500);
				$resp = array("message" => "Failed to insert");
			}

			echo json_encode($resp);
			exit();

		}
		else if($direction == "out"){
			error_log("php now: " . date("Y-m-d H:i:s"));
			error_log(print_r($_POST, true));
			if(isset($_POST["report_id"])){
				$report_id = $_POST["report_id"];
			}
			else{
				http_response_code(401);
				$resp = array("message" => "Missing report info");
				echo json_encode($resp);
				exit();
			}
			error_log("dir out");
			$break = 0;
			if(isset($_POST["break_minutes"]) ){
				$br = $_POST["break_minutes"];
				if(intval($br)>0){
					$break = intval($br);
				}
			}//TODO: check that break is less or equal to working time

			$success = $report->check_out($user, $report_id, $time, $lon, $lat, $benamning, $break);

			/*TODO: don't replace benamning if exists, append*/
			if($success){
				http_response_code(200);
				$resp = array("message" => "You checked out");
			}
			else{
				http_response_code(500);
				$resp = array("message" => "Failed to check out");
			}
			echo json_encode($resp);
			exit();

		}

	}
	else{
		http_response_code(404);
		$resp = array("message" => "No direction");
		echo json_encode($resp);
		exit();
	}


	http_response_code(404);
	$resp = array("message" => "Code not complete");
	echo json_encode($resp);
}
