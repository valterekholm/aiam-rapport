<?=$chain_table?>
<?php echo validation_errors(); ?>

<?php echo form_open('customers/create'); ?>

<?php if($level==1){
?>
    <label for="company_id">Company id</label>
    <input type="text" name="company_id" id="company_id" placeholder="id"/><br>

<?php } ?>

    <label for="name">Namn</label>
    <input type="text" name="name" id="name" /><br>

    <label for="email">Email</label>
    <input type="text" name="email" id="email" /><br>

    <label for="tel1">Telefon</label>
    <input type="text" name="tel1" id="tel1" /><br>

    <label for="tel2">Telefon alternativ</label>
    <input type="text" name="tel2" id="tel2" /><br>

    <input type="submit" name="submit" value="Registrera kund" />

</form>
<?php
    echo anchor('customers/', 'Åter till Kunder', 'class=""') . "<br>";
?>
