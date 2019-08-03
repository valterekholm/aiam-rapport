<h2><?php /*echo $title;*/ ?></h2>

<?php echo validation_errors(); ?>


<?php

//$data['company_info']

//$comp_info = $data['company_info'];

//echo print_r($company_info, true);//TODO: find company name, use in message


?>


<?php echo form_open('staff/update'); ?>

    <input type="text" name="id" value="<?=$person["id"];?>" readonly><br>
    <label for="fname">Förnamn</label>
    <input type="text" name="fname" value="<?=$person["fornamn"];?>"><br><!-- kanske ska vara $person['fornamn'] -->
    <label for="ename">Efternamn</label>
    <input type="text" name="ename" value="<?=$person["efternamn"];?>"><br>
    <label for="email">Email</label>
    <input type="text" name="email" value="<?=$person["email"];?>"><br>

    <label for="tel">Telefon</label>
    <input type="text" name="tel" value="<?=$person["tel"];?>"><br>

    <?php if($level<3){ ?>
    <label for="tel">Personnummer</label>
    <input type="text" name="personnummer" value="<?=$person["personnummer"];?>"><br>
    <?php } ?>

    <?php if($level==1){ ?>
    <label for="tel">Nytt lösenord</label>
    <input type="text" name="password"><br>
    <?php } ?>

    <?php if($level==1){ ?>
    <label for="level">Access level</label>
    <input type="text" name="level" value="<?=$person["level"]?>"><br>
    <label for="code">Code</label>
    <input type="text" name="pcode" value="<?=$person["code"]?>"><br>
    <label for="company">Företag</label>
<?php
$options = $companies;//array('1'=>'Laial AB', '2' => 'Firma V. Ekholm');
$selected = array($person["company_id"]);
    echo form_dropdown('company', $options, $selected);
    ?>

    <?php } ?>

    <input type="submit" name="submit" value="Uppdatera">
</form>

<?php
    if($level<3){
        //Skicka inbjudning att sätta lösenord
        $meddelande = "<html><body><table style='width:200px; height:300px; padding:10px; border-collapse: separate; border-spacing: 7px; border:3px solid #0865A2'><tr><td><strong>Du inbjuds att registra dig i jobb-rapport-systemet för " . $company_info["name"] . "</strong></td></tr><tr><td>Klicka på länken för att sätta lösenord:<br> " . "<a href='" . base_url("index.php/staff/set_password/".base64_encode($person["id"])."/".base64_encode($person["code"]))."'>Sätt lösenord</a></td></tr><tr><td style='text-align:center'>Med vänliga hälsningar ansvarig/Laial<br><img src='https://laialflytt.se/img/laial_logo_cropped_74_90_j.png' alt='Laial AB'></td></tr></table></body></html>";
	$meddelande = "Du inbjuds att registra dig i jobb-rapport-systemet för " . $company_info["name"] . ". Gå till adressen för att sätta lösenord:\n\n" . base_url("index.php/staff/set_password/".base64_encode($person["id"])."/".base64_encode($person["code"]));
	$kort_meddelande = "För att sätta lösenordet, besök " . base_url("index.php/staff/set_password/".base64_encode($person["id"])."/".base64_encode($person["code"]));
        //echo "<div id='message'>".$meddelande . "</div>";
        //echo "<br><input type='button' value='Skicka mail' id='send_invitation'>";
        $hidden = array("id" => $person["id"]);
        echo "<fieldset>";
	echo "<h3>Skicka ut mail om att sätta lösenord</h3>";
	echo "<A HREF='mailto:".$person["email"]."?subject=Vänligen sätt ditt lösenord&body=".$kort_meddelande."'>Skicka från din epost-klient</A>";
        echo form_open('staff/send_mail_3');
        echo "Adress: ";
        echo form_input('to',$person["email"]);//address
        echo "Rubrik";
        echo form_input('subject',"Vänligen sätt ditt lösenord");//header
        echo "Meddelande";
        echo form_textarea('message',$meddelande);
        echo form_submit('mySubmit','Skicka');
        echo "</form>";
        echo "</fieldset>";
        echo "<div id='result'></div>";
    }

?>


<?php
    if($level==1){//TODO: kolla om admin 2 behöver kunna ta bort
    echo anchor('staff/delete_row/'.$person["id"], 'Ta bort') . "<br>";
    }
    if($level < 3){
	    echo anchor('staff/null_company/'.$person["id"], 'Koppla bort') . " (Koppla bort från företag)<br>";
    }
    echo anchor('staff/', 'Åter till Personal', 'class=""') . "<br>";
?>

<script type="text/javascript">

    window.onload = function () {
    if(typeof document.getElementById("send_invitation") !== "undefined"){
        //var skicka_knapp = document.getElementById("send_invitation");
        /*skicka_knapp.addEventListener("click", function () {
            
            var targ = document.getElementById("result");
            var data0 = "address=" + "<?=$person["email"];?>";
            var data1 = "&header=" + "Meddelande fr. Laial";
            var data2 = "&message=" + "<?=$meddelande?>";
        });*/
    }
    }

</script>
