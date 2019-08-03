<?php

//args: events_dates: array of strings like '2019-01-01'
//start_date: a DateTime obj
function print_dates_stripe($events_dates, $start_date){
	echo "print_dates_stripe with " . print_r($events_dates, true);
	$count_printed = 0;

	//remove any passed events
	$events_printable = array();
	foreach($events_dates as $ed){
		$edt = new DateTime($ed);
		if($edt >= $start_date){
			$events_printable[] = $edt->format("Y-m-d");
		}
		else{
			echo "date before start_date found";
		}
	}
	if(count($events_printable) == 0){
		echo "no events printable";
		return 0;
	}

	$dateWalker = clone $start_date;
	echo "<table><tr>";
	do{	
		echo "<td";
		if(in_array($dateWalker->format("Y-m-d"), $events_printable)){
			echo " class='event_cell'";
			$count_printed++;
		}
		echo "><div><span>" . $dateWalker->format("Y-m-d") . "</span></div></td>";
		$dateWalker->modify("+1 day");
	}while($count_printed < count($events_printable));
	
}

