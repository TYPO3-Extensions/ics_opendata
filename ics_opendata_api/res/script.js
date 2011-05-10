/**
 * Control of entry form
 * @return true/false
 */
function valid_form(){
	// Valid email address// /^[a-z0-9!#$%&'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+(?:[A-Z]{2,4})$/i
	var regex = new RegExp(/^[a-z0-9!#$%&'*+\/=?^_`{|}~-]+(\.[a-z0-9!#$%&'*+\/=?^_`{|}~-]+)*@([a-z0-9]([a-z0-9-]*[a-z0-9])?\.)+([A-Z]{2,4})$/i);
	if(document.getElementById('email').value == ""
		|| !regex.test(document.getElementById('email').value)){
		alert('Veuillez vérifier votre email.');
		return false;
	}
	// Valid pseudo
	if(document.getElementById('pseudo').value == ""){
		alert('Veuillez saisir le pseudo');
		return false;
	}
	// Valid password
	if( (document.getElementById('password').value == "")
		|| ((document.getElementById('password').value).length < 8)
		|| (document.getElementById('password').value != document.getElementById('password_confirm').value) ){
		alert('Veuillez vérifier votre mot de passe');
		return false;
	}
	// Valid CGU
	if( !document.getElementById('cgu').checked ){
		alert('Veuillez accepter les conditions d\'utilisation');
		return false;
	}

	return true;
}

/**
 * Control input form from add application
 * @return true/false
 */
function valid_application_form(){
	// Verifies that the name is filled
	if(document.getElementById('name').value == ""){
		alert('Veuillez saisir le nom.');
		return false;
	}
	// Verifies that the description is filled
	if(document.getElementById('description').value == ""){
		alert('Veuillez saisir la description.');
		return false;
	}
}