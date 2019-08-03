data_target = null;//ska återanvändas för tilldelning av värden
global_interval = null;
(function() {

  'use strict';

  // click events
  //document.body.addEventListener('click', copy, true);

  // event handler
  function copy(e) {

    // find target element
    var
      t = e.target,
      c = t.dataset.copytarget,/*hämta data-set värde*/
      inp = (c ? document.querySelector(c) : null);

    // is element selectable?
    if (inp && inp.select) {

      // select text
      inp.select();

      try {
        // copy text
        document.execCommand('copy');
        inp.blur();
      }
      catch (err) {
        alert('please press Ctrl/Cmd+C to copy');
      }

    }

  }

})();

function kopiera_input(elem){
    
    if(elem && elem.select){
        elem.select();
    }
          try {
        // copy text
        document.execCommand('copy');
        element.blur();
      }
      catch (err) {
        alert('Tryck Ctrl/Cmd+C för att kopiera');
      }
}

function make_confirm(Message){
    return confirm(Message);
}

function makeLinksConfirmable(className){


    var Anchors = document.getElementsByTagName("a");

for (var i = 0; i < Anchors.length ; i++) {
    if (Anchors[i].className == className) {
        Anchors[i].addEventListener("click",
        function (event) {
            event.preventDefault();
            if (confirm('Säker?')) {
                window.location = this.href;
            }
        },
        false);
    }
}


/*

    var links = document.getElementsByClassName(className);

    var len = links.length;

    for(var i=0; i<len; i++){
        links[i].addEventListener(function () {
            make_confirm("Säker?");
        });
    }*/
}

function messageNonBlocking(timeMS, message, interval){
	var infoBlock = document.createElement("div");
	infoBlock.id = "JR_INFO_BLOCK";
	infoBlock.innerHTML = message;
	infoBlock.style = "position: fixed; bottom: 10%; height: 70px; border: 3px dotted gray; width: 50%; left: 24%; right: 24%; z-index: 400";
	document.body.appendChild(infoBlock);

	//shifting style
	global_interval = setInterval(function(){

		if(infoBlock.className == "variant"){
			//console.log(infoBlock.className);
			infoBlock.className = "";
		}
		else{
			//console.log(infoBlock.className);
			infoBlock.className = "variant";
		}
	}, interval);//700

	setTimeout(function(){
		if(infoBlock && infoBlock.parentElement){
			//console.log(infoBlock);
			infoBlock.parentElement.removeChild(infoBlock)
		}
	}, timeMS);
}

function closeMessage(){
	var inf = document.querySelector("#JR_INFO_BLOCK");
	//console.log("closeM");
	//console.log(inf);
	inf.parentElement.removeChild(inf);
	//console.log(global_interval);
	clearInterval(global_interval);
}

/*options for getCurrentPosition*/
var options = {
	enableHighAccuracy: true,
	timeout: 9990,
	maximumAge: 0
};

function getLocation() {
	//alert("getLocation");
	if (navigator.geolocation) {
		messageNonBlocking(10000, "Hämtar din position...", 700);
		navigator.geolocation.getCurrentPosition(showPosition, getPositionError, options);
		//alert("Position verkar fungera");
	} else { 
		data_target.innerHTML = "Geolocation is not supported by this browser.";
		//alert("Position fungerar ej");
	}
}



//for showing the coords
//using a global variable; data_target
function showPosition(position) {
	closeMessage();
	//alert("showPosition");
	if(Array.isArray(data_target)){//As set by sides script
		fillInValue(data_target[0], position.coords.latitude);
		fillInValue(data_target[1], position.coords.longitude);
		//alert("Fyllde i värden");
	}
	else{
		fillInValue(data_target, position.coords.latitude + ", " + position.coords.longitude);
		//alert("Fyllde i värden");
	}
}

function getPositionError(err){
	//alert("Error - kunde ej hämta position");
	closeMessage();
	messageNonBlocking(3000, "Kunde ej hämta din position", 1000);
	console.warn(`ERROR(${err.code}): ${err.message}`);
}

