<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Test med excel</title>
    </head>
    <body>

<?php

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


// Create new PHPExcel object
echo date('H:i:s') , " Create new PHPExcel object" , EOL;
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
        echo "Ska sätta värde $key i cell $txt<br>";
        $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue($txt, $key);
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
                echo "Hämtar length från $col<br>";
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

//gör rubrik högre
//$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(-1);

    // Rename worksheet
echo date('H:i:s') , " Rename worksheet" , EOL;
$objPHPExcel->getActiveSheet()->setTitle('Arbets_rapporter');



//so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Save Excel 2007 file
echo date('H:i:s') , " Write to Excel2007 format" , EOL;
$callStartTime = microtime(true);

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
$callEndTime = microtime(true);
$callTime = $callEndTime - $callStartTime;

echo date('H:i:s') , " File written to " , str_replace('.php', '.xlsx', pathinfo(__FILE__, PATHINFO_BASENAME)) , EOL;
echo 'Call time to write Workbook was ' , sprintf('%.4f',$callTime) , " seconds" , EOL;
// Echo memory usage
echo date('H:i:s') , ' Current memory usage: ' , (memory_get_usage(true) / 1024 / 1024) , " MB" , EOL;

// Save Excel 95 file
echo date('H:i:s') , " Write to Excel5 format" , EOL;
$callStartTime = microtime(true);

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save(str_replace('.php', '.xls', __FILE__));
$callEndTime = microtime(true);
$callTime = $callEndTime - $callStartTime;

//namn
$name = str_replace('.php', '.xlsx', pathinfo(__FILE__, PATHINFO_BASENAME));//Excel 2007

echo date('H:i:s') , " File written to " , str_replace('.php', '.xls', pathinfo(__FILE__, PATHINFO_BASENAME)) , EOL;
echo 'Call time to write Workbook was ' , sprintf('%.4f',$callTime) , " seconds" , EOL;
// Echo memory usage
echo date('H:i:s') , ' Current memory usage: ' , (memory_get_usage(true) / 1024 / 1024) , " MB" , EOL;


// Echo memory peak usage
echo date('H:i:s') , " Peak memory usage: " , (memory_get_peak_usage(true) / 1024 / 1024) , " MB" , EOL;

// Echo done
echo date('H:i:s') , " Done writing files" , EOL;
echo 'Files have been created in ' , getcwd() , EOL;
echo "<br>";
echo "<a href='$name'>Ladda ner</a>";



}
else{
    echo "Tabellen var tom";
}

?>

    </body>
</html>