function cellClicked(cell, url, urlJob){
	console.log("cellClicked med " + cell + " och " + url + " och " + urlJob);
	console.log(cell);
	showCellInfo(cell, url, urlJob);
}

function showCellInfo(cell, url, urlJob){


	var events = JSON.parse("[" + cell.dataset.jobs + "]");
	console.log(events);

	events.forEach(function(val, index){//array med id:n
		console.log("events: " + val + ", " + index);
		var param = "/"+val;

		getAjax(url+param, function(data){
			//console.log(data);
			var obj = JSON.parse(data);
			setTimeout(function(){
			showCellInfo2(cell, obj, ".event_info .e"+val, document.querySelector(".info_block"), urlJob, index);
			}, 50 * index);
		}); 
	});

}

//cell is a html-element, obj is the response of an ajax call, textTarget is where obj data is to be put
function showCellInfo2(cell, obj, textTargetSelector, targetBlock, urlJob, iteration){
	console.log("showCellInfo2 textTargetSelector: " + textTargetSelector);

	//if element not found:
	

	var text = "<p class='event_info'>" + cell.innerHTML + "</p>";//direkt-kopierat

	if(iteration==0){
		targetBlock.innerHTML += text;//test, was later
		//alert(text);
	}


	var len = obj.length;
	var text2 = "Anknuten personal ("+len+")<br>";//test
	console.log(len + " personal");
	
	for(var i=0; i<len; i++){
		text2 += "Namn: " + obj[i]["fornamn"] + " " + obj[i]["efternamn"] + "<br>";
	}

	var idjobs = cell.dataset.jobs;

	var jobsObj = JSON.parse("[" + idjobs + "]");//array med id på jobb
	//console.log("Parsed into obj");
	//console.log(jobsObj);

	//for(var i=0; i<jobsObj.length; i++){

	var jobId = btoa(jobsObj[iteration]);
	text2 += "<a href = '" + urlJob + jobId+"/yes' target='_blank'>Se mer om jobbet</a><br>";
	//}
	//activate info_block

	if(iteration==0){
		targetBlock.className += " showing";//trigger transition
	}

	//alert(text2);

	//printSlowly(text, textTarget, 5, function(){addCloseBtn(targetBlock, "showing", true, 2000);});
	//targetBlock.innerHTML = text;
	//alert(textTargetSelector);
	var textTarget = document.querySelector(textTargetSelector);

	//TODO: find out why sometimes null
	if(textTarget == null){
		console.log("Error, could not select " + textTargetSelector + ", please try again");
	}

	//nsole.log(textTarget);
	else{
		textTarget.innerHTML += text2;
	}
	if(iteration==0){
		addCloseBtn(targetBlock, "showing", true, function(){targetBlock.innerHTML=""});
	}


}

function printSlowly(text, domTarget, intervalMs, callback){
	console.log("printSlowly, " + text + ", domTarget:");
	console.log(domTarget);
	var targetText = "";
	var intr = setInterval(function(){
		targetText += text.charAt(targetText.length);

		domTarget.innerHTML = targetText;

		if(text == targetText){
			clearInterval(intr);
			callback();
		}
	}, intervalMs);

}

function addCloseBtn(elem, removeClass, useDiv, callback){
	console.log("addCloseBtn");
	console.log(elem);
	var btn = document.createElement("button");
	btn.innerHTML = "X";


	if(useDiv){
		var div = document.createElement("div");
		div.className = "closeBtnDiv";
		div.appendChild(btn);
		elem.appendChild(div);
	}
	else{
		elem.appendChild(btn);
	}

	if(removeClass != ""){
		btn.addEventListener("click", function(){
			var className = elem.className;

			if(className.indexOf(removeClass)>=0){
				elem.className = className.replace(removeClass, "");
				elem.className = elem.className.trim();
			}
			callback();
		});
	}


}
