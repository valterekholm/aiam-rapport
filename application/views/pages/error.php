<?php

?>

<!DOCTYPE html>
<html lang="sv">
    <head>
        <meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?="<link rel=\"stylesheet\" type=\"text/css\" href=\"".base_url()."/css/style.css\" />"?>
        <title>Error</title>
    </head>
    <body>
        <h1>Något gick fel</h1>
        <?php
            if(isset($message)){
                echo "<p class='message'>$message</p>";
            }            
        ?>
        <?php echo anchor('/', 'Åter till startsida', 'class=""');?>
    </body>
</html>