function fillInValue(targetElement, textValue){
	console.log("fillInValue " + textValue + " to " + targetElement);
	//console.log("typeof 'value': " + typeof targetElement.value);
	if(typeof targetElement.value == "undefined"){
		targetElement.innerHTML = textValue;
	}
	else{
		targetElement.value = textValue;
	}
}

function appendIFrameToElem(elem, url, replaceContent){
	var ifrm = document.createElement("iframe");
	ifrm.url = url;

	if(replaceContent){
		elem.innerHTML = "";
	}

	elem.appendChild(ifrm);
}

function setDivH(elem) {
    console.log("setDivH");
    var offset = 100;
    var md = elem;//document.getElementsByClassName("mainDiv")[0];
    console.log("md " + md);
    var _scrollHeight = document.documentElement.scrollHeight;//cprcrack stack overflow
    console.log("_scrollHeight " + _scrollHeight);
    //alert(_scrollHeight);
    md.style.height = _scrollHeight-offset + "px";
    console.log("md.style.height " + md.style.height);
    if (_scrollHeight < (window.innerHeight-offset)) {//var kommer marginal (ca 10px) ifrån?
        md.style.height = (window.innerHeight-offset) + "px";
        console.log("md.style.height " + md.style.height);
    }
}
/*to swap order of first two forms within div (container)*/
function swap_2_forms(child){
	var container = child.parentNode;

	console.log("swap_2_forms med child" + child + " (parent: " + container + ")");


	if(container.children.length < 2){
		console.log("To few child nodes");
		return false;
	}

	var child0 = container.children[0];
	var child1 = container.children[1];


	if(child0.tagName.toLowerCase() != "form"){ return false; }
	if(child1.tagName.toLowerCase() != "form"){ return false; }
	container.insertBefore(child1, child0);
	return true;
}

window.mobilecheck = function() {
	var check = false;
	(function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);
	return check;
};

function convertTableToDivs(){

	var tbl = document.getElementsByTagName("table")[0];
	var tableDiv = document.createElement("div");
	if(tbl.tBodies.length > 0){
		tbl = tbl.tBodies[0];
	}
	else{
	}

	var nrRows = tbl.rows.length;
	var rows = tbl.rows;

	for(var i=0; i<nrRows; i++){
		var rowDiv = document.createElement("div");
		rowDiv.className = "rowDiv";

		var nrCols = rows[i].cells.length;
		for(var j=0; j<nrCols; j++){
			var tdDiv = document.createElement("div");
			tdDiv.className = "tdDiv";
			tdDiv.innerHTML = rows[i].cells[j].innerHTML;

			rowDiv.appendChild(tdDiv);
		}
		tableDiv.appendChild(rowDiv);
	}
	document.body.insertBefore(tableDiv, tbl);
	tbl.parentElement.removeChild(tbl);

}

