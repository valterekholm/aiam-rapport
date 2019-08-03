<div>
Test...
<?php
 
?>

<?php echo form_open('staff/send_mail'); ?>

    <label for="address">Adress</label>
    <input type="text" name="address" id="address" /><br>

    <label for="header">Rubrik</label>
    <input type="text" name="header" id="header" /><br>

    <label for="message">Meddelande</label>
    <input type="text" name="message" id="message" /><br>

    <input type="submit" name="submit" value="Test send email" />
	
</form>

<?php echo form_open('staff/send_mail_2'); ?>

    <label for="address">Adress</label>
    <input type="text" name="address" id="address" /><br>

    <label for="header">Rubrik</label>
    <input type="text" name="header" id="header" /><br>

    <label for="message">Meddelande</label>
    <input type="text" name="message" id="message" /><br>

    <input type="submit" name="submit" value="Test send email via Staff_model->send_mail" />
	
</form>

<?php echo form_open('staff/send_mail_company'); ?>
	<input type="hidden" name="id" value="1" />
	<label for="address">Adress</label>
	<input type="text" name="address" id="address" /><br>
	<label for="header">Rubrik</label>
	<input type="text" name="header" id="header" /><br>
	<label for="message">Meddelande</label>
	<input type="text" name="message" id="message" /><br>
    <input type="submit" name="submit" value="Test send email via Staff->send_mail_company" />
	
</form>



<?php //echo(anchor('staff/send_mail/', 'Redigera', 'class=""')); $address=FALSE, $header=FALSE, $message=FALSE?>


</div>