/*
 * $Id$ 
 */

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
	return true;
}