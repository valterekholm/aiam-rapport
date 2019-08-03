<div>
<?php if(isset($block_view)){
     foreach ($jobs as $job):
     ?>

<h3>Arbetsplats <?=$job['arbetsplats_id'] ?></h3>
<div class="main">
    <table>
        <tr><td>Start</td><td>
    <?php echo $job['datum_start']."<br>"; ?></td></tr>
        <tr><td>Slut</td><td>
        <?php echo $job['datum_slut']."<br>"; ?></td></tr>
        </table>
</div>
<p><?php echo(anchor('jobs/edit/'.base64_encode($job["id"]), 'Redigera', 'class=""'));?></p>

<?php endforeach; }

else{//inte block_view... todo: blockview ej klart
    $target = ($level<3)?"jobs/edit":"jobs/view";
    print_array_table($jobs,$target,"id");
}
 
?>
    <br>
<?php
if($level<3 && $level > 0){
	echo anchor(base_url()."index.php/jobs/create/",'Lägg in nytt');
}
    ?>
    <br>
    <?php
	/*echo anchor(base_url()."index.php/jobs/",'Åter');*/
    ?>
</div>
