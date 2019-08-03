<div>
<?php if(isset($block_view)){
     foreach ($workplaces as $workplace):
     ?>

<h3><?php echo $workplace['namn']?></h3>
<div class="main">
    <table>
        <tr><td>Gatuadress</td><td>
    <?php echo $workplace['gatu_adress']."<br>"; ?></td></tr>
        <tr><td>Postnummer</td><td>
    <?php echo $workplace['postnummer']."<br>"; ?></td></tr>
        <tr><td>Trappor</td><td>
    <?php echo $workplace['trappor']."<br>"; ?></td></tr>
        <tr><td>Land</td><td>
    <?php echo $workplace['land']."<br>"; ?></td></tr>
        <tr><td>Longitud</td><td>
<?php echo $workplace['longi']."<br>"; ?></td></tr>
        <tr><td>Latitud</td><td>
<?php echo $workplace['lati']."<br>"; ?></td></tr>
        <tr><td>Kund-id</td><td>
<?php echo $workplace['kund_id']."<br>"; ?></td></tr>
        </table>
</div>
<p><?php echo(anchor('workplaces/edit/'.base64_encode($workplace["id"]), 'Redigera', 'class=""'));?></p>

<?php endforeach; }

else{
    print_array_table($workplaces,"workplaces/edit","id");
}
 
?>
    <br>
    <?php
        echo anchor(base_url()."index.php/workplaces/create/",'Lägg in ny');
    ?>
    <br>
</div>
