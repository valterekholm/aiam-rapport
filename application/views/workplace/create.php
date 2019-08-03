<?=$chain_table?>
<?php echo validation_errors(); ?>

<?php echo form_open('workplaces/create'); ?>

    <label for="name">Namn</label>
	    <input type="text" name="name" id="name" value="<?php echo set_value("name");?>" /><br>

    <label for="name">Gatuadress</label>
    <input type="text" name="street" id="street" value="<?php echo set_value("street");?>" /><br>

    <label for="name">Postnummer</label>
    <input type="text" name="postal_code" id="postal_code"  value="<?php echo set_value("postal_code");?>" /><br>

    <label for="city">Stad</label>
    <input type="text" name="city" id="city" value="<?php echo set_value("city");?>" /><br>

    <input type="button" value="Sök koordinater" id="search_coords" /><br>

    <label for="name">Trappor</label>
    <input type="text" name="stairs" id="stairs" value="<?php echo set_value("stairs");?>" /><br>

    <label for="name">land</label>
    <input type="text" name="land" id="land" placeholder="Kan lämnas tomt för Sverige" value="<?php echo set_value("land");?>" /><br>

    <label for="lat">Latitud</label>
    <input type="text" name="lati" id="lat" value="<?php echo set_value("lati");?>" /><br>

    <label for="lon">Longitud</label>
    <input type="text" name="longi" id="lon" value="<?php echo set_value("longi");?>" /><br>

    <label for="customer">Anknyten kund</label>
    <?php
    $options = array('0'=>'ingen');
    foreach($customers as $cust){
        $options[$cust["id"]] = $cust["namn"];
    }
    
    //array_unique($options);
    echo form_dropdown('customer', $options, "", "id='customer'");
    echo anchor('customers/create', 'Skapa ny', 'title="Om kund fattas i listan" target="_blank" id="cc"');
?>
   <br>
<div id="formNewCustomer"></div>
   <label for="timezone">Tidzon</label>
   <input type="text" name="time_zone" id="timezone" value="<?=$comp_time_zone?>" /><br>
<br>

    <input type="submit" name="submit" value="Spara arbetsplats" />
</form>
<div id="info_block"></div>

<div id="map1" style='width: 100%; height: 300px'></div>


    <script>
    var selectInterval = null;
    window.onload = function () {
	    console.log("Page loaded");
	    var btn = document.getElementById("search_coords");
	    btn.addEventListener("click", function () {
		    var street = document.getElementById("street").value;
		    var postal_code = document.getElementById("postal_code").value;
		    var city = document.getElementById("city").value;

		    var argString = "street="+street; //arguments
		    if(city.length > 1){
			    argString += "&city="+city;
		    }
		    if(postal_code.length >= 5){
			    argString += "&postal_code="+postal_code;
		    }

		    var targets = [];
		    targets.push(document.getElementById("lat"));
		    targets.push(document.getElementById("lon"));
		    targets.push(document.getElementById("info_block"));
		    //targets.push(document.getElementById("karta")); replaced with leaflet
			/*TODO: add city and postal_code as arguments below */
		    ajaxPostMaps("<?=site_url('workplaces/get_coords_lociq')?>",
			    argString,
			    targets);
	    });// "&city="+city+"&postal_code="+postal_code

	    var customer = document.querySelector("#customer");
	    var linkCreateCu = document.querySelector("#cc");
/*
	linkCreateCu.addEventListener("click", function(){
		console.log("Add customer clicked");
		selectInterval = initGetAjaxInterval('<?=site_url("customers/get_customers_for_dropdown/$company")?>', function(res){

			putJsonInSelect(customer, res, {0:"ingen"})}, 2000);

		customer.addEventListener("click", function(){
			clearInterval(selectInterval);
		});
	});
 */


	    //reload
	    var name = localStorage.getItem(name);
	    if (name !== null) document.querySelector("#name").value = name;
	    var street = localStorage.getItem(street);
	    if (street !== null) document.querySelector("#street").value = street;
	    var postal_code = localStorage.getItem(postal_code);
	    if (postal_code !== null) document.querySelector("#postal_code").value = postal_code;
	    var city = localStorage.getItem(city);
	    if (city !== null) document.querySelector("#city").value = city;
	    var stairs = localStorage.getItem(stairs);
	    if (stairs !== null) document.querySelector("#stairs").value = stairs;
	    var land = localStorage.getItem(land);
	    if (land !== null) document.querySelector("#land").value = land;
	    var lat = localStorage.getItem(lat);
	    if (lat !== null) document.querySelector("#lat").value = lat;
	    var lon = localStorage.getItem(lon);
	    if (lon !== null) document.querySelector("#lon").value = lon;


	    /*create customer with ajax*/
	    var cc = document.getElementById("cc");
	    cc.addEventListener("click", function(ev){
		    ev.preventDefault();
		    console.log("click");
		    var target = document.getElementById("formNewCustomer");
		    getFormCustomerToElem(target);
	    });


    }//window.onload


window.onbeforeunload = function() {
    localStorage.setItem(name, document.querySelector("#name").value);
    localStorage.setItem(street, document.querySelector("#street").value);
    localStorage.setItem(postal_code,document.querySelector("#postal_code").value);
    localStorage.setItem(city, document.querySelector("#city").value);
    localStorage.setItem(stairs, document.querySelector("#stairs").value);
    localStorage.setItem(land,document.querySelector("#land").value);
    localStorage.setItem(lat, document.querySelector("#lat").value);
    localStorage.setItem(lon, document.querySelector("#lon").value);
}

function getFormCustomerToElem(elem){
	console.log("getFormCustomerToElem");
	console.log(elem);
    var callb = function(data){
		elem.innerHTML = data;
	};
	getAjax("<?=site_url("customers/get_form_create")?>", callb);
}

/*to generate map with latit and longit, assuming map/div is #map1, and js is loaded*/
function prepareLeafletMap(lat, lon){

	console.log("prepareLeafletMap with " + lat + ", " + lon);

	//document.getElementById("map1").innerHTML = "<div id='map' style='width: 100%; height: 300px'></div>";

	if(typeof mymap !== "undefined"){
		mymap.off();
		mymap.remove();
	}
	mymap = L.map('map1').setView([lat, lon], 13);
	L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token={accessToken}', {
	attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
	maxZoom: 18,
	id: 'mapbox.streets',
	accessToken: 'pk.eyJ1IjoiYWJkdWxsYWh2YWx0ZXIiLCJhIjoiY2p3ZHYxajRpMTZuMjQ4bW9wdmxvcm90aCJ9.ZX_bUgp9AS7lQDl-PWyDHA'
	}).addTo(mymap);
	var marker = L.marker([lat,lon]).addTo(mymap);

}


</script>
