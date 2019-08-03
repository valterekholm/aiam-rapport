<div>

<?php if(isset($block_view)){ foreach ($customers as $customer): ?>
<h3><?php echo $customer['namn']?></h3>
<div class="main">
<?php echo $customer['email']."<br>"; ?>
<?php echo $customer['tel1']."<br>"; ?>
<?php echo $customer['tel2']."<br>"; ?>
</div>
<p><?php echo(anchor('customers/edit/'.base64_encode($customer["id"]), 'Redigera', 'class=""'));?></p>
<?php endforeach; } ?>

<!--<a href="http://localhost:11154/index.php/customers/create/">Lägg in ny</a>-->
    <?php 
    print_array_table($customers,"customers/edit","id");
    echo anchor(base_url()."index.php/customers/create/",'Lägg in ny');
    echo "<br>";
?>
</div>
