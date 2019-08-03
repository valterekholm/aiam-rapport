<?php echo validation_errors(); ?>

<?php
    $hidden = array("id" => $report["id"]);
?>
<?php echo form_open('reports/update','',$hidden); ?>

    <label for="person">Person</label>
<?php
    $options = array();
    foreach($staff as $person){
        $options[$person["id"]] = $person["fornamn"]." ".$person["efternamn"]." ".$person["personnummer"];
    }

        //markerad
    $marked = array($report["personal_id"]);

echo form_dropdown('person', $options,$marked,"id='person'");
echo anchor('staff/create', 'Registera ny person');
?>
<br>

    <label for="job">Jobb</label>
    <?php
    $options = array();
    foreach($jobs as $job){
        $options[$job["id"]] = $job["arb_plats"]." ".substr($job["datum_start"],0,16)." ".substr($job["beskrivning"],0,20);
    }
    $marked = array($report["jobb_id"]);

echo form_dropdown('job', $options,$marked,"id='job' titel='".$job["beskrivning"]."'");
echo anchor('jobs/create', 'Skapa nytt');
?>

<br>
    <fieldset>
    <label for="cal_1">In-tid</label>
    <input type="text" name="check_in_time" placeholder="yyyy-mm-dd hh:mm" id='cal_1' autocomplete='off' value="<?=$report["check_in_time"]?>"><!--span class="question"></span--><br>

    <label for="check_in_lati">Latitud</label>
    <input type="text" name="check_in_lati" placeholder="11.11111" id="in_lat" value="<?=$report["lati_in"]?>">

    <label for="check_in_longi">Longitud</label>
    <input type="text" name="check_in_longi" placeholder="11.11111" id="in_lon" value="<?=$report["longi_in"]?>"><input type="button" value="Hämta din aktuella pos" id="get_1">
    </fieldset>

    <fieldset>
    <label for="cal_2">Ut-tid</label>
    <input type="text" name="check_out_time" placeholder="yyyy-mm-dd hh:mm" id='cal_2' autocomplete='off' value="<?=$report["check_out_time"]?>"><!--span class="question"></span--><br>

    <label for="check_out_lati">Latitud</label>
    <input type="text" name="check_out_lati" placeholder="11.11111" id="out_lat" value="<?=$report["lati_out"]?>">

    <label for="check_out_longi">Longitud</label>
    <input type="text" name="check_out_longi" placeholder="11.11111" id="out_lon" value="<?=$report["longi_out"]?>"><input type="button" id="get_2" value="Hämta din aktuella pos">
    </fieldset>

    <input type="submit" name="submit" value="Update report record" />
</form>

<?php
//kartor; arbetsplats, check in, check ut - positioner
    


?>
<?php
    echo anchor('reports/index', 'Åter till arbetsrapporter', 'class=""') . "<br>";
?>

<br>
<!--span class="question"></span>
För att välja datum och tid; klicka i text-rutan, välj klockslaget och sen datumet<br>-->
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