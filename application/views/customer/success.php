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
        <?php echo anchor('customers/', 'Åter till Kunder', 'class=""');?>
        <script>
            setTimeout(function(){location.replace("<?php echo site_url('/customers/') ?>")},5000);
        </script>
    </body>
</html>
