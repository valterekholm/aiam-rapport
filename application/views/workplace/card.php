<?php

?>

<!DOCTYPE html>
<html lang="sv">
    <head>
        <meta charset="utf-8" />
        <title><?=$title?></title>
    </head>
    <body>
        <div id="ram" style="border: 2px solid gray; width: 95%; height: 95%; padding: 20px">
        <h1><?=$title?></h1>
        <h3><?=$workplace["namn"]?></h3>
        <h3>Position:</h3>
        <label>Latitud: </label><strong><?=$workplace["lati"]?></strong><br>
        <label>Longitud: </label><strong><?=$workplace["longi"]?></strong><br>
        <h3>Adress:</h3>
        <label>Gatuadress: </label><strong><?=$workplace["gatu_adress"]?></strong><br>
        <label>postnummer: </label><strong><?=$workplace["postnummer"]?></strong><br>
        <label>trappor: </label><strong><?=$workplace["trappor"]?></strong><br>
        <label>land: </label><strong><?=$workplace["land"]?></strong><br>

        <br>
        <input type="button" onclick="window.close()" value="Stäng">
        <br>
        <input type="button" onclick="window.history.back()" value="Bakåt">
        <?php
            
    $lat = floatval($workplace["lati"]);
    $long = floatval($workplace["longi"]);
    $center_of_map_lat = $lat;//59.319178;
    $center_of_map_long =$long;//18.095856;
    $zoom_level = 14;
    $url_map = "https://kartor.eniro.se/?c=".$center_of_map_lat.",".$center_of_map_long."&z=".$zoom_level."&g=".$lat.",".$long;
    echo $url_map."<br>";
?>
            
        ?>
        <iframe width="100%" height="70%" src="<?php echo $url_map; ?>" id="iframe1">
</iframe>

            </div>
    </body>
</html>
