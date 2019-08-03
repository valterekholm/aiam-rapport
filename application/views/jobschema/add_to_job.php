<h3>Schema till arbetstillfälle</h3>
<?php
error_log("<p>1. Check supplied code<br>2. Check if code allready saved<br>3. If not save, get id<br>4. If, get id<br>5. Set id to jobs schema<br>Show success/error</p>");
?>

<h4>Listning:</h4>
<pre>
<?php
$count = 0;
foreach($dates as $d){
	echo "$d\t";
	$count++;
	if($count%10==0){
		echo "\n";
	}
}
?>
</pre>
