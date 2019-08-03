<?=$chain_table?>
<?php echo validation_errors(); ?>
<div class="form_holder">
<?php echo form_open('staff/create'); ?>

<?php

if($access_level == 1){
?>
<label>Company-id</label>
<input type="text" name="company_id"><br>
<?php
}

?>

    <label for="fname">Förnamn</label>
    <input type="text" name="fname" id="fname" /><br>

    <label for="ename">Efternamn</label>
    <input type="text" name="ename" id="ename" /><br>

    <label for="email">Email</label>
    <input type="text" name="email" id="email" /><br>

    <label for="tel">Telefon</label>
    <input type="text" name="tel" id="tel" /><br>

    <input type="submit" name="submit" value="Create staff record" />

</form>
    </div>
<?php
    echo anchor('staff/', 'Åter till personal', 'class=""') . "<br>";
?>
