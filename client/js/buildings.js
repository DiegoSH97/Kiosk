function getBuildings() {
	console.log('Getting buildings...');
	//create request
	var x = new XMLHttpRequest();
	//prepare request
	x.open('GET', 'http://localhost/kiosk/apis/buildings.php', true);
	//send request
	x.send();
	//handle readyState change event
	x.onreadystatechange = function() {
		// check status
		// status : 200=OK, 404=Page not found, 500=server denied access
		// readyState : 4=Back with data
		if (x.status == 200 && x.readyState == 4) {
			//show buildings
			showBuildings(x.responseText);
		}
	}
}

function showBuildings(data) {
	//buildings element
	var table = document.getElementById('buildings');
	//clear
	table.innerHTML = '';
	//parse to JSON
	var JSONdata = JSON.parse(data); 
	//get buildings array
	var buildings =JSONdata.buildings; 
	//read buildings
	for(var i = 0; i < buildings.length; i++) {
		console.log(buildings[i]);
		//create row
		var row = document.createElement('tr');
		//create id cell
		var cellId = document.createElement('td');
		cellId.innerHTML = buildings[i].id;
		//create name cell
		var cellName = document.createElement('td');
		cellName.innerHTML = buildings[i].name;
		//create location cell
		var cellLocation = document.createElement('td');
		cellLocation.innerHTML = buildings[i].location.latitude + ', ' + buildings[i].location.longitude;
		//create location cell
		var cellType = document.createElement('td');
		cellType.innerHTML = buildings[i].type.id + ' : ' + buildings[i].type.description;
		
		//add cells to row
		row.appendChild(cellId);
		row.appendChild(cellName);
		row.appendChild(cellLocation);
		row.appendChild(cellType);
		//add row to table
		table.appendChild(row);
		
	}
	
}

