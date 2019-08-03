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
        <?php if(isset($message)){echo "<p class='message'>$message</p>";} ?>
        <input type="button" value="Logga ut" onclick="window.location.assign('<?=site_url("users/logout")?>')">
        <p>Ska logga ut automatiskt...</p>
        <script>
            setTimeout(function(){ location.replace("<?=site_url('users/logout')?>")}, 3000);
        </script>
    </body>
</html>
