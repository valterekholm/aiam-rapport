
<?php
error_log("view med key $key");
if($key == '0'){
?>
<p>Ingen nyckel finns.</p>

<?php
	echo anchor(base_url()."index.php/apikeys/make_my_key","Skapa");
}
else{
?>
<p><?=$key?></p>
<?php
}
?>

