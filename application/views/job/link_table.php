<!--table-->
<?php
    $count_empty = 0;
foreach($link_table as $row):

if(array_key_exists('id',$row)){//räknar oifyllda fält
    if(empty($row["id"])){
        $count_empty++;
    }
}

endforeach;

?>

<!--/table-->

<?php
print_array_table($link_table,"jobs/edit","id_arbetstillfalle");


if(array_key_exists('id',$link_table[0])){//man kommer från _nullcheck
    if($count_empty>0){
        echo "<br>".anchor(base_url()."index.php/jobs/clear_empty_links_from_links_table/link_table", "Rensa onödiga poster - bör göras...");
    }
    else{
        echo "<br>Inga tomma länkar hittade";
    }
}
else{//man kommer från controller link_list
	if($level == 1){
		echo "<br>".anchor(base_url()."index.php/jobs/link_list_nullcheck", "Kolla efter onödiga poster");
	}
}
?>
<br>
