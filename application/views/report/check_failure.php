<!DOCTYPE html>
<html lang="sv">
    <head>
        <meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Jobb_rapport - fel</title>
    </head>
    <body>
	<h1>Fel - din operation misslyckades</h1>
        <?php //echo anchor('reports/index', 'Åter till \'Arbetsrapporter\'', 'class=""');?>
        <input type="button" value="Gå till startsida" onclick="window.location.assign('<?=site_url("/")?>')">
        <p>Ska skicka till startsida...</p>
        <script>
            setTimeout(function(){ location.replace("<?=site_url('/')?>"); }, 3000);
        </script>
    </body>
</html>
