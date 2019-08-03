<!--div class="form_holder"-->


<?php

//error_log("Startsida " . base_url() . " " . $_SERVER['REQUEST_URI']);
 /*
 | Field         | Type         | Null | Key | Default | Extra |
+---------------+--------------+------+-----+---------+-------+
| name          | varchar(50)  | YES  |     | NULL    |       |
| gatuadress    | varchar(50)  | YES  |     | NULL    |       |
| postnummer    | char(5)      | YES  |     | NULL    |       |
| telefon       | char(15)     | YES  |     | NULL    |       |
| cctld         | varchar(5)   | YES  |     | se      |       |
| log_save_days | mediumint(8) | NO   |     | 7       |
  */

/*This is the application web startpage*/

if($level<3 && $level>0){
?>
<div class="partment">
<h3>Företag</h3>
    <ul>
	<?php if($level > 1){ ?>
	<li><?=anchor(base_url()."index.php/company/","Uppgifter")?></li>
<?php }
else {
?>
	<li><?=anchor(base_url()."index.php/company/superadmin","Superadmin")?></li>
<?php
}
?>
    </ul>
</div>
<div class="partment">

<h3>Personal</h3>
<ul>
<li><?=anchor(base_url()."index.php/staff/","Lista")?></li>

</ul>
</div>

<div class="partment">
<h3>Kunder</h3>
<ul>
<li><?=anchor(base_url()."index.php/customers/","Lista")?></li>
</ul>
</div>

<div class="partment">
<h3>Arbetsplatser</h3>
<ul>
    <li><?=anchor(base_url()."index.php/workplaces/view2","Lista")?></li>
    <?php
    if($level < 3){
        ?>
        <li><?=anchor(base_url()."index.php/workplaces/create","Lägg in ny")?></li>
        <?php
    }
    ?>
</ul>

</div>
<?php
if($level==1){
?>
<div class="partment">
<h3>Test av mail</h3>
<ul>
	<li><?=anchor(base_url()."index.php/test/","Test av mail")?></li>
</ul>        
</div>
<?php
}
}//slut if level < 3 && level > 0

if($level<=3 && $level>0){
}

?>

<?php
if($level >= 1)
{//2
?>


<div class="partment">
<h3>Arbetstillfällen</h3>
<ul>
<?php
	if($level < 3){
?>

<li><?=anchor(base_url()."index.php/jobs/view1","Lista av arbetstillfällen")?></li>
<li><?=anchor(base_url()."index.php/jobs/link_list","Arbetstillfällen länktabell")?></li>
<?php
	}
?>
<li><?=anchor(base_url()."index.php/jobs/view1_personal","Lista, mina arbetstillfällen")?></li>
<li><?=anchor(base_url()."index.php/jobs/calendar","Kalender - arbetstillfällen")?></li>

<?php
if($level < 3){
?>
	<li><?=anchor(base_url()."index.php/jobs/create","Lägg in nytt")?></li>
<?php
}
?>

</ul>

</div>

<?php
if($level == 1){
?>
<div class="partment">
<h3>Jobbscheman</h3>
<ul>

	<li><?=anchor(base_url()."index.php/jobschema/","Lista")?></li>
</ul>
</div>
<?php
}
?>


<div class="partment">

<h3>Arbetsrapporter</h3>
     <ul>
<li><?=anchor(base_url()."index.php/reports/","Lista")?></li>
<?php
	//todo: kalender för anställd med kommande arbetstillfällen
	if($level<3){//3
?>
<li><?=anchor(base_url()."index.php/reports/choose_staff","Se rapporter för en person")?></li>
<?php 
	}//2
?>


</ul>
</div>

<div class="partment">
<h3>Mobil-app inställningar</h3>
<ul><li><?=anchor(base_url()."index.php/apikeys/","Nyckel")?></li></ul>
</div>

<?php
if($level == 1){
?>
	<div class="partment">
	<h3>Miniblogg</h3>
	<ul>

	        <li><?=anchor(base_url()."index.php/miniblog/","Inlägg")?></li>
	        <li><?=anchor(base_url()."index.php/miniblog/create","Skapa")?></li>
		</ul>
		</div>
<?php
}
?>


<?php

	//checka in / ut
	$check_way = "in";
	if (isset($_SESSION["open_report"]) && $_SESSION["open_report"] != "" /*&& !empty($_SESSION["open_report"])*/){
		$check_way = "ut";
	}

	echo "<ul><li class='button_like'>".anchor(base_url()."index.php/reports/check","Checka $check_way")." <span id='under_check_btn'></span></li></ul>";

}//1


if (isset($_SESSION["user_name"])) {//2
?>
<?php
	if($level<3){
		echo "<p>Admin level: " . $level . "</p>";
	}
} else {//2 // !isset($level)
?>


<img src="/aiam_rapport.png" style="display: block; width:150px; margin: 0 auto;" alt="Aiam rapport" onerror="if (this.src != '/aiam_rapport.png') this.src = 'https://aiam-rapport.se/aiam_rapport.png';">

<?php
	echo "<fieldset><legend>Logga in</legend>";
	echo "<form action='".base_url()."index.php/users/login' method='post' id='login_form'>";
	echo "<div>";
	echo "<label for='email'>Användare</label>";
	echo "<input type='text' name='email' id='email'>";
	echo "</div>";
	echo "<div>";
	echo "<label for='password'>Lösenord</label>";
	echo "<input type='password' name='password' id='password'>";
	echo "</div>";
	echo "<div>";
	echo "<input type='submit' value='logga in'>";
	echo "</div>";
	echo "</form>";
	echo "</fieldset>";
}
?>
    <!--/div-->

<?php
if(null != $last_blog && $level > 0 && $level < 4){
	echo "<fieldset><legend>Senaste admin-blogg</legend>";
	echo "<h3>" . $last_blog["title"] . "</h3>";
	echo "<p>" . $last_blog["created_date"] . "</p>";
	echo "<p>" . $last_blog["message"] . "</p>";
	echo "</fieldset>";
}
?>

<script>
    window.onload = function () {
        if (!document.getElementById("login_form"))
            if (mobilecheck()) {
                document.getElementById('under_check_btn').innerHTML = "<br><br>Kom ihåg att sätta på 'Plats' i din mobil innan";
            }
            else {
                //alert("Ej mobil");
            }
    }
</script>
