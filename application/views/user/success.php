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
	<p class="message"><?php if(isset($message)) echo $message; ?></p>
	<p>
	<?php echo anchor('/', 'Åter till startsida', 'class=""');?>
	</p>
	<script>
		setTimeout(function(){
			location.replace("<?=site_url('/')?>");
			}, 4000);
        </script>
    </body>
</html>
