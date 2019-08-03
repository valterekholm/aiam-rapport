<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


error_log("get_data.php");

include_once 'database.php';
include_once 'apikey.php';
include_once 'report.php';



if(isset($_GET["my_key"]) && isset($_GET["my_temp_key"])){

	$my_key = $_GET["my_key"];
	$temp_key = $_GET["my_temp_key"];

	$database = new Database();
	$db = $database->getConnection();
	//check temp key
	$apikey = new Apikey($db);

	if(! $apikey->temp_key_auth($my_key, $temp_key)){
		http_response_code(401);
		echo json_encode(
			array("message" => "No access")
		);
		exit();

	}
	$user = $apikey->key_auth_get_id($my_key);

	
	//okej
	//All requests about reports must have the word 'report' in "what_info"
	if(isset($_GET["what_info"])){

		$what_info = $_GET["what_info"];

		if(strstr($what_info, "report")){
			/*about reports*/
			$report = new Report($db);
			$report_arr = array();
			$report_arr["records"] = array();

		}

		switch($what_info){
		case 'my_last_report':
			/*get last completed report*/
			$stmt = $report->get_last_completed($user);
			$num = $stmt->rowCount();
			if($num > 0){
				while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
					extract($row);
					/* public $id;
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
					 */
					$report_item = array(
						"id"=>$id,
						"personal_id" => $personal_id,
						"arbetstillfalle_id" => $arbetstillfalle_id,
						"check_in_time" => $check_in_time,
						"longi_in" => $longi_in,
						"lati_in" => $lati_in,
						"check_out_time" => $check_out_time,
						"longi_out" => $longi_out,
						"lati_out" => $lati_out,
						"benamning" => $benamning,
						"rast_minuter" => $rast_minuter
					);
					array_push($report_arr["records"], $report_item);
				}
				http_response_code(200);
				echo json_encode($report_arr);
				exit();
			}
			else{
				http_response_code(404);
				echo json_encode(
					array("message" => "No report found.")
				);
				exit();
			}

			break;

		case 'is_report_unfinished':
			/*if any*/
			$response = $report->is_report_unfinished($user);
			error_log("Got response : $response");
			http_response_code(200);
			if($response == TRUE){
				echo json_encode(
					array("is_report_unfinished" => "yes")
				);
			}
			else{
				echo json_encode(
					array("is_report_unfinished" => "no")
				);
			}
			break;
		case 'my_unfinished_report':
			$stmt = $report->get_unfinished($user);
			$num = $stmt->rowCount();
			if($num > 0){
				if($num > 1){
					error_log("Strange; $num unfinished reports for user $user");
				}
				while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
					extract($row);
					$report_item = array(
						"id"=>$id,
						"personal_id" => $personal_id,
						"arbetstillfalle_id" => $arbetstillfalle_id,
						"check_in_time" => $check_in_time,
						"longi_in" => $longi_in,
						"lati_in" => $lati_in,
						"benamning" => $benamning
					);
					array_push($report_arr["records"], $report_item);
				}
				http_response_code(200);
				echo json_encode($report_arr);
				exit();
			}
			else{
				http_response_code(404);
				echo json_encode(
					array("message" => "No report found.")
				);
				exit();
			}


			break;
		}

	}
}
