<?php //sida för att lägga till företag till en kund ?>
<?php echo $header; ?>
<?php echo validation_errors(); ?>
<?php echo form_open('customers/add_company'); ?>

    <br>
    <?php
        echo form_hidden('customer', $customer["id"]);
    ?>

    <label for="company">Namn</label>
<?php
    $options = array(
        /*'small'         => 'Small Shirt',
        'xlarge'        => 'Extra Large Shirt'
        */
);

foreach($unrelated_companies as $company){
    $options[$company["id"]] = $company["name"];
}

//array_unique($options);
echo form_dropdown('company', $options);

?>

<input type="submit" name="submit" value="Lägg till">    
</form>
