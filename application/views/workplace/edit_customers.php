<?php echo $header; ?>

<?php echo validation_errors(); ?>
<?php echo form_open('workplaces/add_customer'); ?>

    <br>
    <?php
        echo form_hidden('workplace', $workplace["id"]);
    ?>

    <label for="new_cust">Namn</label>
<?php
    $options = array(
        /*'small'         => 'Small Shirt',
        'xlarge'        => 'Extra Large Shirt'
        */
);

foreach($unrelated_customers as $cust){
    $options[$cust["id"]] = $cust["namn"];
}

//array_unique($options);
echo form_dropdown('customer', $options);

?>

<input type="submit" name="submit" value="Anknyt"> 
</form>
