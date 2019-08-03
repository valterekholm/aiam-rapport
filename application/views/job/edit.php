<?php echo validation_errors(); ?>

<?php
    $hidden = array("id" => $job["id"]);
?>
<?php echo form_open('jobs/update','', $hidden); ?>

    <label for="workplace">Arbetsplats</label>
    <?php
    $options = array();
    foreach($workplaces as $workplace){
        $options[$workplace["id"]] = $workplace["namn"];
    }
    //markerad
    $marked = array($job["arb_pl_id"]);

echo form_dropdown('workplace', $options,$marked,"id='workplace'");
echo anchor('workplaces/create', 'Skapa ny', 'title="Om arbetsplats fattas i listan" target="_blank"');//om arbetsplats inte finns i listan
?>

<br>
    <label for="cal_1">Start-tid</label>
    <input type="text" name="start" placeholder="yyyy-mm-dd hh:mm" id='cal_1' autocomplete='off' value="<?=$job['datum_start']?>"><br>

    <label for="cal_2">Slut-tid</label>
    <input type="text" name="end" placeholder="yyyy-mm-dd hh:mm" id='cal_2' autocomplete='off' value="<?=$job['datum_slut']?>"><br>

    <label for="description">Beskrivning</label><br>
    <textarea name="description" id="description"><?=$job["beskrivning"]?></textarea> <br>

    <input type="submit" name="submit" value="Uppdatera" />

</form>

<div>
<strong>Anknuten personal:<br></strong>

<?php

foreach($staff as $person){
    echo "<div class='related_post inner_shadow'>";
    echo "Namn: " . $person["fornamn"] . " " . $person["efternamn"] . "<br>";
    echo "(" . $person["email"] . ")<br>";
    echo anchor('staff/edit/'.base64_encode($person["id"]), 'Redigera person', 'class=""') . "<br>";
    echo anchor('jobs/delete_staff_connection/'.base64_encode($job["id"])."/".base64_encode($person["id"])."/j", 'Ta bort', 'title="Koppla bort från platsen bara"') . "<br>";
    echo "</div>";
}    

if(sizeof($staff) == 0){
    echo "<p>Ingen anknuten personal</p>";
}
?>

    </div>

<?=anchor('jobs/connect_any_staff/'.base64_encode($job["id"]), "Anknyt personal till arbetstillfället") . "<br>"?>


<strong>Anknutet schema:<br></strong>

<?php
if(isset($related_schema)){
	$startDate = new DateTime($job['datum_start']);
	echo "<div class='related_post inner_shadow'>";
	echo $related_schema["schema_kod"] . "<br>";
	echo $schema_phrase . "<br>";
	print_calendar($startDate, array(0 => $job), "month", $repetitions);
	$startDate->modify("+1 month");
	print_calendar($startDate, array(0 => $job), "month", $repetitions);
	echo anchor('jobs/remove_schema/'.base64_encode($job["id"]), 'Ta bort', 'title="Schemat kommer försvinna"') . "<br>";
	echo "</div><br>";
}
else{
	echo "<p>Inget anknutet schema</p>";

	echo anchor('jobs/add_schema/'.base64_encode($job["id"]), "Koppla schema") . "<br>";
}

//schema-code <br>
//schema preview (2 månaders rad)<br>
	//schema 4 första upprepningars datum<br>
?>



<?php
    //var_dump($workplaces);
    $lat = floatval($workplaces[array_search($job["arb_pl_id"], array_column($workplaces,'id'))]["lati"]);//obs ändras inte om select ändras...
    $long = floatval($workplaces[array_search($job["arb_pl_id"], array_column($workplaces,'id'))]["longi"]);
    //$center_of_map_lat = $lat;//59.319178;
    //$center_of_map_long =$long;//18.095856;
    //$zoom_level = 14;
    //$url_map = "https://kartor.eniro.se/?embed=true&c=".$center_of_map_lat.",".$center_of_map_long."&z=".$zoom_level."&g=".$lat.",".$long;
    //echo $url_map."<br>";
?>
<!--iframe width="100%" height="300px" src="<?php echo $url_map; ?>" id="iframe1">
</iframe-->

<div id="map1" style="width: 100%; height: 300px"></div>

<div class="bottom_links">
<?php
    //todo: ta-bort länk
    echo anchor('jobs/delete/'.base64_encode($job["id"]),'Ta bort jobb');
    echo anchor('jobs/view1', 'Åter till arbetstillfällen', 'class=""');
?>
    </div>
    <script>
    var mymap = L.map('map1').setView([<?=$lat?>, <?=$long?>], 13);

    L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token={accessToken}', {
    	attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
	maxZoom: 18,
	id: 'mapbox.streets',
	accessToken: 'pk.eyJ1IjoiYWJkdWxsYWh2YWx0ZXIiLCJhIjoiY2p3ZHYxajRpMTZuMjQ4bW9wdmxvcm90aCJ9.ZX_bUgp9AS7lQDl-PWyDHA'
    }).addTo(mymap);

    var marker = L.marker([<?=$lat?>,<?=$long?>]).addTo(mymap);


    window.onload = function () {
        
        //myCalendar.setSensitiveRange(todays_date,null);
        //doOnLoad(); //calendar
        //myCalendar.setDateFormat("%Y-%m-%d %H:%i");
        
        //myCalendar.setDate("<?=$job['datum_start']?>","<?=$job['datum_start']?>");

    var workplace = document.getElementById("workplace");
    workplace_start_id = workplace.value;
    workplace.addEventListener("change", function(){
        if(this.value != workplace_start_id){
        document.getElementById("iframe1").style.opacity = "0.1";
        }
        else{
            document.getElementById("iframe1").style.opacity = "1";
        }

    });
    }
</script>
