<?php echo validation_errors(); ?>
<div class="form_holder">
<?php echo form_open('miniblog/create'); ?>

<label>Inlägg</label>
<!--input type="text" name="job"-->
<?php

$options = array();



?>

<br>





    <label for="title">Rubrik</label>
    <input type="text" name="title" id="title" /><br>

    <label for="message">Meddelande</label>
    <textarea name="message" id="message"></textarea>

    <input type="submit" name="submit" value="Skapa inlägg" />

</form>
    </div>
<?php
    echo anchor('miniblog/', 'Åter till inlägg', 'class=""') . "<br>";
?>

<script>

</script>
