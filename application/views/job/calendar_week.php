<?php

echo anchor('jobs/create', 'Skapa nytt', 'title="Registrera arbetstillfälle" target="_blank"');
error_log("Ska skriva kalender från datum " . $from_date->format("Y-m-d"));
if(isset($repetitions)){
	print_calendar($from_date, $jobs, "week", $repetitions);
}
else{
	print_calendar($from_date, $jobs, "week");
}

//Todo: for bookings, show event(s) info, offer full day view




$calendar->printCal();
?>


<div class="info_block">Info</div>

<pre>
<?php
if(isset($repetitions)){
	//print_r($repetitions, true);
}
?>
</pre>

<script>

window.onload = function(){

	var cal = document.querySelector("div.calendar").querySelector("table");
	console.log(cal);

	var cells = cal.getElementsByTagName("td");

	var len = cells.length;

	for(var i=0; i<len; i++){
		if(cells[i].className.indexOf('booked') >= 0){
			var c = cells[i];
			var jids = c.dataset.jobs;
			/*admin 1,2: jobs/edit/MTY=*/
			/*admin 3: jobs/view/MTE=*/

			c.addEventListener("click", function(){
				cellClicked(this, "<?=site_url('jobs/get_staff_json')?>", "<?=$jobUrl?>");
			});
		}
	}
}

</script>

