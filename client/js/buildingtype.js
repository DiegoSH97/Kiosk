function getBuildingType() {
	console.log('Getting buildingtype...');
	//create request
	var x = new XMLHttpRequest();
	//prepare request
	x.open('GET', 'http://localhost/kiosk/apis/buildingtype.php', true);
	//send request
	x.send();
	//handle readyState change event
	x.onreadystatechange = function() {
		// check status
		// status : 200=OK, 404=Page not found, 500=server denied access
		// readyState : 4=Back with data
		if (x.status == 200 && x.readyState == 4) {
			//show buildings
			showBuildingType(x.responseText);
		}
	}
}

function showBuildingType(data) {
	//buildingtype element
	var select = document.getElementById('seltype');
	//clear
	select.innerHTML = '';
	//parse to JSON
	var JSONdata = JSON.parse(data); 
	//get buildingType array
	var buildingtype =JSONdata.buildingtype; 
	//read buildings
	for(var i = 0; i < buildingtype.length; i++) {
		console.log(buildingtype[i]);
		//create option
		var option = document.createElement('option');
		//create id cell
		var valueDescription = document.createElement('value');
		valueDescription.innerHTML = buildingtype[i].id;		
		//add cells to row
		option.appendChild(valueDescription);
		//add row to table
		select.appendChild(option);
		
	}
}
