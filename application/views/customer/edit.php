<div class="form_holder">

<?php echo validation_errors(); ?>

<?php echo form_open('customers/update'); ?>
    <label for="fname">Databas-nummer</label>
    <input type="text" name="id" value="<?=$customer["id"];?>" readonly title="Kundens"><br>
    <label for="fname">Namn</label>
    <input type="text" name="name" value="<?=$customer["namn"];?>"><br><!-- kanske ska vara $person['fornamn'] -->
    <label for="ename">Email</label>
    <input type="text" name="email" value="<?=$customer["email"];?>"><br>
    <label for="email">Telefon 1</label>
    <input type="text" name="tel1" value="<?=$customer["tel1"];?>"><br>
    <label for="tel">Telefon 2</label>
    <input type="text" name="tel2" value="<?=$customer["tel2"];?>"><br>
    <input type="submit" name="submit" value="Uppdatera">
</form>

<h2><?=$sub_header?></h2>

    <?php if (sizeof($related_workplaces) == 0){ echo "Inga anknytna platser "; } ?>
<?php
    foreach($related_workplaces as $workplace):
    ?>

    <div class="related_post inner_shadow">
    <h3><?=$workplace['namn']?></h3>

    <?=$workplace['gatu_adress']."<br>"?>
    <?=$workplace['postnummer']."<br>"?>
    <?=$workplace['lati']."<br>"?>
    <?=$workplace['longi']."<br>"?>

    <?=anchor('workplaces/edit/'.base64_encode($workplace["id"]), 'Redigera arbetsplats', 'class=""')?><br>
    <?=anchor('workplaces/delete_customer_connection/'.base64_encode($workplace["id_arbetsplats"])."/".base64_encode($customer["id"])."/c", 'Koppla bort', 'title="Koppla bort från platsen bara"')?>
    </div>

<?php
    endforeach;

    echo anchor('customers/connect_any_workplace/'.base64_encode($customer["id"]), 'Anknyt arbetplats');

    if(isset($related_companies)){
	echo "<h2>Anknutna företag</h2>";
   if (sizeof($related_companies) == 0){ echo "Inga anknytna företag "; } 
	foreach($related_companies as $company):
?>

    <div class="related_post inner_shadow">
    <h3><?=$company['name']?></h3>
    <?=$company['gatuadress']."<br>"?>
    <?=$company['postnummer']."<br>"?>

	<?php /*anchor('company/edit/'.base64_encode($workplace["id"]), 'Redigera arbetsplats', 'class=""')*/?>
	    <?=anchor('company/delete_customer_relation/'.base64_encode($company["id"])."/".base64_encode($customer["id"])."/cu", 'Koppla bort', 'title="Koppla bort från platsen bara"')?>
	        </div>

<?php
		    endforeach;
    echo anchor('customers/connect_any_company/'.base64_encode($customer["id"]), 'Anknyt företag');
    }
?>



<div class="bottom_links">
<?php
?>

<?php
    echo anchor('customers/delete_row/'.$customer["id"], 'Ta bort kunden');
    echo anchor('customers/', 'Åter till Kunder', 'class=""');
?>
    </div>
</div>
