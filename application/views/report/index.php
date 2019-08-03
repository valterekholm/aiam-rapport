<script type="text/javascript" src="<?=base_url()?>js/Script.js"></script>
<div>
<?php if(isset($block_view)){
     foreach ($reports as $report):
     ?>

<h3>Jobbrapport <?=$report['arbetsplats_id'] ?></h3>
<div class="main">
    <table>
        <tr><td>Personal</td><td><?=$report[""]?></td></tr>
        <tr><td>Check in</td><td>
    <?=$report['check_in_time']."<br>"; ?></td><td><?=$report["lati_in"]?></td><td><?=$report["longi_in"]?></td></tr>
        <tr><td>Check ut</td><td>
        <?=$report['check_out_time']."<br>"; ?></td><td><?=$report["lati_out"]?></td><td><?=$report["longi_out"]?></td></tr>
        </table>
</div>
<p><?=anchor('jobs/edit/'.base64_encode($report["id"]), 'Redigera', 'class=""');?></p>

<?php endforeach; }

else{
    print_array_table($reports,"reports/edit","id");
    //echo "<p>Summa sekunder: $summa<br>";
    if(sizeof($reports)>0){
    printf("<p>Summa timmar: %.2f <br>", $summa/3600);

    $timmar = intval($summa/3600);
    echo "$timmar Timmar,<br>";
    $summa = $summa-($timmar*3600);
    $minuter = intval($summa/60);
    echo "$minuter minuter,<br>";
    $summa = $summa-($minuter*60);
    echo "$summa sekunder</p>";
    }
}
if(isset($id) && $id>0){
    $hidden = array("id" => $id);
    echo form_open('reports/index','',$hidden);//Skapa fält för id 
}
else{
    echo form_open('reports/index');
}

?>
    <label for="cal_1">Avgränsning: </label><br>
    Från: <input type="text" name="cal_1" id="cal_1" class="date-field"  placeholder="yyyy-mm-dd" value="<?php echo set_value('cal_1'); ?>" autocomplete="off"> 
    Till: <input type="text" name="cal_2" id="cal_2" class="date-field" placeholder="yyyy-mm-dd" value="<?php echo set_value('cal_2'); ?>" autocomplete="off"> 
    <?php
	if($id>0){
?>
<?php
}
?>
	<input type="submit" value="Uppdatera">
    </form>
    <hr>
    <?="<div class='center'>$message</div>"?>
    <?php //om inga rapporter hittas t.ex.?>
    <br>
    <?php
        
if(!empty($reports)){
	//echo anchor(base_url()."application/views/report/MakeReport1.php",'Skapa Excel fil', "target='_blank'") . "<br>";

	if(isset($id)){
		$hidden = array("idp" => base64_encode($id));
	}
	else{
		$hidden = array("idp" => '');
	}
	$from = isset($from) ? $from : '';
	$to = isset($to) ? $to : '';
	echo form_open('reports/make_excel_post','target=_blank',$hidden);
	echo "<input type='hidden' name='from' value='$from'>";
	echo "<input type='hidden' name='to' value='$to'>";
	echo "<input type='submit' value='Skapa excel-fil' id='getExcelBtn'><br>";
	echo "</form>";
	echo form_open('reports/make_pdf_post','target=_blank',$hidden);
	echo "<input type='hidden' name='from' value='$from'>";
	echo "<input type='hidden' name='to' value='$to'>";
	echo "<input type='submit' value='Skapa pdf-fil' id='getPdfBtn'><br>";
	echo "</form>";
}
        //echo anchor(base_url().$excel_link,'Ladda ner Excel-fil',"class='button_like excel_btn_link'");
    ?>
    <br>
    <?php
        if($level<3)
                echo anchor(base_url()."index.php/reports/create/",'Lägg in ny');
    ?>
    <br>
</div>

<script>
	window.onload = function () {
		doOnLoad(); //calendar
		//myCalendar.setSensitiveRange(todays_date,null);
		myCalendar.setDateFormat("%Y-%m-%d");

		var ce = document.getElementById("getExcelBtn");
		ce.addEventListener("click", safetyRedirect);

		var cp = document.getElementById("getPdfBtn");
		cp.addEventListener("click", safetyRedirect);
	}

	function safetyRedirect(){
		setTimeout(function(){
			console.log("12000");
			//redirect
			window.location.replace("<?=base_url()?>");

		}, 12000);
		/*messageNonBlocking(timeMS, message, interval)*/
		setTimeout(function(){
			messageNonBlocking(5000, "Av säkerhetsskäl kommer startsidan laddas om strax", 2000);
		}, 7000);
	}

</script>
