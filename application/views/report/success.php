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
        <?php echo anchor('reports/index', 'Åter till \'Arbetsrapporter\'', 'class=""');?>
        <p>Ska återgå automatiskt</p>
        <script>
            setTimeout(location.replace("<?=site_url('reports/index')?>"), 1000);
        </script>
    </body>
</html>
