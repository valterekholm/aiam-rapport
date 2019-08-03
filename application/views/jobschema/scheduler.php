<!DOCTYPE html>
<html>
<head>
<style>
table{
margin: 20px;
}
table, td{
border: 1px solid gray;
}
td{
/*height: 100px;*/
white-space: nowrap;
}
td > div {
  transform: 
    /* Magic Numbers */
    /*translate(15px, 51px)*/
    /* 45 is really 360 - 45 */
    rotate(315deg);
  width: 30px;
  margin-bottom: 5px;
}
td > div > span {
  /*border-bottom: 1px solid #ccc;*/
  padding: 1px;
  background-color: rgba(255,255,255,.3);
  border-radius: 4px;
}
.event_cell{
background: #339999;
}
</style>
</head>
<body>

<?php
include "calendar_print.php";
?>

<h1>Scheduler</h1>
<?php

$di =2;
$wi =2;
$repetitions = 15;

echo "<h3>Settings:</h3>";
echo "day interval: $di<br>";
echo "week interval: $wi<br>";

$startDate = new DateTime('2018-12-15');

$weeksRunner = clone $startDate;
$weeksSerie = array();

for($i=0; $i<$repetitions; $i++){
	//Weeks to come according to wi
	$weeksSerie[] = $weeksRunner->format("Y W");
	$weeksRunner->modify("+$wi week");
}

echo "<pre>Weeks serie: \n " . print_r($weeksSerie, true) . "\n";

$dayRunner = clone $startDate;
$daysSerie = array();
$result = array();

for($i=0; $i<$repetitions; $i++){
	//Days to come according to di
	$daysSerie[] = $dayRunner->format("Y-m-d");
	$dayRunner->modify("+$di day");
}

echo "Days serie: \n " . print_r($daysSerie, true) . "\n";


foreach($daysSerie as $date){//strings
	$dTest = new DateTime($date);
	$test = $dTest->format("Y W");
	if(in_array($test, $weeksSerie)){
		echo $date . ", ";
		$result[] = $date;
	}
}

print_dates_stripe($result, $startDate);
//$result=array_intersect($weeksSerie,$daysSerie);
?>
</body>
</html>
