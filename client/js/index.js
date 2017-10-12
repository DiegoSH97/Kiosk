//initialize page
function init() {
	console.log('Initializing page...');
	//check authentication
	if (sessionStorage.authenticated) {
		//user info
		document.getElementById('username').innerHTML = sessionStorage.userName;
	}
	else{	
		//redirect to login
		window.location = 'login.html';
	}
}