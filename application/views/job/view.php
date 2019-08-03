
<div class="inline_info"><label>Arbetsplats:</label><?=$arbetsplats?></div>
<div class="inline_info"><label>Gatuadress:</label><?=$gatu_adress?></div>
<div class="inline_info"><label>Trappor:</label><?=$trappor?></div>
<div class="inline_info"><label>Postnummer:</label><?=$postnummer?></div>
<div class="inline_info"><label>Start:</label><?=$datum_start?></div>
<div class="inline_info"><label>Slut:</label><?=$datum_slut?></div>

<div class="inline_info"><label>Beskrivning:</label><?=$beskrivning?></div>
<div class="inline_info"><label>Latitud:</label><?=$latitud?></div>
<div class="inline_info"><label>Longitud:</label><?=$longitud?></div>

<?php
    //karta

    //var_dump($workplaces);
    $lat = $latitud;
    $long = $longitud;
    /*$center_of_map_lat = $lat;//59.319178;
    $center_of_map_long =$long;//18.095856;
    $zoom_level = 14;
    $url_map = "https://kartor.eniro.se/?embed=true&c=".$center_of_map_lat.",".$center_of_map_long."&z=".$zoom_level."&g=".$lat.",".$long;*/
?>
<!--div class="inline_info w700p"><label>Karta för arbetsplats</label><span class="vague">(<?=$url_map?>)</span></div-->
<!--iframe width="100%" height="300px" src="<?php echo $url_map; ?>" id="iframe1">
</iframe-->
<div id="map1" style="width: 100%; height: 300px"></div>

<label>Anknuten personal:</label><br>
<?php
    foreach($related_staff as $person):
?>
    <div class="related_post inner_shadow">
    <h3><?=$person['fornamn'] . " " . $person['efternamn']?></h3>
    <?php if($level < 3) echo $person['personnummer']?>
    </div>
<?php
    endforeach;
?>

<script>
var mymap = L.map('map1').setView([<?=$lat?>, <?=$long?>], 13);

L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token={accessToken}', {
	attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
	maxZoom: 18,
	id: 'mapbox.streets',
	accessToken: 'pk.eyJ1IjoiYWJkdWxsYWh2YWx0ZXIiLCJhIjoiY2p3ZHYxajRpMTZuMjQ4bW9wdmxvcm90aCJ9.ZX_bUgp9AS7lQDl-PWyDHA'
}).addTo(mymap);

var marker = L.marker([<?=$lat?>,<?=$long?>]).addTo(mymap);

window.onload = function () {
}

</script>

