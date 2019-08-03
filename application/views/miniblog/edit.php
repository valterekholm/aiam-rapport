<?php echo validation_errors(); ?>


<?php echo form_open('miniblog/update'); ?>

    <input type="hidden" name="id" value="<?=$post["id"];?>" readonly><br>

    <label for="title">Rubrik</label>
    <input type="text" name="title" id="title" value="<?=$post["title"];?>" /><br>

    <label for="message">Meddelande</label>
    <textarea name="message" id="message"><?=$post["message"];?></textarea>

    <input type="submit" name="submit" value="Uppdatera">
</form>

