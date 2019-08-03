<!DOCTYPE html>
<html lang="sv" class="jobb_rapport">
        <head>
        <title>Aiam rapport</title>
<?php

if(base_url() == "https://aiam-rapport.se/"){
?>

<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
<link rel="manifest" href="/site.webmanifest">
<link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
<meta name="msapplication-TileColor" content="#da532c">
<meta name="theme-color" content="#80b9b3">

<?php

}



	//error_log("header.php med base_url " . base_url());
                if(isset($head_ext_css)){
                    echo $head_ext_css . "\n";
		}
		else{
			echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".base_url()."css/style.css\" />\n";//test
		}
		if(isset($head_css)){
			echo "<style>\n".$head_css . "\n</style>\n";
		}
                if(isset($head_ext_script)){
                    echo $head_ext_script;
                }
            ?>
            <script>
                <?php
                    if(isset($head_script)){
                        echo $head_script;
                    }
                ?>
<?php
echo "todays_date = '" . date("Y-m-d") . "';\n";
?>
</script>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        </head>
	<body id="body1"><!-- had .padded1 -->
	<div id="content">
<div class="pagetop">
<div class="headline">
<?php
if( substr(base_url(), strrpos(rtrim(base_url(), '/'), '/')) == $_SERVER['REQUEST_URI'] ){
?>
	<ul class="menu">
        <li>Startsida</li>
	</ul>

<?php
}
else if (isset($_SESSION["user_name"])){
?>
	<ul class="menu">
	<li><a href="<?= base_url() ?>">Startsida</a></li>
	</ul>

<?php
}
else{
	//not logged in, show logo
?>
<?php

}
if(function_exists('print_breadcrumb')){
	print_breadcrumb(true);
}
?>

		<?php if (isset($_SESSION["open_report"]) && $_SESSION["open_report"] != "" && !empty($_SESSION["open_report"])){ 

	$openReport = new DateTime($_SESSION["open_report"]);
	$today = new DateTime();
	$op_re = "";
	if($openReport->format("Y-m-d") == $today->format("Y-m-d")){
		$op_re = $openReport->format("H:i");
	}
	else{
		$op_re = $openReport->format("Y-m-d H:i");
	}
?>

	<p class="open_report">Du har ett startat pass som började <?=$op_re;?> </p>
<?php } ?>
	<?php if (isset($_SESSION["user_name"])){
?>





        <span class="headline_right">
            <?=anchor(site_url("jobs/calendar"),"<img src='".base_url()."css/imgs/calendar-interface-symbol-tool.svg'>")?>
        </span>

        <span class="username_logout headline_right right">Inloggad som <?=$_SESSION['user_name']?><br><?=anchor(base_url()."index.php/users/logout",'Logga ut')?></span>
<?php } ?>
</div>
    <div class="headline2">
<?php if(isset($title)){ ?>
		<h1 class="diffuse_underline"><?php echo $title; ?></h1>
<?php } ?>

    </div>

</div>