/*url - of internal service*/
/*params - string*/
/*targets - array of dom-elements*/
/*result ex. {lat:1, lon:2, "<iframe>"}*/
/*hämtar koordinater, karta, meddelande*/
/*Anpassad för eniro kartor*/
/*Efter körning ska koordinater finnas (?)*/
function ajaxPostMaps(url,params,target){
	console.log("ajaxPost med url " + url + ", params " + params + ", target " + target);
	var xhttp = new XMLHttpRequest();
	xhttp.open("POST", url, true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

	xhttp.onreadystatechange = function () {
		if (xhttp.readyState == 4) {
			var fetchedLat = 0, fetchedLon = 0;
			var coordCount = 0;

			console.log(xhttp.responseText);
			if (target != null && !Array.isArray(target)) {

				fillInValue(target, xhttp.responseText);
			}
			else if (Array.isArray(target)) {

				try{
					var obj = JSON.parse(xhttp.responseText);
					var keys = Object.keys(obj); //make obj from response
					var gotAltern = false;
					keys.forEach(function (item, index) { // go through key by key "item"
						console.log(obj[item]);
						if(item == "message"){
							messageNonBlocking(4500, obj[item], 700);
						}
						else if(item == "karta"){
							document.querySelector("#karta").innerHTML = obj[item];
						}
						else if(item == "alternatives"){
							fillInValue(target[2], obj[item]);
							gotAltern = true;
						}
						else{
							fillInValue(target[index], obj[item]);
							if(item=="lat"){
								fetchedLat = obj[item];
								coordCount++;
							}
							if(item=="lon"){
								fetchedLon = obj[item];
								coordCount++;
							}
						}
					});
					if(!gotAltern){
						fillInValue(target[2], "");//make empty
					}
				}
				catch(error){
					messageNonBlocking(3333, "Kunde ej bearbeta, " + xhttp.responseText, 700);
				}
			}

			if(coordCount == 2){
				console.log("Got coords (?)");
				//make other map
				prepareLeafletMap(fetchedLat, fetchedLon);
			}
		}
	}
	xhttp.send(params);
}

function postAjax(url, data, success) {
	console.log("postAjax");
	var params = typeof data == 'string' ? data : Object.keys(data).map(
		function(k){ return encodeURIComponent(k) + '=' + encodeURIComponent(data[k]) }
	).join('&');

	console.log(params);

	var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
	xhr.open('POST', url, true);
	xhr.onreadystatechange = function() {
		if (xhr.readyState>3 && xhr.status==200) {
			console.log("postAjax succeeded");
			success(xhr.responseText); }
	};
	/*xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');*/
	xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	xhr.send(params);
	return xhr;
}

function getAjax(url, success) {
	console.log("getAjax med " + url + " och " + success);
	var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
	xhr.open('GET', url);
	xhr.onreadystatechange = function() {
		if (xhr.readyState>3 && xhr.status==200) success(xhr.responseText);
	};
	xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
	xhr.send();
	return xhr;
}

function initGetAjaxInterval(url, success, interval){
	console.log("initGetAjaxInterval med url " + url);
	var interv = setInterval(
		function(){
			var xhr = getAjax(url, success);

		},
		interval
	)
	return interv;
}

/*form - dom form WITH action as a url */
function formPostAjax(form, url2){
	console.log("formPostAjax");
	console.log(form);
	var url = form.action;
	console.log(url);
	var inputs = form.getElementsByTagName("input");
	console.log(inputs);

	var len = inputs.length;
	var o = {};//to contain form data
	for(var i=0; i<len; i++){
		var n = inputs[i].name;
		if(n==""){}
		else{
			var v = inputs[i].value;
			var t = inputs[i].type;
			if(t !== "submit"){
				var obj = {};
				obj[n] = v;
				o[n]=v;
			}
		}


	}//todo: handle chekboxes, radio, textarea ...
	//var dataUrl = jsonToURLci(o); for get ci
	//console.log(dataUrl);

	var select = document.getElementById("customer");
	console.log("Got a select elem: " + select);
	var callback = function(data){
		console.log("A function that takes data: " + data);//fungerar, fick id av ny kund

		/*
		var succ = function(d){
			console.log("En funktion som fick d: " + d + " och använder " + select + " och data: " + data);
			putJsonInSelect(select, d);
			select.selectedIndex = data;
		};
		*/
		//console.log("Will call getAjax with " + url2 + " and " + succ);

		//getAjax(url2, succ);
		//select the returned id
		//move this funct to caller


		//add to dropdown
		if(addJsonToSelect(select, data, true)){
			messageNonBlocking(2000,"Din nya kund är vald",700);
			form.parentElement.removeChild(form);
			select.focus();
		}
	}
	//url += dataUrl;
	console.log("Will call postAjax with url " + url + ", " + o + " and 'callback'");
	//getAjax(url, callback);
	postAjax(url,o,callback);
}

/*data should be object*/
function jsonToURL(data){
	url = Object.keys(data).map(function(k) {
		    return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
	}).join('&');
	/*from https://stackoverflow.com/questions/14525178/is-there-any-native-function-to-convert-json-to-url-parameters */
	console.log(url);
	return url;
}

function jsonToURLci(data){
	url = Object.keys(data).map(function(k) {
		return encodeURIComponent(data[k])

	}).join('/');
	/*from https://stackoverflow.com/questions/14525178/is-there-any-native-function-to-convert-json-to-url-parameters */
	console.log(url);
	return url;
}

/*
Takes data for a complete select element
select - a dom element
 * json - data as json
 * firstRow - object with data for a first row */
function putJsonInSelect(select, json, firstRow){
	console.log("putJsonInSelect");
	console.log(json);
	var data = JSON.parse(json);
	//should be array of key-value pairs
	

	var len = data.length;

	if(len == 0){ return false;}
	else{ console.log("found " + len);}

	select.disabled = true;
	var oldId = select.options[select.selectedIndex].value;

	while(select.options.length > 0){
		select.remove(0);
	}

	if(firstRow){
		var opt = document.createElement("option");
		opt.value = Object.keys(firstRow)[0];
		opt.innerHTML = Object.values(firstRow)[0];
		select.appendChild(opt);
	}

	var foundSelected = false;
	
	for(var i=0;i<len;i++){
		var opt = document.createElement("option");
		var d = data[i];
		var k = Object.keys(d)[0];
		var v = Object.values(d)[0];
		opt.value = k;
		opt.innerHTML = v;
		select.appendChild(opt);

		if(k==oldId){
			foundSelected = i;
		}
	}
	if(foundSelected){
		select.selectedIndex = foundSelected;
		if(firstRow){
			select.selectedIndex++;
		}
	}

	select.disabled = false;

	return i;
}

/*
* Add to end of html select
* select - a dom element
* json - expecting [{"id": 123, "name": "abc"}]
* */
function addJsonToSelect(select, json, alsoSelect){
	console.log("addJsonToSelect med");
	console.log(select);
	console.log(json);
	var d=0;
	try{
		d = JSON.parse(json);
	}
	catch (e) {
		alert(e);
		return false;
	}

	console.log(d);
	var newOpt = document.createElement("option");
	newOpt.value = d[0].id;
	newOpt.text = d[0].name;
	select.add(newOpt);
	if(alsoSelect){
		newOpt.selected = true;
	}
	return true;
}


function getWideTds(tbl, limitPx){

	/*tbodies*/
	var tbody = tbl.tBodies[0];

	rLen = tbody.rows.length;
	console.log(rLen);

	if(rLen==0){
		return false;
	}

	var found = [];

	var rows = tbody.rows;
	for(var i=0; i<rLen; i++){

		var cells = rows[i].childNodes;

		var c = getWideTds2(cells, limitPx);
		if(c){
			c.forEach(function(elem){
				found.push(elem);
			});
		}
	}

	console.log(found);
	return found;
}

function getWideTds2(cells, limitPx){

	var len=cells.length;

	var found = [];


	for (var i=0;i<len;i++){
		var cell = cells[i];

		var wid = cell.offsetWidth;
		if(wid > limitPx){
			if(cell.innerHTML !== ""){
				console.log(cell.innerHTML);
				found.push(cell);
			}
		}
	}

	if(found.length>0){
		return found;
	}
	else return false;
}

function setElementInDiv(elm, style){

	var parent = elm.parentNode;

	var newDiv = document.createElement("div");
	newDiv.style = style;


	parent.insertBefore(newDiv, elm);
	elm.parentNode.removeChild(elm);
	newDiv.appendChild(elm);
}

/*Insert div with supplied style added, between elm and its content*/
function insertDivBetween(elm, style){
	var newDiv = document.createElement("div");
	newDiv.style = style;
	newDiv.innerHTML = elm.innerHTML;
	elm.innerHTML = "";
	elm.appendChild(newDiv);

}
