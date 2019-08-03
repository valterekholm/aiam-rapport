<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '/var/www/example.com/public_html/jobb_rapport/api/database.php';//'database.php';
include_once 'apikey.php';
require_once '/var/www/example.com/public_html/jobb_rapport/vendor/autoload.php';//'../vendor/autoload.php';

use Zend\Math\Rand;



if(isset($_GET["my_key"]) && isset($_GET["my_id"])){

	$my_id = $_GET["my_id"];
	$my_key = $_GET["my_key"];

	$database = new Database();
	$db = $database->getConnection();

	$apikey = new Apikey($db);

	if(! $apikey->key_auth($my_id, $my_key)){
		http_response_code(401);
		echo json_encode(
			array("message" => "No access")
		);
		exit();

	}

	$string = Rand::getString(60, "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_");
	//error_log("Zend Math Rand: $string");

	$stmt = $apikey->make_temp_key($my_id, $string);

	if($stmt){
		$key_arr = array("temp_key"=>$stmt);
	
		http_response_code(200);
		echo json_encode($key_arr);

	}
	else{
		http_response_code(404);
		echo json_encode(
			array("message" => "No found.")
		);
		exit();
	}



}

?>
