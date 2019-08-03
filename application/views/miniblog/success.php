	<h1>Framgång</h1>
<?php if(isset($message)){
?>

        <p class="message"><?=$message?></p>

<?php
}
?>

        <?php echo anchor('miniblog/index', 'Åter till inlägg', 'class=""');?>
