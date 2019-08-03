<?php echo validation_errors(); ?>


<?php echo form_open('jobschema/update'); ?>

    <input type="hidden" name="id" value="<?=$schema["id"];?>" readonly><br>
<?php
$options = array(
	'small'         => 'Small Shirt',
	'med'           => 'Medium Shirt',
	'large'         => 'Large Shirt',
	'xlarge'        => 'Extra Large Shirt',
);
//echo form_dropdown('job', $options, 'selected');

?>
    <label for="code">Schema-kod</label>
    <input type="text" name="schemacode" value="<?=$schema["schema_kod"];?>"><br>
<?php
//$options = $companies;//array('1'=>'Laial AB', '2' => 'Firma V. Ekholm');
//$selected = array($person["company_id"]);
//    echo form_dropdown('job', $options, $selected);
    ?>


    <input type="submit" name="submit" value="Uppdatera">
</form>

