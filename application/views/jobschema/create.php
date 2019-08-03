<?=$chain_table?>
<?php echo validation_errors(); ?>
<div class="form_holder">
<?php echo form_open('jobschema/create'); ?>

<label>Arbetstillfälle</label>
<!--input type="text" name="job"-->
<?php

$options = array();

foreach($jobs as $key=>$val){
	$options[$key] = $val;//$job["beskrivning"];
}

echo form_dropdown('job', $options, null, "id=jobOpt");

?>

<br>


    <label for="schemahelp">Snabbval</label>
<?php
$koder = array(
	"d1"=>"Varje dag, från ursprungsdagen",
	"d2"=>"Var annan dag...",	
	"14"=>"Måndagar och torsdagar, tidigast från ursprungsdagen",
	"25"=>"Tisdagar och fredagar...",
	"1234" => "Mån-tor, varje vecka",
	"w2"=>"Som ursprungsdagen varannan vecka",
	"1w1m2x" => "Som ursprungsdagen samt måndag varje vecka varannan månad");
echo form_dropdown('snabbval', $koder, null,"id='snabbval'") . "<br>";
?>


    <label for="schemacode">Schema-kod</label>
    <input type="text" name="schemacode" id="schemacode" /><br>

    <a href="<?=site_url("jobschema/listing/");?>" target="_blank" id="listLink" >Se listning</a><br>

    <input type="submit" name="submit" value="Skapa schema" />

</form>
    </div>
<?php
    echo anchor('jobschema/', 'Åter till scheman', 'class=""') . "<br>";
?>

<script>


window.onload = function(){

	originalListLink = document.querySelector("#listLink").href;


	var snabbval = document.querySelector("#snabbval");
	var kod = document.querySelector("#schemacode");

	snabbval.addEventListener("change", function(){
		var selInd = this.selectedIndex;
		var selOpt = this.options[selInd];
		var optVal = selOpt.value;
	
		kod.value = optVal;

		var jobOpt = document.querySelector("#jobOpt");
		var selInd2 = jobOpt.selectedIndex;
		var selOpt2 = jobOpt.options[selInd2];
		var optVal2 = selOpt2.text;
		
		var lastIndexSpace = optVal2.lastIndexOf(" ") + 1;
		var optDate = optVal2.substring(lastIndexSpace);

		addToAHref(kod.value);
				
	});

	kod.addEventListener("input", function(){
		console.log("inp");
		addToAHref(this.value);
	});

}

function addToAHref(text){
	var listL = document.querySelector("#listLink");
	var optDate = getOptDate(document.querySelector("#jobOpt"));
	listL.href = originalListLink;
	listL.href += optDate + "/" + text;
}

function getOptDate(selectElem){
	var selInd2 = jobOpt.selectedIndex;
	var selOpt2 = jobOpt.options[selInd2];
	var optVal2 = selOpt2.text;

	var lastIndexSpace = optVal2.lastIndexOf(" ") + 1;
	var optDate = optVal2.substring(lastIndexSpace);
	return optDate;
}


</script>
