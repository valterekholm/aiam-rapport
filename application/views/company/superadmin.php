<div class="form_holder">
<?php
echo form_open('company/index');
echo "<div class='inline_info'>";
echo "<label>Namn</label>";

echo "<table><tr><th>id</th><th>namn</th></tr>";
foreach($options as $id => $name){
	echo "<tr><td>$id</td><td>$name</td></tr>";
}
echo "</table>";

echo form_dropdown('company', $options);

echo form_submit('submit1', 'Gå');
echo "</form>";
?>
</div>

<?php

echo anchor(base_url()."index.php/company/create/",'Lägg in nytt');
?>

<div id="map1"></div>

<script>

var mymap = L.map('map1').setView([59.325119, 18.071032], 13);//51.505, -0.09

L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token={accessToken}', {

	attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
	maxZoom: 18,
	id: 'mapbox.streets',
	accessToken: 'pk.eyJ1IjoiYWJkdWxsYWh2YWx0ZXIiLCJhIjoiY2p3ZHYxajRpMTZuMjQ4bW9wdmxvcm90aCJ9.ZX_bUgp9AS7lQDl-PWyDHA'
}).addTo(mymap);

var marker = L.marker([59.325119, 18.071032]).addTo(mymap);

var circle = L.circle([59.325119, 18.079032], {
	color: 'gray',
	fillColor: '#503',
	fillOpacity: 0.5,
	radius: 500
}).addTo(mymap);

var polygon = L.polygon([
	[59.32, 18.08],
	[59.33, 18.08],
	[59.32, 18.09]
]).addTo(mymap);

marker.bindPopup("<b>Hello world!</b><br>I am a popup.").openPopup();
circle.bindPopup("I am a circle.");
polygon.bindPopup("I am a polygon.");


//function onMapClick(e) {
//	    alert("You clicked the map at " + e.latlng);
//}

var popup = L.popup();

function onMapClick(e) {
	popup
		.setLatLng(e.latlng)
		.setContent("You clicked the map at " + e.latlng.toString())
		.openOn(mymap);
}

mymap.on('click', onMapClick);


</script>

