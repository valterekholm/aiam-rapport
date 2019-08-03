<div class="calendar_nav">
<div><?=anchor("jobs/calendar/day","Dag")?></div>
<div><a href=""><<<</a></div>
    <div><?=$date_span?></div>
    <div><a href="">>>></a></div>
    <div><?=anchor("jobs/calendar/week","Vecka")?></div>
    <div><?=anchor("jobs/calendar/month","Månad")?></div>
</div>


<?php


foreach($jobs as $job):

echo "Datum: " . $job["datum_start"] . " - " . $job["datum_slut"] . "<br>";

echo "Arbetsplats: " . $job["arbetsplats-namn"] . "<br>";

echo "Person / personer: " . "<div id = 'jobb_" . $job["id"] . "_personal'></div>";

echo "<hr>";

endforeach;
?>
<script>
    var nr_staff = <?=sizeof($all_staff)?>;
    var all_staff = [];
    var links = [];

    <?php
        foreach($all_staff as $person){
            echo "all_staff.push({first_name:'".$person["fornamn"]."', last_name:'".$person["efternamn"]."', number:'".$person["nummer"]."'});\n\t";
        }
    ?>
    <?php
        foreach($link_jobs_staff as $link){
            echo "links.push({id_jobb:'".$link["id_arbetstillfalle"]."', nummer_person:'".$link["nummer_person"]."'});\n\t";
        }
    ?>

    //alert("Antal arbetspass planerade: " + links.length);

    for(var i=0, len = links.length; i<len; i++){
    //console.log("jobb_" + links.id_jobb + "_personal");
    var targ = document.getElementById("jobb_" + links[i].id_jobb + "_personal");    
    content = targ.innerHTML;
    targ.innerHTML = content + "<span class='person_span person_"+links[i].nummer_person+"' style='border:1px solid gray'></span>&nbsp;"; 
        
    }//for

    for(var i=0, len = all_staff.length; i<len; i++){
        var targ = document.getElementsByClassName("person_" + all_staff[i].number);
        
        for(var j=0; j<targ.length; j++){
            targ[j].innerHTML = all_staff[i].first_name;
        }
    }
</script>