<div class="datapage">

<?php if(isset($block_view)){ error_log("company index block_view"); foreach ($staff as $person): ?>
<h3><?php echo $person['fornamn'] . " " . $person['efternamn']; ?></h3>
<div class="main">
<?php echo $person['email']."<br>"; ?>
<?php echo $person['tel']; ?>
</div>
<p><?php echo(anchor('staff/edit/'.base64_encode($person["id"]), 'Redigera', 'class=""'));?></p>
    <?php //"http://localhost:11154/index.php/staff/edit/".base64_encode($person["email"]); ?>
<?php endforeach; }

else{
	error_log("not block_view");
	print_array_table($staff,"staff/edit","id");
} 
 
?>

<?=anchor('staff/create', 'Lägg in ny', 'class=""')?>

</div>

<script>

	var width = 300;

	var tbl = document.getElementsByTagName("table")[0];
	var wideTds = getWideTds(tbl, width);
		
	wideTds.forEach(function(elem){
		insertDivBetween(elem, "display:inline-block; width:" + width + "px; overflow:hidden");
	});



</script>
