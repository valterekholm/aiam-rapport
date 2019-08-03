<?php
//OBS inkluderas av filen download_excel_direct.php
//filen blir fel på, kan inte öppnas
/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/Stockholm');
//databas
$DB_Server = "localhost"; //MySQL Server    
$DB_Username = "root"; //MySQL Username     
$DB_Password = "root";             //MySQL Password     
$DB_DBName = "gps_in_rapport";         //MySQL Database Name  
$DB_TBLName = "arbets_rapport"; //MySQL Table Name   
$filename = "arbetrapport";         //File Name
/*******YOU DO NOT NEED TO EDIT ANYTHING BELOW THIS LINE*******/    
//create MySQL connection   
$sql = "Select * from $DB_TBLName";
//$Connect = @mysqli_connect($DB_Server, $DB_Username, $DB_Password, $DB_TBLName) or die("Couldn't connect to MySQL:<br>" . mysqli_error() . "<br>" . mysql_errno());
$conn = new mysqli($DB_Server, $DB_Username, $DB_Password, $DB_DBName);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
//execute query 
$result = $conn->query($sql);
define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
    $dir = "../../..";
    $a = scandir($dir);
    print_r($a);
/** Include PHPExcel */
require_once '../../../PHPExcel/Classes/PHPExcel.php';
//min cell width
$min_cell_width = 15;
//Create new PHPExcel object
//echo date('H:i:s') , " Create new PHPExcel object" , EOL;
$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator("")
							 ->setLastModifiedBy("Ekholm")
							 ->setTitle("PHPExcel Test Document")
							 ->setSubject("PHPExcel Test Document")
							 ->setDescription("Test document for PHPExcel, generated using PHP classes.")
							 ->setKeywords("office PHPExcel php")
							 ->setCategory("Test result file");
//skriv data
if ($result->num_rows > 0) {
    $letters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";//obs max 26 kolumner
    //headers
    $row1 = $result->fetch_array(MYSQLI_ASSOC);
    $keys = array_keys($row1);
    $cou=0;
    foreach($keys as $key){
        $txt = $letters[$cou]."1";
        //echo "Ska sätta värde $key i cell $txt<br>";
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($txt, $key);
        $cou++;
    }
    $result->data_seek(0);//börja om nu med innehåll
    // output data of each row
    // börja på excel rad 2
    $count=2;
    while($row = $result->fetch_assoc()) {
        $keys = array_keys($row);
        $lengths = array();
        foreach($row as $col){
                $lengths[] = strlen($col);
        }
        $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A'.$count, $row["personal_id"])
        ->setCellValue('B'.$count, $row["arbetstillfalle_id"])
        ->setCellValue('C'.$count, $row["check_in_time"])
        ->setCellValue('D'.$count, $row["lati_in"])
        ->setCellValue('E'.$count, $row["longi_in"])
        ->setCellValue('F'.$count, $row["check_out_time"])
        ->setCellValue('G'.$count, $row["lati_out"])
        ->setCellValue('H'.$count, $row["longi_out"])
        ->setCellValue('I'.$count, $row["id"]);
        $count++;
    }//slut while row
$objPHPExcel->getActiveSheet()->setTitle('Arbets_rapporter');
//so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
}
else{
    echo "Tabellen var tom";
}

?>