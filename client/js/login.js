//login
function login() {
	console.log('Getting token...');
	//create request
	var x = new XMLHttpRequest();
	//prepare request
	x.open('GET', urlApis + 'login.php', true);
	//request headers
	x.setRequestHeader('user', document.getElementById('user').value);
	x.setRequestHeader('password', document.getElementById('password').value);
	//send request
	x.send();
	//event handler
	x.onreadystatechange = function() {
		if (x.readyState == 4 && x.status == 200) {
			//parse to JSON
			var JSONdata = JSON.parse(x.responseText); console.log(JSONdata);
			//check status
			if (JSONdata.status == 0) {
				//save session info
				sessionStorage.authenticated = true;
				sessionStorage.userId = JSONdata.user.id;
				sessionStorage.userName = JSONdata.user.name;
				sessionStorage.token = JSONdata.token;
				//redirect to index
				window.location = 'index.html';
			}
			else
				document.getElementById('error').innerHTML = JSONdata.errorMessage;
		}
	}
	
}