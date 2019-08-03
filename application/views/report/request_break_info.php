<?php echo validation_errors(); ?>
<?php
    $hidden = array("id" => $user_id);
?>

<div class="swappable-forms">
<?php echo form_open('reports/undo_check_out','',$hidden); ?>

<input type="button" value="Fortsätt med med utstämpling" onClick="swap_2_forms(this.form)">

<input type="submit" value="Ångra utstämpling" class="stamp_undo">
<input type="hidden" value="<?=$report_id?>" name="report_id" id="report">
</form>
<?php echo form_open('reports/add_break_info','',$hidden); ?>
<fieldset>
<label for="person">Person</label>
<input id="person" type="text" value="<?=$full_name?>">

<br>
<p>Ditt arbetspass var <?=$worked_minutes?> minuter</p>
</fieldset>

<br>
<fieldset>
<label for="break_time">Ange minuter</label>
<input type="number" min="0" max="<?=$worked_minutes?>" name="break_time" placeholder="15" id='break_time' autocomplete='off' value="<?php echo set_value('break_time'); ?>"> <input type="submit" value="Rapportera rast minuter" class="stamp_break"> 
</fieldset>
<!--input type="button" value="Ångra utstämpling" onClick="swap_2_forms(this.form)"-->
<input type="button" value="0 minuters rast" onClick="alert('Utstämpling klar');window.location.replace('<?=site_url('users/logout')?>')">
<input type="hidden" value="<?=$report_id?>" name="report_id" id="report">
</form>
</div>
<?php
    echo anchor('reports/index', 'Åter till arbetsrapporter', 'class=""') . "<br>";
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
