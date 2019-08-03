<script type="text/javascript" src="<?=base_url()?>js/Script.js"></script>

<?php echo validation_errors(); ?>

<?php echo form_open('reports/create'); ?>

    <label for="person">Person</label>
<?php
    $options = array();
    foreach($staff as $person){
        $options[$person["id"]] = $person["fornamn"]." ".$person["efternamn"]." ".$person["personnummer"];
    }

echo form_dropdown('person', $options,"","id='person'");
echo anchor('staff/create', 'Registera ny person');
?>
<br>

    <label for="job">Jobb</label>
    <?php
    $options = array();
    foreach($jobs as $job){
        $options[$job["id"]] = $job["arb_plats"]." ".substr($job["datum_start"],0,16)." ".substr($job["beskrivning"],0,20);
    }

echo form_dropdown('job', $options,"","id='job' titel='".$job["beskrivning"]."'");
echo anchor('jobs/create', 'Skapa nytt');
?>

<br>
    <fieldset>
    <label for="cal_1">In-tid</label>
    <input type="text" name="check_in_time" placeholder="yyyy-mm-dd hh:mm" id='cal_1' autocomplete='off'><span class="question"></span><br>

    <label for="check_in_lati">Latitud</label>
    <input type="text" name="check_in_lati" placeholder="11.11111" id="in_lat">

    <label for="check_in_longi">Longitud</label>
    <input type="text" name="check_in_longi" placeholder="11.11111" id="in_lon"><input type="button" value="Hämta" id="get_1">
    </fieldset>

    <fieldset>
    <label for="cal_2">Ut-tid</label>
    <input type="text" name="check_out_time" placeholder="yyyy-mm-dd hh:mm" id='cal_2' autocomplete='off'><span class="question"></span><br>

    <label for="check_out_lati">Latitud</label>
    <input type="text" name="check_out_lati" placeholder="11.11111" id="out_lat">

    <label for="check_out_longi">Longitud</label>
    <input type="text" name="check_out_longi" placeholder="11.11111" id="out_lon"><input type="button" id="get_2" value="Hämta">
    </fieldset>

    <input type="submit" name="submit" value="Create report record" />
</form>
<?php
    echo anchor('reports/index', 'Åter till arbetsrapporter', 'class=""') . "<br>";
?>

<br>
<span class="question"></span>
För att välja datum och tid; klicka i text-rutan, välj klockslaget och sen datumet
<br>
<script>
    window.onload = function () {
        doOnLoad(); //calendar
        //myCalendar.setSensitiveRange(todays_date,null);
        myCalendar.setDateFormat("%Y-%m-%d %H:%i");

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
