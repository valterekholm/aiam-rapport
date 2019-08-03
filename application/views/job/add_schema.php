<p>Hej</p>
<p>Här skapar man ett mönster för hur ett arbetstillfälle ska upprepas</p>
<?php

echo validation_errors();
echo form_open('jobschema/add_to_job');
?>
<label for="job">Utgår från jobb</label>
<input value="<?=$job_description?>" disabled>
<input type="hidden" name="job" id="job" value="<?=$job_id?>">

<label for="startDate">Start-date</label>
<input type="text" name="startDate" id="startDate" placeholder="yyyy-mm-dd" value="<?=$start_date?>" readonly>
<fieldset id="weekDaysArea">
<legend>Veckodagar</legend>
<p><input type="checkbox" name="wd1" id="wd1" value="1"><label for="wd1">Måndag</label></p>
<p><input type="checkbox" name="wd2" id="wd2" value="2"><label for="wd2">Tisdag</label></p>
<p><input type="checkbox" name="wd3" id="wd3" value="3"><label for="wd3">Onsdag</label></p>
<p><input type="checkbox" name="wd4" id="wd4" value="4"><label for="wd4">Torsdag</label></p>
<p><input type="checkbox" name="wd5" id="wd5" value="5"><label for="wd5">Fredag</label></p>
<p><input type="checkbox" name="wd6" id="wd6" value="6"><label for="wd6">Lördag</label></p>
<p><input type="checkbox" name="wd7" id="wd7" value="7"><label for="wd7">Söndag</label></p>
</fieldset>
<input type="hidden" name="weekDays" id="weekDays" title="use numbers 1-7 for mond-sund">
<label for="di">Day interval</label>
<select name="di" id="di">
<option value=0></option>
<option value=1>Varje dag</option>
<option value=2>Varannan dag</option>
<option value=3>Var tredje dag</option>
<option value=4>Var fjärde dag</option>
<option value=5>Var femte dag</option>
<option value=6>Var sjätte dag</option>
<option value=7>Var sjunde dag</option>
<option value=8>Var åttonde dag</option>
<option value=9>Var nionde dag</option>
<option value=10>Var tionde dag</option>
<option value=11>Var elfte dag</option>
<option value=12>Var tolfte dag</option>
<option value=13>Var trettonde dag</option>
<option value=14>Var fjortonde dag</option>
</select>

<label for="wi">Week interval</label>
<select name="wi" id="wi">
<option value=0></option>
<option value=1>Varje vecka</option>
<option value=2>Varannan vecka</option>
<option value=3>Var tredje vecka</option>
<option value=4>Var fjärde vecka</option>
<option value=5>Var femte vecka</option>
<option value=6>Var sjätte vecka</option>
<option value=7>Var sjunde vecka</option>
</select>
<label for="mi">Month interval</label>
<select name="mi" id="mi">
<option value=0></option>
<option value=1>Varje månad</option>
<option value=2>Varannan månad</option>
<option value=3>Var tredje månad</option>
<option value=4>Var fjärde månad</option>
<option value=5>Var femte månad</option>
<option value=6>Var sjätte månad</option>
<option value=7>Var sjunde månad</option>
</select>

<input type="submit">
</form>

<div id="schedule_preview">
</div>

<script>

window.onload = function(){

	var targ_preview = document.querySelector("#schedule_preview");

	var wd = document.querySelector("#weekDays");
	var di = document.querySelector("#di");
	var wdArea = document.querySelector("#weekDaysArea");
	var wdChecks = wdArea.getElementsByTagName("input");
	console.log(wdChecks);
	var wdLen = wdChecks.length;
	console.log("wdLen: " + wdLen);

	di.addEventListener("change", function(){/*this clears weekdays*/
		if(this.value.length > 0){
			console.log("ch");
			clearWeekDays(wdChecks, wd);
		}
	});


	for(var i=0; i<wdLen; i++){
		var chb = wdChecks[i];
		console.log(chb);
		chb.addEventListener("change", function(){
			console.log(this.checked);
			wd.value = allWeekdaysToString(wdChecks);
			/*reset day interval*/
			di.selectedIndex=0;
		});
	}

}

function allWeekdaysToString(weekDaysCh){
	var wdLen = weekDaysCh.length;
	var allEmpty = true;
	var result = "";
	for(var i=0; i<wdLen; i++){
		var chb = weekDaysCh[i];
		if(chb.checked){
			allEmpty = false;
			result += (chb.value);
		}
	}
	return result;
}

function clearWeekDays(checks, wds){
	var len = checks.length;
	for(var i=0; i<len; i++){
		checks[i].checked=false;
	}
	wds.value = "";
}


</script>
