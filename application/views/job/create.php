
<?php

echo validation_errors();

echo form_open('jobs/create');
?>

    <label for="workplace">Arbetsplats</label>
    <?php
$options = array();
    foreach($workplaces as $workplace){
        $options[$workplace["id"]] = $workplace["namn"];
    }

echo form_dropdown('workplace', $options,"","id='workplace'");
echo anchor('workplaces/create', 'Skapa ny', 'title="Om arbetsplats saknas i listan" id="cwp"');
?>

<br>


    <label for="cal_1">Start-tid</label>
    <input type="text" name="start" placeholder="yyyy-mm-dd" id='cal_1' autocomplete='off'><span class="question"></span><br>

    <label for="cal_2">Slut-tid</label>
    <input type="text" name="end" placeholder="yyyy-mm-dd" id='cal_2' autocomplete='off'><span class="question"></span><br>

    <label for="description">Beskrivning</label><br>
    <textarea name="description" id="description"></textarea> <br>

    <input type="submit" name="submit" value="Spara" />

</form>
<?php
    echo anchor('jobs/view1', 'Åter till arbetstillfällen', 'class=""') . "<br>";
?>

<br>
<span class="question"></span>&nbsp;
För att välja datum och tid;<ul><li>klicka i text-rutan</li><li>välj klockslaget</li><li>välj sen datumet</li></ul>
<br>
<script>
    window.onload = function () {
        doOnLoad(); //calendar
        //myCalendar.setSensitiveRange(todays_date,null);
	myCalendar.setDateFormat("%Y-%m-%d %H:%i");

	var workplace = document.querySelector("#workplace");
	var linkCreateWp = document.querySelector("#cwp");

	linkCreateWp.addEventListener("click", function(){
		console.log("Add workplace clicked");
	});

    }
</script>
