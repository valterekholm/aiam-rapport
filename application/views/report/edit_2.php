<?php echo validation_errors(); ?>
<div class="form_holder">
<p><i>OBS, ifylld rapport</i></p>
<?php
$hidden = array("id" => $report["id"], "person" => $report["personal_id"]);
?>
<?php echo form_open('reports/update','',$hidden); ?>

    <label for="person" class="center">Person</label>
<?php
    //$options = array();
    foreach($staff as $person){
        //$options[$person["id"]] = $person["fornamn"]." ".$person["efternamn"]." ".$person["personnummer"];

        if($person["id"] == $report["personal_id"]){
            echo $person["fornamn"]." ".$person["efternamn"]." ".$person["personnummer"];
        }
    }

    //markerad
    //$marked = array($report["personal_id"]);

    //echo form_dropdown('person', $options,$marked,"id='person'");
    //echo $staff["fornamn"] . " " . $person["efternamn"] . "<br>";
    //echo anchor('staff/create', 'Registera ny person');
?>


<br>
    <fieldset>
    <label for="cal_1" class="center">In-tid</label>
    <input type="text" name="check_in_time" placeholder="yyyy-mm-dd hh:mm" id='cal_1' autocomplete='off' value="<?=$report["ch_i"]?>"><!--span class="question"></span--><br>

    <label for="check_in_lati" class="center">Latitud</label>
    <input type="text" name="check_in_lati" placeholder="11.11111" id="in_lat" value="<?=$report["lati_in"]?>">

    <label for="check_in_longi" class="center">Longitud</label>
    <input type="text" name="check_in_longi" placeholder="11.11111" id="in_lon" value="<?=$report["longi_in"]?>"><input type="button" value="Hämta din aktuella pos" id="get_1"  style="display: none">
    </fieldset>

    <fieldset>
    <label for="cal_2" class="center">Ut-tid</label>
    <input type="text" name="check_out_time" placeholder="yyyy-mm-dd hh:mm" id='cal_2' autocomplete='off' value="<?=$report["ch_o"]?>"><!--span class="question"></span--><br>

    <label for="check_out_lati" class="center">Latitud</label>
    <input type="text" name="check_out_lati" placeholder="11.11111" id="out_lat" value="<?=$report["lati_out"]?>">

    <label for="check_out_longi" class="center">Longitud</label>
    <input type="text" name="check_out_longi" placeholder="11.11111" id="out_lon" value="<?=$report["longi_out"]?>"><input type="button" id="get_2" value="Hämta din aktuella pos" style="display: none">
    </fieldset>

	<fieldset>
	<label for="rast_m" class="center">Varav rast</label>
	<input type="number" name="rast_m" id="rast_m" value="<?=$report["varav_rast"]?>" min="0" max="<?=$worked_minutes?>"><br>
	<label for="benamning" class="center">Benämning på plats</label>
        <input type="text" name="benamning" id="benamning" value="<?=$report["benamning"]?>">
	
	</fieldset>

    <input type="submit" name="submit" value="Uppdatera" />
</form>

    <?php
        echo anchor("reports/delete_row/".$report["id"], "Ta bort") . "<br>";
    ?>

<?php
//kartor; arbetsplats, check in, check ut - positioner
    $lat = floatval($report["lati_in"]);//obs ändras inte om select ändras...
    $long = floatval($report["longi_in"]);
    $center_of_map_lat = $lat;//59.319178;
    $center_of_map_long =$long;//18.095856;
    $zoom_level = 14;
    $url_map = "https://kartor.eniro.se/?embed=true&c=".$center_of_map_lat.",".$center_of_map_long."&z=".$zoom_level."&g=".$lat.",".$long;
    echo "<br>";

    echo "<p>Check in plats <span class='vague'>($url_map)</span>:</p>";
    ?>

<iframe style="display: inline-block" width="450px" height="350px" src="<?php echo $url_map; ?>" id="iframe1">
</iframe><br>

<?php
    $lat = floatval($report["lati_out"]);//obs ändras inte om select ändras...
    $long = floatval($report["longi_out"]);
    $center_of_map_lat = $lat;//59.319178;
    $center_of_map_long =$long;//18.095856;
    $zoom_level = 14;
    $url_map = "https://kartor.eniro.se/?embed=true&c=".$center_of_map_lat.",".$center_of_map_long."&z=".$zoom_level."&g=".$lat.",".$long;
    echo "<br>";
?>
<p>Check ut plats <span class="vague">(<?=$url_map?>)</span>:</p>
<iframe style="display: inline-block" width="450px" height="350px" src="<?php echo $url_map; ?>" id="iframe2">
</iframe>
<br>

<?php
    echo anchor('reports/index', 'Åter till arbetsrapporter', 'class=""') . "<br>";
?>

<br>
<!--span class="question"></span>
För att välja datum och tid; klicka i text-rutan, välj klockslaget och sen datumet<br>-->

</div>
<script>
    window.onload = function () {
        //doOnLoad(); //calendar
        //myCalendar.setSensitiveRange(todays_date,null);
        //myCalendar.setDateFormat("%Y-%m-%d %H:%i");

        document.getElementById("get_1").addEventListener("click", function () {
            data_target = [document.getElementById("in_lat"), document.getElementById("in_lon")];
            getLocation();
        });
        document.getElementById("get_2").addEventListener("click", function () {
            data_target = [document.getElementById("out_lat"),document.getElementById("out_lon")];
            getLocation();
        });
    }
</script>
