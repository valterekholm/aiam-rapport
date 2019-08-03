<?php

?>

<!DOCTYPE html>
<html lang="sv">
    <head>
        <meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title></title>
    </head>
    <body>
	<h1>Framgång</h1>

<?php if(isset($message)){
?>

	<p class="message"><?=$message?></p>

<?php
}
?>
        <?php echo anchor('jobs/view1', 'Åter till \'Arbetstillfällen\'', 'class=""');?>
        <script>
            setTimeout(function(){ location.replace("<?php echo site_url('/jobs/view1') ?>") },5000);
        </script>
    </body>
</html>
