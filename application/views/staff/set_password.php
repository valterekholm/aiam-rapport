<div class="form_holder">
<h2><?php /*echo $title;*/ ?></h2>

<?php echo validation_errors(); ?>

<?php
    $hidden = array("id" => $person["id"], "pcode" => $person["code"]);
?>
<?php echo form_open('staff/set_password_post','',$hidden); ?>
    <label for="fornamn">Förnamn</label>
    <input type="text" name="fornamn" value="<?=$person["fornamn"]?>" readonly><br>
    <label for="efternamn">Efternamn</label>
    <input type="text" name="efternamn" value="<?=$person["efternamn"]?>" readonly><br>
    <label for="email">Email</label>
    <input type="text" name="email" value="<?=$person["email"]?>" readonly><br>
    <label for="password">Nytt lösenord</label>
    <input type="password" name="password"><input type="button" value="visa" onclick="this.previousElementSibling.type='text';"><br>
    <label for="submit"></label>
    <input type="submit" name="submit" value="Skicka">
</form>
<?php
    echo anchor('staff/', 'Åter till personal', 'class=""') . "<br>";
?>

</div>