        <?php
            echo $info."<br><br>";
            //echo $result."<br><br>";
            //echo $start."<br><br>";
            //echo $pos."<br><br>";
            
            
            //var_dump($json);
            //var_dump($xml);
            //echo json_last_error();
            //echo JSON_ERROR_NONE." ".JSON_ERROR_DEPTH." ".JSON_ERROR_STATE_MISMATCH." ".JSON_ERROR_CTRL_CHAR." ".JSON_ERROR_SYNTAX." ".JSON_ERROR_UTF8." ".JSON_ERROR_RECURSION." ".JSON_ERROR_INF_OR_NAN." ".JSON_ERROR_UNSUPPORTED_TYPE." ";
            echo form_open('workplaces/get_page'); ?>


    <label for="street">Gatuadress</label>
    <input type="text" name="street" value="<?=$street?>"><br>

    <label for="postal_code">Postnummer</label>
    <input type="text" name="postal_code" value="<?=$postal_code?>"><br>

    <label for="city">Stad</label>
    <input type="text" name="city" value="<?=$city?>"><br>

    <input type="submit">
</form>

<input type="text" id="lat" value="<?=$lat?>"><br>
<input type="text" id="lon" value="<?=$lon?>"><br>


<?php
    if($lat && $lon){

    $center_of_map_lat = $lat;//59.319178;
    $center_of_map_long =$lon;//18.095856;
    $zoom_level = 14;
    $url_map = "https://kartor.eniro.se/?c=".$center_of_map_lat.",".$center_of_map_long."&z=".$zoom_level."&g=".$lat.",".$lon;
    echo $url_map."<br>";
    ?>
    <iframe width="100%" height="70%" src="<?=$url_map?>" id="iframe1">
</iframe>
<?php
    }

?>

