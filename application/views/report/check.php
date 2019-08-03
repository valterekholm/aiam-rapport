<?php echo validation_errors(); ?>
<?php
    $hidden = array("id" => $user_id);
?>

<div class="swappable-forms">
<?php 
$attributes = array('class' => 'check_in_out');
echo form_open('reports/check_no_coords','',$hidden); ?>
<fieldset>
	<label for="person">Person</label>
	<input id="person" type="text" value="<?=$full_name?>">
	<br>
	<label for="check_type">Checkar</label>
	<input type="text" readonly value="<?=$check_type?>" name="check_type" id="check_type">

</fieldset>
<br>
<fieldset>

	<label for="cal_1">Tid</label>
	<input type="text" name="check_time" placeholder="yyyy-mm-dd hh:mm" id='check_time' autocomplete='off' value="<?=date("Y-m-d H:m:d")?>" readonly=""><br>
	<label for="check_name">Plats</label>
	<input type="text" name="check_place" id="check_name">
<?php // <input type="button" value="Hämta" id="get_1"> //fungerade inte i android?>
</fieldset>

<input type="button" value="Gör med position" onClick="swap_2_forms(this.form)">

<?php
if($check_type == "in"){
?>
	<input type="submit" value="Stämpla in" class="stamp_in">
<?php
}
else if($check_type == "out"){ //TODO: Om fältet benamning finns ifyllt, visa då inga koordinat-fält, och fyll i Plats (benamning) fätet från databasen
?>
<input type="hidden" value="<?=$report["id"]?>" name="report_id" id="report">
<input type="submit" value="Stämpla ut" class="stamp_out"> 
<?php                
}
?>
</form>
<?php echo form_open('reports/check','',$hidden); ?>
<fieldset>
<label for="person">Person</label>
<input id="person" type="text" value="<?=$full_name?>">

<br>

<label for="check_type">Checkar</label>
<input type="text" readonly value="<?=$check_type?>" name="check_type" id="check_type">

</fieldset>

<br>
<fieldset>
<label for="cal_1">Tid</label>
<input type="text" name="check_time" placeholder="yyyy-mm-dd hh:mm" id='check_time' autocomplete='off' value="<?=date("Y-m-d H:m:d")?>" readonly=""><br>
<label for="<?=$lat_name?>">Latitud</label>
<input type="text" name="<?=$lat_name?>" placeholder="59.323" id="lat">

<label for="<?=$lon_name?>">Longitud</label>
<input type="text" name="<?=$lon_name?>" placeholder="18.07" id="lon">
<?php // <input type="button" value="Hämta" id="get_1"> //fungerade inte i android?>
</fieldset>
<input type="button" value="Ladda om sida" onclick="location.reload()">
<input type="button" value="Gör utan position" onClick="swap_2_forms(this.form)">

<?php
if($check_type == "in"){
?>
<input type="submit" value="Stämpla in" class="stamp_in">
<?php
}
else if($check_type == "out"){
?>
<input type="hidden" value="<?=$report["id"]?>" name="report_id" id="report">
<input type="submit" value="Stämpla ut" class="stamp_out"> 
<?php                
}
?>
</form>
</div>
<?php
    //echo anchor('reports/index', 'Åter till arbetsrapporter', 'class=""') . "<br>";
?>

<br>

<script>
window.onload = function () {
        //doOnLoad(); //calendar
        //myCalendar.setSensitiveRange(todays_date,null);
        //myCalendar.setDateFormat("%Y-%m-%d %H:%i");

	data_target = [document.getElementById("lat"), document.getElementById("lon")];
	getLocation();

	<?php
	if(!empty($benamning)){
	?>
	swap_2_forms(document.querySelector("form"));//TODO: test och sätt värde för Plats (benamning)
	document.querySelector("#check_name").value = "<?=$benamning?>";
	<?php
	}
	?>
}
</script>
