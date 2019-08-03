<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


error_log("read.php");

include_once 'database.php';
include_once 'company.php';


$use_full_url = true;


// instantiate database and product object
$database = new Database();
$db = $database->getConnection();
//  
//  // initialize object
$company = new Company($db);

$stmt = $company->read($use_full_url);
$num = $stmt->rowCount();

if($num > 0){
	$comp_arr=array();
	$comp_arr["records"]=array();
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
		extract($row);

		$comp_item = array(
			"id"=>$id,
			"name" => $name,
			"logo_file" => $logo_file
	);
		array_push($comp_arr["records"], $comp_item);
	}
	http_response_code(200);
	if($use_full_url){
		echo json_encode($comp_arr, JSON_UNESCAPED_SLASHES);
	}
	else{
		echo json_encode($comp_arr);
	}
}

else{
	http_response_code(404);
	echo json_encode(
		array("message" => "Nothing found.")
	);
}
