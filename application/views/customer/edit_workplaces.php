<?php //sida för att lägga till arbetsplats till en kund ?>
<?php echo $header; ?>
<?php echo validation_errors(); ?>
<?php echo form_open('customers/add_workplace'); ?>

    <br>
    <?php
        echo form_hidden('customer', $customer["id"]);
    ?>

    <label for="new_cust">Namn</label>
<?php
    $options = array(
        /*'small'         => 'Small Shirt',
        'xlarge'        => 'Extra Large Shirt'
        */
);

foreach($unrelated_workplaces as $work){
    $options[$work["id"]] = $work["namn"];
}

//array_unique($options);
echo form_dropdown('workplace', $options);

?>

<input type="submit" name="submit" value="Lägg till">    
</form>
