<?=$chain_table?>

<?php echo validation_errors(); ?>

<?php echo form_open('workplaces/update'); ?>

    <input type="hidden" name="id" value="<?=$workplace["id"]?>"><br>

    <label for="name">Namn</label>
    <input type="text" name="name" value="<?=$workplace["namn"]?>"><br>

    <label for="name">Gatuadress</label>
    <input type="text" name="street" value="<?=$workplace["gatu_adress"]?>" id="gata"><input type="button" value="kopiera" onClick="kopiera_input(this.previousElementSibling)"><br>

    <label for="name">Postnummer</label>
    <input type="text" name="postal_code" value="<?=$workplace["postnummer"]?>"><br>

    <label for="name">Trappor</label>
    <input type="text" name="stairs" value="<?=$workplace["trappor"]?>"><br>

    <label for="name">land</label>
    <input type="text" name="land" placeholder="Kan lämnas tomt för Sverige" value="<?=$workplace["land"]?>"><br>

    <label for="lati">Latitud</label>
    <input type="text" name="lati" value="<?=$workplace["lati"]?>"><br>

    <label for="longi">Longitud</label>
    <input type="text" name="longi" value="<?=$workplace["longi"]?>"><br>

    <label for="timezone">Tidzon</label>
    <input type="text" name="time_zone" id="timezone" value="<?=$workplace["time_zone"]?>"><br>

<input type="submit" name="submit" value="Uppdatera arbetsplats data">    
</form>
<label>Anknytna kunder:</label><br>
(Alla kunder som kan gälla för denna arbetsplats)<br>
<?php if (sizeof($related_customers) == 0){ echo "Inga anknytna kunder"; } ?>
<?php foreach ($related_customers as $customer):
 ?>
<div class="related_post inner_shadow">
<h3><?php echo $customer['namn']?></h3>

<?php echo $customer['email']."<br>"; ?>
<?php echo $customer['tel1']."<br>"; ?>
<?php echo $customer['tel2']."<br>"; ?>

<?php echo(anchor('customers/edit/'.base64_encode($customer["id"]), 'Redigera kund', 'class=""'));?><br>
<?php echo(anchor('workplaces/delete_customer_connection/'.base64_encode($customer["id_arbetsplats"])."/".base64_encode($customer["id"])."/wp", 'Koppla bort', 'title="Koppla bort från platsen bara"'));?><br>
    </div>
<?php endforeach;
 ?>
<?php
    echo "<div class='bottom_links'>";
echo anchor('workplaces/connect_any_customer/'.base64_encode($workplace["id"]),'Anknyt kund');
    echo anchor('workplaces/delete_row/'.$workplace["id"], 'Ta bort arbetsplats', 'class="critical"');
    echo anchor('workplaces/', 'Åter till Arbetsplatser', 'class=""');
    echo "</div>";

    $lat = floatval($workplace["lati"]);
    $long = floatval($workplace["longi"]);
    $center_of_map_lat = $lat;//59.319178;
    $center_of_map_long =$long;//18.095856;
    $zoom_level = 14;
    $url_map = "https://kartor.eniro.se/?embed=true&c=".$center_of_map_lat.",".$center_of_map_long."&z=".$zoom_level."&g=".$lat.",".$long;
    echo $url_map."<br>";
?>
<!--iframe width="450px" height="350px" src="<?php echo $url_map; ?>" id="iframe1">
</iframe-->
<div id="map1" style='width: 100%; height: 300px'></div>
<script>
    makeLinksConfirmable("critical");
    window.onload = function(){
        var mymap = L.map('map1').setView([<?=$lat?>, <?=$long?>], 13);

        L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token={accessToken}', {
            attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
            maxZoom: 18,
            id: 'mapbox.streets',
            accessToken: 'pk.eyJ1IjoiYWJkdWxsYWh2YWx0ZXIiLCJhIjoiY2p3ZHYxajRpMTZuMjQ4bW9wdmxvcm90aCJ9.ZX_bUgp9AS7lQDl-PWyDHA'
        }).addTo(mymap);

        var marker = L.marker([<?=$lat?>,<?=$long?>]).addTo(mymap);
    }
</script>
