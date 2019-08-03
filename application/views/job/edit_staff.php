<?php echo $header; ?>

<?php echo validation_errors(); ?>
<?php echo form_open('jobs/add_staff'); ?>

    <br>
    <?php
        echo form_hidden('job', $job["id"]);
    ?>

    <label for="new_cust">Namn</label>
<?php
    $options = array(
        /*'small'         => 'Small Shirt',
        'xlarge'        => 'Extra Large Shirt'
        */
);

foreach($unrelated_staff as $person){
    $txt = $person["fornamn"] . " " . $person["efternamn"];
    if(isset($person["personnummer"])) $txt .= " " . $person["personnummer"];
    $options[$person["id"]] = $txt;
}

//array_unique($options);
echo form_dropdown('person', $options);

?>

<input type="submit" name="submit" value="Anknyt">    
</form>